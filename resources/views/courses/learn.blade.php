<x-app-layout>
    {{-- PHẦN HEADER (Giữ nguyên) --}}
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

    {{-- CONTAINER CHÍNH --}}
    <div class="flex flex-col lg:flex-row h-[calc(100vh-65px)]">

        {{-- CỘT TRÁI: VIDEO PLAYER & ĐIỀU HƯỚNG --}}
        <div class="lg:w-3/4 bg-black flex flex-col relative group">

            {{-- Video Player --}}
            <div class="flex-1 flex items-center justify-center bg-black relative w-full h-full">
                @if(Str::contains($lesson->video_url, 'youtube.com') || Str::contains($lesson->video_url, 'youtu.be'))
                <iframe class="w-full h-full absolute inset-0" src="{{ str_replace('watch?v=', 'embed/', $lesson->video_url) }}" frameborder="0" allowfullscreen>
                </iframe>
                @else
                <div class="text-white text-center">
                    <p class="text-6xl mb-4">▶️</p>
                    <p>Video Demo Player</p>
                    <p class="text-xs text-gray-500 mt-2">{{ $lesson->video_url }}</p>
                </div>
                @endif
            </div>

            {{-- THANH ĐIỀU HƯỚNG BÊN DƯỚI (CÓ NÚT HOÀN THÀNH) --}}
            <div class="bg-white dark:bg-gray-800 p-4 flex justify-between items-center border-t dark:border-gray-700" x-data="{
                    completed: {{ \Illuminate\Support\Facades\DB::table('lesson_views')->where('user_id', Auth::id())->where('lesson_id', $lesson->id)->exists() ? 'true' : 'false' }},
                    isLoading: false,

                    async markComplete() {
                        if (this.isLoading) return;
                        this.isLoading = true;

                        try {
                            let res = await fetch('{{ route('lesson.complete', $lesson->id) }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            });
                            
                            if (res.ok) {
                                this.completed = true;
                                // Reload nhẹ sidebar để cập nhật trạng thái (nếu cần thiết)
                                // window.location.reload(); 
                            }
                        } catch(e) {
                            console.error(e);
                        } finally {
                            this.isLoading = false;
                        }
                    }
                 }">

                {{-- Nút Bài trước --}}
                @if($previousLesson)
                <a href="{{ route('lesson.show', [$course->id, $previousLesson->id]) }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                    &laquo; Bài trước
                </a>
                @else
                <button disabled class="px-4 py-2 bg-gray-100 dark:bg-gray-900 text-gray-400 rounded cursor-not-allowed">&laquo; Bài trước</button>
                @endif

                {{-- 👇 NÚT ĐÁNH DẤU HOÀN THÀNH (MỚI THÊM) --}}
                <button @click="markComplete()" class="px-6 py-2 rounded font-bold transition flex items-center gap-2 shadow-sm" :class="completed ? 'bg-green-100 text-green-700 cursor-default border border-green-500' : 'bg-indigo-600 text-white hover:bg-indigo-700'">

                    <span x-show="!completed && !isLoading">Đánh dấu đã học</span>
                    <span x-show="isLoading">Đang lưu...</span>

                    <span x-show="completed" class="flex items-center">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Đã hoàn thành
                    </span>
                </button>

                {{-- Nút Bài tiếp theo --}}
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
            <div class="p-4 border-b dark:border-gray-700 font-bold text-lg flex justify-between items-center">
                <span>Nội dung khóa học</span>
                <span class="text-xs text-gray-500 font-normal">{{ $course->progress() }}% hoàn thành</span>
            </div>

            <div class="flex flex-col">
                @foreach($course->chapters as $chapter)
                <div x-data="{ open: true }">
                    <button @click="open = !open" class="w-full flex justify-between items-center p-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 font-semibold text-sm transition">
                        <span>{{ $chapter->title }}</span>
                        <svg :class="{'rotate-180': open}" class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-show="open" class="bg-white dark:bg-gray-800">
                        @foreach($chapter->lessons as $l)
                        @php
                        // Check trạng thái đã học của từng bài trong list
                        $isDone = \Illuminate\Support\Facades\DB::table('lesson_views')
                        ->where('user_id', Auth::id())
                        ->where('lesson_id', $l->id)
                        ->exists();

                        $isActive = ($l->id === $lesson->id);
                        @endphp

                        <a href="{{ route('lesson.show', [$course->id, $l->id]) }}" class="block p-3 border-b dark:border-gray-700 text-sm hover:bg-indigo-50 dark:hover:bg-gray-900 transition flex items-start gap-2
                                  {{ $isActive ? 'bg-indigo-50 dark:bg-gray-700 text-indigo-700 font-bold border-l-4 border-indigo-500' : 'text-gray-600 dark:text-gray-400 border-l-4 border-transparent' }}">

                            {{-- Icon trạng thái (Check xanh hoặc tròn thường) --}}
                            <div class="mt-0.5 shrink-0">
                                @if($isDone)
                                {{-- Đã học: Dấu check xanh lá --}}
                                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                @elseif($isActive)
                                {{-- Đang học: Dấu play --}}
                                <svg class="w-4 h-4 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" /></svg>
                                @else
                                {{-- Chưa học: Vòng tròn --}}
                                <div class="w-4 h-4 border-2 border-gray-300 rounded-full"></div>
                                @endif
                            </div>

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
