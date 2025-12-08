<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Quản lý Khóa học của tôi') }} ({{ Auth::user()->courses()->count() }})
            </h2>
            <a href="{{ route('teacher.courses.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                + Đăng Khóa học mới
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        {{ session('success') }}
                    </div>
                    @endif

                    @if($courses->isEmpty())
                    <div class="text-center py-10">
                        <p class="text-gray-500 mb-4">Bạn chưa đăng khóa học nào.</p>
                    </div>
                    @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-1/4">Tên Khóa học</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Giá</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Trạng thái</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ngày tạo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Hành động</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($courses as $course)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200">
                                        {{ $course->title }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ number_format($course->price) }} đ
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($course->is_approved)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Đã duyệt</span>
                                        @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Chờ duyệt</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $course->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-3">

                                            {{-- Nút QUẢN LÝ NỘI DUNG --}}
                                            <a href="{{ route('teacher.courses.content.index', $course) }}" class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300 font-semibold text-xs py-1 px-2 border border-green-200 dark:border-green-800 rounded-md hover:border-green-600 transition" title="Quản lý Chương & Bài học">
                                                Nội dung
                                            </a>

                                            <span class="text-gray-300 dark:text-gray-600 text-xs">|</span>
                                            <a href="{{ route('teacher.courses.show', $course->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-4 font-bold" title="Xem chi tiết">
                                                👁️ Xem
                                            </a>
                                            {{-- Nút Sửa (Icon) --}}
                                            <a href="{{ route('teacher.courses.edit', $course) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200 p-1 rounded-md transition duration-150 hover:bg-gray-100 dark:hover:bg-gray-700" title="Sửa khóa học">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>

                                            {{-- Nút Xóa (Icon) --}}
                                            <form method="POST" action="{{ route('teacher.courses.destroy', $course) }}" onsubmit="return confirm('Bạn có chắc muốn xóa khóa học này?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200 p-1 rounded-md transition duration-150 hover:bg-gray-100 dark:hover:bg-gray-700" title="Xóa khóa học">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.86 12.14A2 2 0 0116.14 21H7.86A2 2 0 015.86 19.14L5 7m4 4v6m6-6v6m-4-10h4m-8 0h4m-4 0a2 2 0 01-2-2V5a2 2 0 012-2h4a2 2 0 012 2v2a2 2 0 01-2 2H9z"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $courses->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
