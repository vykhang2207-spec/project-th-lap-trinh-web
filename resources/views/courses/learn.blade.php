<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight truncate max-w-2xl">
                {{ $course->title }} - <span class="text-red-500">{{ $lesson->title }}</span>
            </h2>
            <a href="{{ route('course.show', $course) }}" class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                &larr; Quay lại trang chi tiết
            </a>
        </div>
    </x-slot>

    <div class="flex flex-col lg:flex-row h-[calc(100vh-65px)]"> {{-- Full height trừ header --}}

        {{-- CỘT TRÁI: VIDEO PLAYER --}}
        <div class="lg:w-3/4 bg-black flex flex-col">
            <div class="flex-1 flex items-center justify-center bg-black relative w-full h-full">
                {{-- Video Iframe --}}
                @if(Str::contains($lesson->video_url, 'youtube.com') || Str::contains($lesson->video_url, 'youtu.be'))
                {{-- Xử lý link YouTube (Ví dụ) --}}
                <iframe class="w-full h-full absolute inset-0" src="{{ $lesson->video_url }}" frameborder="0" allowfullscreen></iframe>
                @else
                {{-- Giả lập Video Player nếu không có link thật --}}
                <div class="text-white text-center">
                    <p class="text-6xl mb-4">▶️</p>
                    <p>Video Demo Player</p>
                    <p class="text-xs text-gray-500 mt-2">{{ $lesson->video_url }}</p>
                </div>
                @endif
            </div>

            {{-- Thanh điều hướng bài học (Dưới video) --}}
            <div class="bg-white dark:bg-gray-800 p-4 flex justify-between items-center border-t dark:border-gray-700">
                @if($previousLesson)
                <a href="{{ route('lesson.show', [$course->id, $previousLesson->id]) }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                    &laquo; Bài trước
                </a>
                @else
                <button disabled class="px-4 py-2 bg-gray-100 dark:bg-gray-900 text-gray-400 rounded cursor-not-allowed">&laquo; Bài trước</button>
                @endif

                @if($nextLesson)
                <a href="{{ route('lesson.show', [$course->id, $nextLesson->id]) }}" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
                    Bài tiếp theo &raquo;
                </a>
                @else
                <button disabled class="px-4 py-2 bg-gray-100 dark:bg-gray-900 text-gray-400 rounded cursor-not-allowed">Hết khóa học</button>
                @endif
            </div>
        </div>

        {{-- CỘT PHẢI: DANH SÁCH BÀI HỌC (SIDEBAR) --}}
        <div class="lg:w-1/4 bg-white dark:bg-gray-800 border-l dark:border-gray-700 overflow-y-auto">
            <div class="p-4 border-b dark:border-gray-700 font-bold text-lg">
                Nội dung khóa học
            </div>

            <div class="flex flex-col">
                @foreach($course->chapters as $chapter)
                <div x-data="{ open: true }">
                    <button @click="open = !open" class="w-full flex justify-between items-center p-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 font-semibold text-sm">
                        <span>{{ $chapter->title }}</span>
                        <svg :class="{'rotate-180': open}" class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-show="open" class="bg-white dark:bg-gray-800">
                        @foreach($chapter->lessons as $l)
                        <a href="{{ route('lesson.show', [$course->id, $l->id]) }}" class="block p-3 border-b dark:border-gray-700 text-sm hover:bg-red-50 dark:hover:bg-gray-900 transition flex items-start gap-2
                                   {{ $l->id === $lesson->id ? 'bg-red-100 dark:bg-gray-700 text-red-700 font-bold border-l-4 border-red-500' : 'text-gray-600 dark:text-gray-400' }}">

                            {{-- Icon trạng thái --}}
                            @if($l->id === $lesson->id)
                            <svg class="w-4 h-4 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" /></svg>
                            @else
                            <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            @endif

                            <span>{{ $l->title }}</span>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
