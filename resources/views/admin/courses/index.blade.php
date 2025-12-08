<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Admin: Danh sách Khóa học') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Khóa học</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Giáo viên</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Giá</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($courses as $course)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold">{{ $course->title }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $course->teacher->name }} <br>
                                    <span class="text-xs">{{ $course->teacher->email }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm">{{ number_format($course->price) }} đ</td>
                                <td class="px-6 py-4">
                                    @if($course->is_approved)
                                    <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Đã duyệt</span>
                                    @else
                                    <span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full">Chờ duyệt</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-3">

                                        {{-- Link xem chi tiết (Mở tab mới cho tiện) --}}
                                        <a href="{{ route('course.show', $course->id) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900" title="Xem nội dung">
                                            👁️ Xem
                                        </a>

                                        @if(!$course->is_approved)
                                        {{-- Nút Duyệt (Chỉ hiện khi chưa duyệt) --}}
                                        <form action="{{ route('admin.courses.approve', $course->id) }}" method="POST" onsubmit="return confirm('Bạn chắc chắn muốn duyệt khóa học này để bán công khai?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-green-600 hover:text-green-900 font-bold">
                                                ✅ Duyệt ngay
                                            </button>
                                        </form>
                                        @else
                                        <span class="text-gray-400 cursor-not-allowed">Đã duyệt</span>
                                        @endif

                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $courses->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
