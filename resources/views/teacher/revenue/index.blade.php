<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Quản lý Doanh thu & Lương') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- PHẦN 1: THỐNG KÊ (3 CARD) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- Card 1: Chờ thanh toán --}}
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md border-l-4 border-yellow-500">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Doanh thu chờ quyết toán</div>
                    <div class="mt-2 flex items-baseline">
                        <span class="text-4xl font-extrabold text-yellow-600">
                            {{ number_format($pendingBalance) }}
                        </span>
                        <span class="ml-2 text-gray-500 font-medium">VNĐ</span>
                    </div>
                    <div class="mt-1 text-xs text-gray-400">Admin sẽ chuyển khoản vào cuối tháng</div>
                </div>

                {{-- Card 2: Tổng thu nhập --}}
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md border-l-4 border-blue-500">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Tổng thu nhập trọn đời</div>
                    <div class="mt-2 text-3xl font-bold text-blue-600">
                        {{ number_format($lifetimeEarnings) }} đ
                    </div>
                    <div class="mt-1 text-xs text-gray-400">Tổng giá trị bạn đã kiếm được</div>
                </div>

                {{-- Card 3: Đã nhận --}}
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md border-l-4 border-green-500">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Đã thực nhận</div>
                    <div class="mt-2 text-3xl font-bold text-green-600">
                        {{ number_format($totalPaid) }} đ
                    </div>
                    <div class="mt-1 text-xs text-gray-400">Tổng tiền đã về tài khoản ngân hàng</div>
                </div>
            </div>

            {{-- PHẦN 2: THÔNG TIN NGÂN HÀNG (Hiển thị để GV biết) --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                        <span>🏦</span> Thông tin nhận lương
                    </h3>
                    <a href="{{ route('profile.edit') }}" class="text-sm text-indigo-600 hover:underline">Cập nhật thông tin →</a>
                </div>

                @if(Auth::user()->bank_account_number)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded border dark:border-gray-600">
                        <span class="block text-gray-500 text-xs uppercase">Ngân hàng</span>
                        <span class="font-bold text-gray-800 dark:text-white">{{ Auth::user()->bank_name }}</span>
                    </div>
                    <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded border dark:border-gray-600">
                        <span class="block text-gray-500 text-xs uppercase">Số tài khoản</span>
                        <span class="font-bold text-gray-800 dark:text-white">{{ Auth::user()->bank_account_number }}</span>
                    </div>
                    <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded border dark:border-gray-600">
                        <span class="block text-gray-500 text-xs uppercase">Chủ tài khoản</span>
                        <span class="font-bold text-gray-800 dark:text-white">{{ Auth::user()->bank_account_name }}</span>
                    </div>
                </div>
                @else
                <div class="p-4 bg-red-100 text-red-700 rounded border border-red-300 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <span>Bạn chưa cập nhật thông tin ngân hàng. Admin sẽ không thể chuyển khoản cho bạn!</span>
                </div>
                @endif
            </div>

            {{-- PHẦN 3: HAI BẢNG LỊCH SỬ --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- BẢNG 1: LỊCH SỬ BÁN KHÓA HỌC (Thu nhập) --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                        <h3 class="font-bold text-gray-700 dark:text-gray-200">📈 Nhật ký Bán hàng</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($transactions as $t)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                    <td class="px-4 py-3 text-sm">
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $t->course->title ?? 'Khóa học đã xóa' }}</div>
                                        <div class="text-xs text-gray-500">{{ $t->created_at->format('d/m/Y H:i') }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="text-sm font-bold text-green-600">+{{ number_format($t->teacher_earning) }} đ</div>
                                        @if($t->payout_status == 'completed')
                                        <span class="text-[10px] bg-green-100 text-green-800 px-1.5 py-0.5 rounded">Đã nhận</span>
                                        @else
                                        <span class="text-[10px] bg-yellow-100 text-yellow-800 px-1.5 py-0.5 rounded">Chờ trả</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="px-4 py-8 text-center text-gray-500 text-sm">Chưa bán được khóa học nào.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-2 border-t dark:border-gray-700">
                        {{ $transactions->appends(['payout_page' => $payouts->currentPage()])->links() }}
                    </div>
                </div>

                {{-- BẢNG 2: LỊCH SỬ NHẬN LƯƠNG (Payouts) --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                        <h3 class="font-bold text-gray-700 dark:text-gray-200">💰 Lịch sử Nhận lương</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($payouts as $p)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-3 text-sm">
                                        <div class="font-medium text-gray-900 dark:text-white">Quyết toán lương</div>
                                        <div class="text-xs text-gray-500">{{ $p->created_at->format('d/m/Y H:i') }}</div>
                                        <div class="text-xs text-gray-400 italic mt-0.5">{{ $p->note }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="text-sm font-bold text-blue-600">+{{ number_format($p->amount) }} đ</div>
                                        <span class="text-[10px] bg-blue-100 text-blue-800 px-1.5 py-0.5 rounded">Thành công</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="px-4 py-8 text-center text-gray-500 text-sm">Chưa có đợt nhận lương nào.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-2 border-t dark:border-gray-700">
                        {{ $payouts->appends(['trans_page' => $transactions->currentPage()])->links() }}
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
