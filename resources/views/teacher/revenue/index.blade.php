<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Quản lý Tài chính & Rút tiền') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- PHẦN 1: THỐNG KÊ TỔNG QUAN (3 Card) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md border-l-4 border-indigo-500">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Số dư khả dụng (Ví)</div>
                    <div class="mt-2 flex items-baseline">
                        <span class="text-4xl font-extrabold text-indigo-600">
                            {{ number_format($currentBalance) }}
                        </span>
                        <span class="ml-2 text-gray-500 font-medium">VNĐ</span>
                    </div>
                    <div class="mt-1 text-xs text-gray-400">Số tiền hiện có thể rút</div>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md border-l-4 border-green-500">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Doanh thu</div>
                    <div class="mt-2 text-3xl font-bold text-green-600">
                        {{ number_format($totalEarned) }} đ
                    </div>
                    <div class="mt-1 text-xs text-gray-400">Tổng doanh thu thực nhận</div>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md border-l-4 border-orange-500">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Đã rút về ngân hàng</div>
                    <div class="mt-2 text-3xl font-bold text-orange-500">
                        {{ number_format($totalWithdrawn) }} đ
                    </div>
                </div>
            </div>

            {{-- PHẦN 2: FORM RÚT TIỀN (Chỉ hiện nếu có tiền > 50k) --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                        <span>💸</span> Yêu cầu Rút tiền
                    </h3>

                </div>

                {{-- Thông báo --}}
                @if(session('success'))
                <div class="mb-4 text-green-700 bg-green-100 p-3 rounded border border-green-400 flex items-center gap-2">
                    ✅ {{ session('success') }}
                </div>
                @endif
                @if(session('info'))
                <div class="mb-4 text-blue-700 bg-blue-100 p-3 rounded border border-blue-400 flex items-center gap-2">
                    ℹ️ {{ session('info') }}
                </div>
                @endif
                @if(session('error'))
                <div class="mb-4 text-red-700 bg-red-100 p-3 rounded border border-red-400 flex items-center gap-2">
                    ⚠️ {{ session('error') }}
                </div>
                @endif

                <form action="{{ route('teacher.withdraw.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Số tiền muốn rút</label>
                        <div class="relative rounded-md shadow-sm">
                            <input type="number" name="amount" min="50000" max="{{ $currentBalance }}" class="block w-full rounded-md border-gray-300 pl-3 pr-12 focus:border-indigo-500 focus:ring-indigo-500 py-2" placeholder="Tối thiểu 50.000" required>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <span class="text-gray-500 sm:text-sm">VND</span>
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Tối đa: {{ number_format($currentBalance) }} đ</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ngân hàng</label>
                            <select name="bank_name" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2">
                                <option value="Vietcombank">Vietcombank</option>
                                <option value="Techcombank">Techcombank</option>
                                <option value="MBBank">MB Bank</option>
                                <option value="Momo">Ví MoMo</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Chủ tài khoản</label>
                            <input type="text" name="bank_account_name" value="{{ Auth::user()->name }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2" required>
                        </div>
                    </div>

                    <div class="md:col-span-2 flex items-end gap-4">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Số tài khoản / SĐT Ví</label>
                            <input type="text" name="bank_account_number" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2" required>
                        </div>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-md shadow transition transform hover:-translate-y-0.5">
                            Gửi Yêu Cầu
                        </button>
                    </div>
                </form>
            </div>

            {{-- PHẦN 3: LỊCH SỬ GIAO DỊCH (2 Bảng song song) --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                        <h3 class="font-bold text-gray-700 dark:text-gray-200">📈 Lịch sử Bán khóa học</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($transactions as $t)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-3 text-sm">
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $t->course->title ?? 'Khóa học đã xóa' }}</div>
                                        <div class="text-xs text-gray-500">{{ $t->created_at->format('d/m/Y H:i') }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right font-bold text-green-600">
                                        +{{ number_format($t->teacher_earning) }} đ
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="px-4 py-8 text-center text-sm text-gray-500">Chưa có giao dịch nào</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-2 border-t dark:border-gray-700">
                        {{ $transactions->appends(['withdraw_page' => $withdrawals->currentPage()])->links() }}
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                        <h3 class="font-bold text-gray-700 dark:text-gray-200">📉 Lịch sử Rút tiền</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($withdrawals as $w)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-3 text-sm">
                                        <div class="font-medium text-gray-900 dark:text-white">
                                            Rút về {{ $w->bank_name }}
                                        </div>
                                        <div class="text-xs text-gray-500">{{ $w->created_at->format('d/m/Y H:i') }}</div>

                                        {{-- Trạng thái --}}
                                        <div class="mt-1">
                                            @if($w->status === 'approved')
                                            <span class="px-2 py-0.5 text-xs rounded bg-green-100 text-green-800">Thành công</span>
                                            @elseif($w->status === 'pending')
                                            <span class="px-2 py-0.5 text-xs rounded bg-yellow-100 text-yellow-800">Chờ duyệt</span>
                                            @else
                                            <span class="px-2 py-0.5 text-xs rounded bg-red-100 text-red-800">Từ chối</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right font-bold text-red-500">
                                        -{{ number_format($w->amount) }} đ
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="px-4 py-8 text-center text-sm text-gray-500">Chưa có lần rút tiền nào</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-2 border-t dark:border-gray-700">
                        {{ $withdrawals->appends(['trans_page' => $transactions->currentPage()])->links() }}
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
