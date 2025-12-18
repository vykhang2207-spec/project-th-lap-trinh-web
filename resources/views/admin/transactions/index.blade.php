<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Quản lý Dòng tiền') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Thong ke tong quan --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                {{-- Tong doanh thu GMV --}}
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-l-4 border-blue-500">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Tổng Doanh Thu (GMV)</div>
                    <div class="text-2xl font-bold text-gray-800 dark:text-white">{{ number_format($totalRevenue) }} đ</div>
                </div>

                {{-- Loi nhuan cua Admin --}}
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-l-4 border-green-500">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Lợi Nhuận Sàn</div>
                    <div class="text-2xl font-bold text-green-600">{{ number_format($totalAdminProfit) }} đ</div>
                    <span class="text-xs text-gray-400">Doanh thu thực tế</span>
                </div>

                {{-- Tien thue giu ho --}}
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-l-4 border-red-500">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Thuế Giữ Hộ</div>
                    <div class="text-2xl font-bold text-red-600">{{ number_format($totalTax) }} đ</div>
                    <span class="text-xs text-gray-400">Cần nộp Nhà nước</span>
                </div>

                {{-- Tien no giao vien chua tra --}}
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-l-4 border-orange-500 relative">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Phải Trả GV</div>
                    <div class="text-2xl font-bold text-orange-500">{{ number_format($pendingPayouts ?? 0) }} đ</div>

                    {{-- Link den trang quyet toan --}}
                    <a href="{{ route('admin.payouts.index') }}" class="absolute bottom-4 right-4 text-xs bg-indigo-100 text-indigo-700 px-2 py-1 rounded hover:bg-indigo-200">
                        Quyết toán ngay →
                    </a>
                </div>
            </div>

            {{-- Bang lich su giao dich --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-bold text-lg text-gray-900 dark:text-white">Nhật ký Giao dịch</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ngày giờ</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Khóa học</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Tổng tiền</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-green-500 uppercase">Sàn nhận</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-blue-500 uppercase">GV nhận</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($transactions as $t)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $t->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $t->course->title ?? 'Khóa học đã xóa' }}</div>
                                    <div class="text-xs text-gray-500">GV: {{ $t->course->teacher->name ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-gray-800 dark:text-gray-200">
                                    {{ number_format($t->total_amount) }}
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-green-600">
                                    +{{ number_format($t->admin_fee) }}
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-blue-600">
                                    {{ number_format($t->teacher_earning) }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    {{-- Hien thi trang thai tra luong --}}
                                    @if($t->payout_status == 'completed')
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 border border-green-200">Đã trả</span>
                                    @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200">Chờ trả</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">Chưa có giao dịch nào.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t dark:border-gray-700">
                    {{ $transactions->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
