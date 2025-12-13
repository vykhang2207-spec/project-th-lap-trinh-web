<x-app-layout>
    {{-- PHẦN HEADER --}}
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Trang chủ - Khóa học nổi bật') }}
            </h2>
            @guest
            <div class="hidden sm:block text-sm text-gray-500">
                Đăng ký ngay để bắt đầu học tập!
            </div>
            @endguest
        </div>
    </x-slot>

    {{-- PHẦN NỘI DUNG CHÍNH --}}
    <div class="py-12 bg-gray-100 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- 1. BANNER QUẢNG CÁO --}}
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl shadow-xl p-8 md:p-12 text-white relative overflow-hidden">
                <div class="relative z-10">
                    <h1 class="text-3xl md:text-5xl font-extrabold mb-4 tracking-tight leading-tight">
                        Học Lập Trình Từ Con Số 0
                    </h1>
                    <p class="text-lg md:text-xl opacity-90 font-medium text-indigo-100 max-w-2xl">
                        Khám phá các khóa học thực chiến từ cơ bản đến nâng cao. Đầu tư cho kiến thức, đầu tư cho tương lai.
                    </p>
                </div>
                {{-- Decor --}}
                <div class="absolute top-0 right-0 -mt-10 -mr-10 w-64 h-64 bg-white opacity-10 rounded-full blur-3xl mix-blend-overlay"></div>
                <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-40 h-40 bg-pink-500 opacity-20 rounded-full blur-2xl mix-blend-overlay"></div>
            </div>

            {{-- 2. THANH TÌM KIẾM (LIVE SEARCH) --}}
            <div class="relative z-40" x-data="{
                query: '{{ request('search') }}',
                results: [],
                isOpen: false,
                
                async fetchSuggestions() {
                    if (this.query.length < 2) {
                        this.results = [];
                        this.isOpen = false;
                        return;
                    }
                    try {
                        let response = await fetch(`{{ route('search.suggestions') }}?query=${this.query}`);
                        this.results = await response.json();
                        this.isOpen = true;
                    } catch (e) {
                        console.error(e);
                    }
                },
                close() { this.isOpen = false; }
            }" @click.away="close()">

                <form action="{{ route('home') }}" method="GET" class="flex gap-4">
                    <div class="flex-grow relative">
                        {{-- Input --}}
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" name="search" x-model="query" @input.debounce.300ms="fetchSuggestions()" @focus="isOpen = true" autocomplete="off" class="block w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 py-3.5 pl-11 pr-5 shadow-sm text-base transition placeholder-gray-400" placeholder="Bạn muốn học gì hôm nay? (VD: Laravel, React...)">
                        </div>

                        {{-- Dropdown Gợi ý --}}
                        <div x-show="isOpen && results.length > 0" x-transition.opacity.duration.200ms class="absolute top-full left-0 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-2xl mt-2 overflow-hidden z-50">
                            <ul>
                                <template x-for="course in results" :key="course.id">
                                    <li>
                                        <a :href="`/course/${course.id}`" class="flex items-center px-4 py-3 hover:bg-indigo-50 dark:hover:bg-gray-700 transition cursor-pointer border-b border-gray-100 dark:border-gray-700 last:border-0">
                                            {{-- Sửa lại: Dùng course.image khớp với JSON controller trả về --}}
                                            <img :src="course.image && course.image.startsWith('http') ? course.image : '/storage/' + course.image" class="w-12 h-8 object-cover rounded mr-3 bg-gray-200" alt="img" onerror="this.src='https://via.placeholder.com/60x40'">
                                            <div>
                                                <div class="font-bold text-gray-800 dark:text-gray-200 text-sm" x-text="course.title"></div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    GV: <span x-text="course.teacher"></span>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                </template>
                            </ul>
                            <a :href="`{{ route('home') }}?search=${query}`" class="block bg-gray-50 dark:bg-gray-900 text-center py-2 text-xs font-bold text-indigo-600 hover:text-indigo-800 dark:text-indigo-400">
                                Xem tất cả kết quả cho "<span x-text="query"></span>"
                            </a>
                        </div>
                    </div>

                    {{-- Nút Tìm kiếm --}}
                    <button type="submit" class="hidden sm:inline-flex justify-center items-center px-8 py-3 border border-transparent text-base font-bold rounded-xl shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition transform hover:-translate-y-0.5">
                        Tìm kiếm
                    </button>
                </form>
            </div>

            {{-- 3. DANH SÁCH KHÓA HỌC --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl">
                <div class="p-6 md:p-8 text-gray-900 dark:text-gray-100">

                    @if ($courses->isEmpty())
                    {{-- Không tìm thấy --}}
                    <div class="text-center py-20">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 mb-4">
                            <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Không tìm thấy khóa học nào</h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Hãy thử tìm với từ khóa khác.</p>
                        @if(request('search'))
                        <div class="mt-6">
                            <a href="{{ route('home') }}" class="text-indigo-600 hover:text-indigo-500 font-medium hover:underline">← Quay lại danh sách tất cả</a>
                        </div>
                        @endif
                    </div>
                    @else
                    {{-- GRID KHÓA HỌC --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach ($courses as $course)
                        <div class="group bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-xl overflow-hidden hover:shadow-2xl hover:border-indigo-100 dark:hover:border-indigo-900 transition-all duration-300 transform hover:-translate-y-1 flex flex-col h-full">

                            {{-- Ảnh Thumbnail --}}
                            <a href="{{ route('course.show', $course) }}" class="relative aspect-video overflow-hidden block">
                                <img src="{{ Str::startsWith($course->image_path, 'http') ? $course->image_path : asset('storage/' . $course->image_path) }}" alt="{{ $course->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" onerror="this.onerror=null;this.src='https://via.placeholder.com/640x360?text=No+Image';">

                                <div class="absolute top-2 right-2 bg-gray-900/80 backdrop-blur-sm text-white text-xs font-bold px-2 py-1 rounded-md shadow-sm border border-white/10">
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

                                {{-- Giảng viên --}}
                                <div class="flex items-center mb-4">
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-gray-700 flex items-center justify-center text-xs font-bold mr-2 text-indigo-600 dark:text-gray-300 border border-indigo-50 dark:border-gray-600">
                                        {{ substr($course->teacher->name ?? 'A', 0, 1) }}
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                        {{ $course->teacher->name ?? 'Admin' }}
                                    </p>
                                </div>

                                {{-- Thống kê (Like/Enroll) --}}
                                <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mb-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                                    <div class="flex items-center gap-3">
                                        <span class="flex items-center" title="Học viên">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                            {{ $course->enrollments_count }}
                                        </span>
                                        <span class="flex items-center" title="Lượt thích">
                                            <svg class="w-4 h-4 mr-1 text-pink-500" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" /></svg>
                                            {{ $course->likes_count }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Nút Xem --}}
                                <div class="mt-auto">
                                    <a href="{{ route('course.show', $course) }}" class="block w-full text-center bg-gray-50 dark:bg-gray-700 hover:bg-indigo-600 hover:text-white text-gray-700 dark:text-gray-300 py-2.5 rounded-lg transition duration-200 text-sm font-semibold border border-gray-200 dark:border-gray-600 hover:border-transparent">
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
