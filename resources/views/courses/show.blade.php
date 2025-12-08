<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Chi tiết khóa học') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 flex flex-col md:flex-row gap-8">

                    {{-- =================================================
                         CỘT TRÁI: ẢNH, GIÁ TIỀN & NÚT MUA
                         ================================================= --}}
                    <div class="w-full md:w-1/3">
                        {{-- 1. Xử lý hiển thị ảnh (Link online hoặc Link trong storage) --}}
                        <img src="{{ Str::startsWith($course->image_path, 'http') ? $course->image_path : asset('storage/' . $course->image_path) }}" alt="{{ $course->title }}" class="w-full rounded-lg shadow-md mb-6 object-cover aspect-video" onerror="this.onerror=null;this.src='https://via.placeholder.com/400x250?text=No+Image'">

                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg text-center">
                            {{-- Giá tiền --}}
                            <p class="text-3xl font-bold text-red-600 mb-2">
                                {{ number_format($course->price) }} VNĐ
                            </p>

                            @auth
                            {{-- 2. Kiểm tra: User đã mua khóa này chưa? --}}
                            {{-- Lưu ý: Cách check này đơn giản, nếu web lớn nên check trong Controller --}}
                            @if(Auth::user()->enrollments->contains('course_id', $course->id))

                            {{-- ĐÃ MUA -> Hiện nút "TIẾP TỤC HỌC" --}}

                            {{-- 👇 FIX LỖI QUAN TRỌNG: Kiểm tra xem có bài học đầu tiên không --}}
                            @php
                            $firstLesson = null;
                            // Kiểm tra có chương nào không?
                            $firstChapter = $course->chapters->first();
                            if ($firstChapter) {
                            // Nếu có chương, lấy bài học đầu tiên của chương đó
                            $firstLesson = $firstChapter->lessons->first();
                            }
                            @endphp

                            @if($firstLesson)
                            {{-- Nếu có bài học -> Link tới bài đó --}}
                            <a href="{{ route('lesson.show', [$course->id, $firstLesson->id]) }}" class="block w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition transform hover:scale-105 duration-200">
                                TIẾP TỤC HỌC
                            </a>
                            @else
                            {{-- Nếu chưa có bài học -> Hiện nút xám (Không bấm được) --}}
                            <button disabled class="block w-full bg-gray-400 cursor-not-allowed text-white font-bold py-3 px-4 rounded-lg">
                                CHƯA CÓ BÀI HỌC
                            </button>
                            @endif

                            <p class="text-xs text-green-600 mt-2 font-semibold">Bạn đã sở hữu khóa học này</p>

                            @else
                            {{-- CHƯA MUA -> Hiện nút "MUA NGAY" --}}
                            <a href="{{ route('payment.checkout', $course) }}" class="block w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-lg transition transform hover:scale-105 duration-200 uppercase">
                                Mua khóa học ngay
                            </a>
                            <p class="text-xs text-gray-500 mt-2">Truy cập trọn đời • Hoàn tiền trong 7 ngày</p>
                            @endif
                            @else
                            {{-- CHƯA ĐĂNG NHẬP -> Yêu cầu đăng nhập --}}
                            <a href="{{ route('login') }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition">
                                ĐĂNG NHẬP ĐỂ MUA
                            </a>
                            @endauth
                        </div>

                        {{-- Thông tin giảng viên --}}
                        <div class="mt-6 border-t pt-4 dark:border-gray-600">
                            <p class="font-semibold">Giảng viên:</p>
                            <div class="flex items-center mt-2">
                                <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center font-bold text-gray-600 uppercase">
                                    {{ substr($course->teacher->name ?? 'T', 0, 1) }}
                                </div>
                                <span class="ml-3">{{ $course->teacher->name ?? 'Ẩn danh' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- =================================================
                         CỘT PHẢI: NỘI DUNG CHI TIẾT & DANH SÁCH BÀI HỌC
                         ================================================= --}}
                    <div class="w-full md:w-2/3">
                        <h1 class="text-3xl font-bold mb-4">{{ $course->title }}</h1>

                        {{-- Mô tả khóa học --}}
                        <div class="prose dark:prose-invert max-w-none mb-8 text-gray-600 dark:text-gray-300">
                            <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-white">Giới thiệu khóa học</h3>
                            <p class="whitespace-pre-line leading-relaxed">
                                {{ $course->description }}
                            </p>
                        </div>

                        {{-- Danh sách bài học (Accordion) --}}
                        <div>
                            <h3 class="text-xl font-semibold mb-4 border-b pb-2">Nội dung bài học ({{ $course->chapters->count() }} chương)</h3>

                            @if($course->chapters->count() > 0)
                            <div class="space-y-3">
                                @foreach($course->chapters as $chapter)
                                {{-- Sử dụng AlpineJS (x-data) để làm hiệu ứng đóng mở --}}
                                <div x-data="{ open: false }" class="border dark:border-gray-700 rounded-lg overflow-hidden transition-all duration-200">

                                    {{-- Tiêu đề chương (Bấm vào để mở) --}}
                                    <button @click="open = !open" class="w-full flex justify-between items-center p-4 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition cursor-pointer">
                                        <span class="font-semibold text-left">{{ $chapter->title }}</span>
                                        {{-- Mũi tên xoay --}}
                                        <svg :class="{'rotate-180': open}" class="w-5 h-5 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>

                                    {{-- Danh sách bài học bên trong (Hiện khi open = true) --}}
                                    <div x-show="open" x-collapse class="bg-white dark:bg-gray-800" style="display: none;">
                                        @if($chapter->lessons->count() > 0)
                                        @foreach($chapter->lessons as $lesson)
                                        {{-- Link tới trang học (lesson.show) --}}
                                        <a href="{{ route('lesson.show', [$course->id, $lesson->id]) }}" class="block w-full">
                                            <div class="p-3 pl-6 border-t dark:border-gray-700 flex justify-between items-center hover:bg-gray-50 dark:hover:bg-gray-900 transition cursor-pointer">
                                                <div class="flex items-center gap-3">
                                                    {{-- Icon Play --}}
                                                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-200">{{ $lesson->title }}</span>
                                                </div>

                                                {{-- Label "Học thử" cho bài đầu tiên --}}
                                                @if($loop->first && $loop->parent->first)
                                                <span class="text-xs font-bold bg-green-100 text-green-700 px-2 py-1 rounded-full">Học thử</span>
                                                @endif
                                            </div>
                                        </a>
                                        @endforeach
                                        @else
                                        <div class="p-4 text-sm text-gray-500 italic">Chưa có bài giảng nào trong chương này.</div>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="p-4 bg-yellow-50 text-yellow-700 rounded-lg border border-yellow-200">
                                Khóa học này đang được cập nhật nội dung.
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
