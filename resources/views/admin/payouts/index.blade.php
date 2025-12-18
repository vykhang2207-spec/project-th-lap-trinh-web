<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Quyết toán Lương Giảng viên') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Thong bao thanh cong hoac loi --}}
            @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">✅ {{ session('success') }}</div>
            @endif
            @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">⚠️ {{ session('error') }}</div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-bold mb-4">Danh sách Giảng viên cần thanh toán</h3>

                    {{-- Kiem tra neu danh sach trong --}}
                    @if($teachers->isEmpty())
                    <p class="text-gray-500 text-center py-4">Hiện không có giáo viên nào có số dư cần trả.</p>
                    @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Giảng viên</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thông tin Ngân hàng</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Số dư chờ trả</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Hành động</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($teachers as $teacher)
                                <tr>
                                    {{-- Thong tin giao vien --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-bold">{{ $teacher->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $teacher->email }}</div>
                                    </td>

                                    {{-- Thong tin ngan hang --}}
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        @if($teacher->bank_account_number)
                                        <div class="font-bold">{{ $teacher->bank_name }}</div>
                                        <div>{{ $teacher->bank_account_number }}</div>
                                        <div class="uppercase text-xs">{{ $teacher->bank_account_name }}</div>
                                        @else
                                        <span class="text-red-500 italic">Chưa cập nhật</span>
                                        @endif
                                    </td>

                                    {{-- So tien cho tra --}}
                                    <td class="px-6 py-4 text-right font-bold text-indigo-600 text-lg">
                                        {{ number_format($teacher->pending_amount) }} đ
                                    </td>

                                    {{-- Nut xac nhan da chuyen khoan --}}
                                    <td class="px-6 py-4 text-center">
                                        <form action="{{ route('admin.payouts.store') }}" method="POST" onsubmit="return confirm('Xác nhận đã chuyển khoản {{ number_format($teacher->pending_amount) }}đ cho GV này?');">
                                            @csrf
                                            <input type="hidden" name="teacher_id" value="{{ $teacher->id }}">
                                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded shadow">
                                                Đã chuyển khoản
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
