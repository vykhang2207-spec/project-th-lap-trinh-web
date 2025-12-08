<x-app-layout>
    {{-- PHẦN HEADER (Tiêu đề trang) --}}
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Trang chủ - Khóa học nổi bật') }}
            </h2>

            {{-- Nút CTA nhỏ ở header (Tùy chọn) --}}
            @guest
            <div class="hidden sm:block text-sm text-gray-500">
                Đăng ký ngay để bắt đầu học tập!
            </div>
            @endguest
        </div>
    </x-slot>

    {{-- PHẦN NỘI DUNG CHÍNH --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Banner quảng cáo (Tùy chọn - để nhìn cho nguy hiểm) --}}
            <div class="mb-8 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg shadow-lg p-8 text-white">
                <h1 class="text-4xl font-bold mb-2">Học Lập Trình Từ Con Số 0</h1>
                <p class="text-lg opacity-90">Khám phá các khóa học thực chiến từ cơ bản đến nâng cao.</p>
            </div>

            {{-- Danh sách khóa học --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if ($courses->isEmpty())
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Chưa có khóa học</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Hiện chưa có khóa học nào được duyệt hiển thị.</p>
                    </div>
                    @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @foreach ($courses as $course)
                        <div class="group bg-white dark:bg-gray-900 border dark:border-gray-700 rounded-lg overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 flex flex-col h-full">

                            {{-- Ảnh khóa học --}}
                            <a href="{{ route('course.show', $course) }}" class="relative overflow-hidden">
                                <img src="{{ Str::startsWith($course->image_path, 'http') ? $course->image_path : asset('storage/' . $course->image_path) }}" alt="{{ $course->title }}" class="w-full h-48 object-cover group-hover:scale-110 transition duration-500" onerror="this.onerror=null;this.src='https://via.placeholder.com/300x160?text=Course+Image';">

                                {{-- Badge Giá tiền --}}
                                <div class="absolute top-2 right-2 bg-red-600 text-white text-xs font-bold px-2 py-1 rounded shadow">
                                    {{ number_format($course->price) }} đ
                                </div>
                            </a>

                            <div class="p-4 flex flex-col flex-1">
                                {{-- Tên khóa học --}}
                                <h2 class="text-lg font-bold mb-2 line-clamp-2 min-h-[3.5rem] group-hover:text-indigo-600 transition">
                                    <a href="{{ route('course.show', $course) }}">
                                        {{ $course->title }}
                                    </a>
                                </h2>

                                {{-- Thông tin giảng viên --}}
                                <div class="flex items-center mb-4">
                                    <div class="w-6 h-6 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-xs font-bold mr-2 text-gray-600 dark:text-gray-300">
                                        {{ substr($course->teacher->name, 0, 1) }}
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                        {{ $course->teacher->name }}
                                    </p>
                                </div>

                                {{-- Nút Xem chi tiết (Đẩy xuống đáy) --}}
                                <div class="mt-auto pt-4 border-t dark:border-gray-700">
                                    <a href="{{ route('course.show', $course) }}" class="block w-full text-center bg-gray-100 dark:bg-gray-800 hover:bg-indigo-600 hover:text-white text-gray-800 dark:text-gray-200 py-2 rounded transition duration-200 text-sm font-semibold">
                                        Xem chi tiết
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Phân trang --}}
                    <div class="mt-8">
                        {{ $courses->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
