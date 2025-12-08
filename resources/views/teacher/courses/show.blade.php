<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Chi tiết khóa học') }}
            </h2>
            <a href="{{ route('teacher.courses.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md text-sm transition">
                &larr; Quay lại danh sách
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 flex gap-6">
                <div class="w-1/3">
                    <img src="{{ str_starts_with($course->image_path, 'http') ? $course->image_path : asset('storage/' . $course->image_path) }}" alt="{{ $course->title }}" class="w-full h-48 object-cover rounded-lg shadow-md">
                </div>
                <div class="w-2/3">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ $course->title }}</h3>
                    <p class="text-gray-600 dark:text-gray-300 mb-4 line-clamp-3">{{ $course->description }}</p>
                    <div class="flex items-center gap-4">
                        <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm font-semibold">
                            Giá: {{ number_format($course->price) }} VND
                        </span>
                        <span class="px-3 py-1 {{ $course->is_approved ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }} rounded-full text-sm font-semibold">
                            {{ $course->is_approved ? 'Đã duyệt' : 'Chờ duyệt' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border-l-4 border-blue-500">
                    <div class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase">Tổng Lượt Xem</div>
                    <div class="mt-2 text-3xl font-bold text-gray-800 dark:text-gray-100">{{ number_format($totalViews) }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border-l-4 border-green-500">
                    <div class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase">Lượt Thích</div>
                    <div class="mt-2 text-3xl font-bold text-green-600">{{ $course->likes_count }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border-l-4 border-red-500">
                    <div class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase">Không Thích</div>
                    <div class="mt-2 text-3xl font-bold text-red-600">{{ $course->dislikes_count }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border-l-4 border-yellow-500">
                    <div class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase">Bình Luận</div>
                    <div class="mt-2 text-3xl font-bold text-yellow-600">{{ $course->comments_count }}</div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-bold mb-6 text-gray-900 dark:text-gray-100 flex items-center border-b pb-2">
                        💬 Phản hồi từ học viên
                    </h3>

                    @if($comments->count() > 0)
                    <div class="space-y-6">
                        @foreach($comments as $comment)
                        <div class="flex gap-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl transition hover:bg-gray-100 dark:hover:bg-gray-700">
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg shadow-sm">
                                {{ substr($comment->user->name ?? 'U', 0, 1) }}
                            </div>

                            <div class="flex-1">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-bold text-gray-900 dark:text-gray-100">{{ $comment->user->name ?? 'Người dùng ẩn' }}</h4>
                                        <div class="text-xs text-gray-500 mt-0.5">{{ $comment->created_at->format('d/m/Y \l\ú\c H:i') }}</div>
                                    </div>
                                </div>
                                <p class="text-gray-700 dark:text-gray-300 mt-2 leading-relaxed">
                                    {{ $comment->content }}
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-10">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Chưa có bình luận nào</h3>
                        <p class="mt-1 text-sm text-gray-500">Khóa học này chưa nhận được phản hồi nào từ học viên.</p>
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
