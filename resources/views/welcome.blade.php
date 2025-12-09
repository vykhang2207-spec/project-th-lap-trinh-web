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
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Banner Quảng Cáo --}}
            <div class="mb-8 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg shadow-lg p-8 text-white relative overflow-hidden">
                <div class="relative z-10">
                    <h1 class="text-4xl font-bold mb-2">Học Lập Trình Từ Con Số 0</h1>
                    <p class="text-lg opacity-90">Khám phá các khóa học thực chiến từ cơ bản đến nâng cao.</p>
                </div>
                {{-- Trang trí nhẹ --}}
                <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-white opacity-10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-40 h-40 bg-white opacity-10 rounded-full blur-3xl"></div>
            </div>

            {{-- 🔍 THANH TÌM KIẾM (LIVE SEARCH & FIX UI V2) --}}
            <div class="mb-8 relative z-50" x-data="{
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
                    
                    close() { 
                        this.isOpen = false; 
                    }
                 }" @click.away="close()">

                <form action="{{ route('home') }}" method="GET" class="flex gap-4">
                    <div class="flex-grow relative">


                        {{-- 🔍 THANH TÌM KIẾM (NO ICON - CLEAN VERSION) --}}
                        <div class="mb-8 relative z-50" x-data="{
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
                    
                    close() { 
                        this.isOpen = false; 
                    }
                 }" @click.away="close()">

                            <form action="{{ route('home') }}" method="GET" class="flex gap-4">
                                <div class="flex-grow relative">

                                    {{-- Input: Đã bỏ pl-16, thay bằng px-5 cho cân đối --}}
                                    <input type="text" name="search" x-model="query" @input.debounce.300ms="fetchSuggestions()" @focus="isOpen = true" autocomplete="off" class="block w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 py-3.5 px-5 shadow-sm text-base transition placeholder-gray-400" placeholder="Gõ tên khóa học để tìm kiếm...">

                                    {{-- DROPDOWN GỢI Ý KẾT QUẢ --}}
                                    <div x-show="isOpen && results.length > 0" x-transition.opacity.duration.200ms class="absolute top-full left-0 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-2xl mt-2 overflow-hidden z-50">

                                        <ul>
                                            <template x-for="course in results" :key="course.id">
                                                <li>
                                                    <a :href="`/course/${course.id}`" class="flex items-center px-4 py-3 hover:bg-indigo-50 dark:hover:bg-gray-700 transition cursor-pointer border-b border-gray-100 dark:border-gray-700 last:border-0">
                                                        <img :src="course.image_path && course.image_path.startsWith('http') ? course.image_path : '/storage/' + course.image_path" class="w-12 h-8 object-cover rounded mr-3" alt="img">

                                                        <div>
                                                            <div class="font-bold text-gray-800 dark:text-gray-200 text-sm" x-text="course.title"></div>
                                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                                GV: <span x-text="course.teacher ? course.teacher.name : 'Ẩn danh'"></span>
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
                                <button type="submit" class="hidden sm:inline-flex justify-center items-center px-8 py-3 border border-transparent text-base font-bold rounded-xl shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                                    Tìm kiếm
                                </button>
                            </form>
                        </div>
                        {{-- Danh sách khóa học --}}
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 text-gray-900 dark:text-gray-100">

                                @if ($courses->isEmpty())
                                {{-- TRƯỜNG HỢP KHÔNG CÓ KHÓA HỌC --}}
                                <div class="text-center py-20">
                                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-gray-100">Không tìm thấy khóa học nào</h3>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Hãy thử tìm với từ khóa khác xem sao.</p>
                                    @if(request('search'))
                                    <div class="mt-6">
                                        <a href="{{ route('home') }}" class="text-indigo-600 hover:text-indigo-500 font-medium">← Quay lại danh sách tất cả</a>
                                    </div>
                                    @endif
                                </div>
                                @else
                                {{-- CÓ KHÓA HỌC -> HIỆN LƯỚI --}}
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">

                                    {{-- 👇 VÒNG LẶP BẮT ĐẦU --}}
                                    @foreach ($courses as $course)
                                    <div class="group bg-white dark:bg-gray-900 border dark:border-gray-700 rounded-lg overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 flex flex-col h-full">

                                        {{-- Ảnh khóa học --}}
                                        <a href="{{ route('course.show', $course) }}" class="relative overflow-hidden block">
                                            <img src="{{ Str::startsWith($course->image_path, 'http') ? $course->image_path : asset('storage/' . $course->image_path) }}" alt="{{ $course->title }}" class="w-full h-48 object-cover group-hover:scale-110 transition duration-500" onerror="this.onerror=null;this.src='https://via.placeholder.com/300x160?text=Course+Image';">
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
                                            <div class="flex items-center mb-3">
                                                <div class="w-6 h-6 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-xs font-bold mr-2 text-gray-600 dark:text-gray-300">
                                                    {{ substr($course->teacher->name, 0, 1) }}
                                                </div>
                                                <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                                    {{ $course->teacher->name }}
                                                </p>
                                            </div>

                                            {{-- 👇 PHẦN THỐNG KÊ (GIỮ NGUYÊN) --}}
                                            <div class="flex items-center justify-start gap-3 text-xs text-gray-500 dark:text-gray-400 mb-4 px-1" x-data="{ 
                                        likes: {{ $course->likes_count ?? 0 }}, 
                                        dislikes: {{ $course->dislikes_count ?? 0 }}, 
                                        myReaction: '{{ $course->reactions->where('user_id', Auth::id())->first()->type ?? '' }}',
                                        isLoading: false,

                                        async react(type) {
                                            if (this.isLoading) return;
                                            this.isLoading = true;
                                            try {
                                                const response = await fetch('{{ route('course.reaction', $course) }}', {
                                                    method: 'POST',
                                                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                                    body: JSON.stringify({ type: type })
                                                });
                                                if (response.status === 401) { window.location.href = '{{ route('login') }}'; return; }
                                                const data = await response.json();
                                                this.likes = data.likes_count;
                                                this.dislikes = data.dislikes_count;
                                                this.myReaction = data.user_reaction;
                                            } catch (error) { console.error('Lỗi:', error); } finally { this.isLoading = false; }
                                        }
                                     }">

                                                {{-- 1. Số học viên --}}
                                                <div class="flex items-center px-2 py-1 bg-gray-100 dark:bg-gray-800 rounded-md" title="Số học viên">
                                                    <svg class="w-4 h-4 mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                                    <span class="font-bold">{{ $course->enrollments_count ?? 0 }}</span>
                                                </div>

                                                {{-- 2. Nút Like --}}
                                                <button @click="react('like')" :disabled="isLoading" class="flex items-center px-2 py-1 rounded-md transition hover:bg-gray-100 dark:hover:bg-gray-700" :class="myReaction === 'like' ? 'text-green-600 font-bold bg-green-50 dark:bg-gray-800' : ''" title="Thích">
                                                    <svg class="w-4 h-4 mr-1 transition-colors duration-200" :class="myReaction === 'like' ? 'fill-current' : 'text-green-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" /></svg>
                                                    <span x-text="likes"></span>
                                                </button>

                                                {{-- 3. Nút Dislike --}}
                                                <button @click="react('dislike')" :disabled="isLoading" class="flex items-center px-2 py-1 rounded-md transition hover:bg-gray-100 dark:hover:bg-gray-700" :class="myReaction === 'dislike' ? 'text-red-600 font-bold bg-red-50 dark:bg-gray-800' : ''" title="Không thích">
                                                    <svg class="w-4 h-4 mr-1 transition-colors duration-200" :class="myReaction === 'dislike' ? 'fill-current' : 'text-red-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018a2 2 0 01.485.06l3.76.94m-7 10v5a2 2 0 002 2h.095c.5 0 .905-.405.905-.905 0-.714.211-1.412.608-2.006L17 13V4m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5" /></svg>
                                                    <span x-text="dislikes"></span>
                                                </button>
                                            </div>

                                            {{-- Nút Xem chi tiết --}}
                                            <div class="mt-auto pt-3 border-t dark:border-gray-700">
                                                <a href="{{ route('course.show', $course) }}" class="block w-full text-center bg-gray-100 dark:bg-gray-800 hover:bg-indigo-600 hover:text-white text-gray-800 dark:text-gray-200 py-2 rounded transition duration-200 text-sm font-semibold">
                                                    Xem chi tiết
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                    {{-- 👆 VÒNG LẶP KẾT THÚC --}}

                                </div>

                                {{-- Phân trang (Nằm ngoài Grid để nó xuống dòng) --}}
                                <div class="mt-8">
                                    {{ $courses->links() }}
                                </div>
                                @endif

                            </div>
                        </div>
                    </div>
            </div>
</x-app-layout>
