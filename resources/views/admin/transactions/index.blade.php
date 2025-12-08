<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Quản lý Dòng tiền & Doanh thu') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-l-4 border-blue-500">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Tổng giá trị giao dịch (GMV)</div>
                    <div class="text-2xl font-bold text-gray-800 dark:text-white">{{ number_format($totalRevenue ?? 0) }} đ</div>
                    <span class="text-xs text-gray-400">Tổng tiền khách đã thanh toán</span>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-l-4 border-red-500">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Thuế phải nộp (10%)</div>
                    <div class="text-2xl font-bold text-red-600">{{ number_format($totalTax ?? 0) }} đ</div>
                    <span class="text-xs text-gray-400">Nghĩa vụ thuế với nhà nước</span>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-l-4 border-green-500">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Doanh thu Sàn (Admin Fee)</div>
                    <div class="text-3xl font-bold text-green-600">{{ number_format($totalAdminProfit ?? 0) }} đ</div>
                    <span class="text-xs text-gray-400">Lợi nhuận thực tế của sàn</span>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-bold mb-4">Lịch sử giao dịch chi tiết</h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ngày</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Thông tin</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Khách trả</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Thuế (10%)</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-green-500 uppercase tracking-wider">Sàn nhận</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-blue-500 uppercase tracking-wider">GV nhận</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                @forelse($transactions as $t)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $t->created_at->format('d/m/Y') }} <br>
                                        <span class="text-xs">{{ $t->created_at->format('H:i') }}</span>
                                    </td>

                                    <td class="px-4 py-4">
                                        {{-- Kiểm tra null để tránh lỗi nếu khóa học bị xóa --}}
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $t->course->title ?? 'Khóa học đã bị xóa' }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            GV: {{ $t->course->teacher->name ?? 'N/A' }}
                                        </div>
                                        <div class="text-xs text-gray-400 mt-1">
                                            HV: {{ $t->user->email ?? 'User ẩn' }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-4 text-right text-sm font-bold text-gray-900 dark:text-gray-200">
                                        {{ number_format($t->total_amount) }}
                                    </td>

                                    <td class="px-4 py-4 text-right text-sm text-red-500">
                                        {{ number_format($t->tax_amount) }}
                                    </td>

                                    <td class="px-4 py-4 text-right text-sm font-bold text-green-600">
                                        +{{ number_format($t->admin_fee) }}
                                    </td>

                                    <td class="px-4 py-4 text-right text-sm font-bold text-blue-600">
                                        {{ number_format($t->teacher_earning) }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        Chưa có giao dịch nào được ghi nhận.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $transactions->links() }}
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
