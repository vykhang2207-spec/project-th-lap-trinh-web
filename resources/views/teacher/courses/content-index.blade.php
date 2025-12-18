<x-app-layout>
    {{-- 1. KHAI BÁO LOGIC MODAL & CRUD --}}
    <div x-data="{ 
        showModal: false, 
        isEditMode: false,                // Biến này để biết đang Thêm hay đang Sửa
        formAction: '',                   // Đường dẫn form sẽ gửi tới
        currentChapterTitle: '',
        
        // Dữ liệu Form (Dùng x-model để bind vào input)
        form: {
            title: '',
            video_url: '',
            duration: '',
            is_preview: false
        },

        // Hàm mở Modal THÊM MỚI
        openAddModal(chapterId, chapterTitle) {
            this.isEditMode = false;
            this.formAction = '/teacher/chapters/' + chapterId + '/lessons';
            this.currentChapterTitle = chapterTitle;
            
            // Reset form về rỗng
            this.form = { title: '', video_url: '', duration: '', is_preview: false };
            this.showModal = true;
        },

        // Hàm mở Modal SỬA (Điền dữ liệu cũ vào form)
        openEditModal(lesson, updateUrl, chapterTitle) {
            this.isEditMode = true;
            this.formAction = updateUrl;
            this.currentChapterTitle = chapterTitle;

            // Đổ dữ liệu cũ vào input
            this.form = {
                title: lesson.title,
                video_url: lesson.video_url,
                duration: lesson.duration,
                is_preview: lesson.is_preview == 1 // Chuyển số sang boolean
            };
            this.showModal = true;
        }
    }">

        <x-slot name="header">
            <div class="flex flex-col space-y-2 md:flex-row md:items-center md:justify-between md:space-y-0">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                        {{ __('Quản lý Nội dung') }}
                    </h2>
                    <div class="mt-2 flex items-center">
                        <span class="text-gray-600 dark:text-gray-300 mr-2">Khóa học:</span>
                        <span class="font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-gray-800 px-3 py-1.5 rounded-lg border border-indigo-100 dark:border-gray-700 inline-flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            {{ $course->title }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center space-x-3 mt-4 md:mt-0">
                    <a href="{{ route('teacher.courses.index') }}" class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Quản lý Khóa học
                    </a>
                </div>
            </div>
        </x-slot>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                {{--  THÊM CHƯƠNG --}}
                <div class="mb-10">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 md:p-8 border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center mb-6">
                            <div class="flex-shrink-0 w-10 h-10 md:w-12 md:h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 md:w-6 md:h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-5">
                                <h3 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white">Thêm Chương mới</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Tạo chương học mới cho khóa học của bạn</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('teacher.courses.chapters.store', $course) }}">
                            @csrf
                            <div class="flex flex-col md:flex-row gap-4">
                                <div class="flex-grow">
                                    <input type="text" name="title" placeholder="Ví dụ: Chương 1 - Giới thiệu tổng quan về khóa học..." required class="w-full px-4 py-3 md:px-5 md:py-3.5 text-base border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg md:rounded-xl focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all duration-200 placeholder-gray-500">
                                </div>
                                <button type="submit" class="inline-flex items-center justify-center px-6 py-3 md:px-8 md:py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium md:font-bold rounded-lg md:rounded-xl shadow hover:shadow-md transform hover:-translate-y-0.5 transition-all duration-200 whitespace-nowrap">
                                    <svg class="w-4 h-4 md:w-5 md:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Tạo Chương
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- DANH SÁCH CHƯƠNG --}}
                <div class="space-y-8">
                    @forelse ($course->chapters as $chapter)
                    <div class="bg-white dark:bg-gray-800 rounded-xl md:rounded-2xl shadow-md overflow-hidden border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow duration-300">

                        {{-- HEADER CHƯƠNG --}}
                        <div class="px-4 py-4 md:px-8 md:py-6 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div class="flex-1 text-center">
                                    <h4 class="text-3xl md:text-5xl font-black uppercase text-gray-900 dark:text-white tracking-tight leading-tight">
                                        {{ $chapter->title }}
                                    </h4>
                                </div>
                            </div>
                        </div>

                        {{-- DANH SÁCH BÀI HỌC --}}
                        <div class="p-4 md:p-8 bg-gray-900/30">
                            @if($chapter->lessons->count() > 0)
                            <div class="space-y-3 md:space-y-4">
                                @foreach ($chapter->lessons as $lesson)
                                <div class="group flex items-center justify-between p-4 md:p-5 bg-gray-900/50 dark:bg-gray-700/50 border border-gray-700 hover:border-indigo-600 rounded-lg md:rounded-xl hover:shadow-lg transition-all duration-200">

                                    {{-- KHỐI TRÁI --}}
                                    <div class="flex items-center gap-4 flex-1 min-w-0">
                                        <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full bg-white text-indigo-600 text-base font-bold shadow-sm border border-indigo-600">
                                            {{ $loop->iteration }}
                                        </span>
                                        <span class="text-sm md:text-base font-medium text-white truncate group-hover:text-indigo-400 transition-colors">
                                            {{ $lesson->title }}
                                        </span>
                                    </div>

                                    {{-- KHỐI PHẢI --}}
                                    <div class="flex items-center gap-5 flex-shrink-0 ml-4">
                                        <div class="hidden sm:flex flex-col text-xs md:text-sm text-gray-400 text-right">
                                            <span>⏱ {{ $lesson->duration ?? '00:00' }}</span>
                                            @if(!$lesson->video_url)
                                            <span class="text-amber-400 font-medium">Chưa có video</span>
                                            @else
                                            <span class="text-green-400 font-medium">Video đã có</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-2">

                                            {{--  NÚT SỬA (Đã gắn action) --}}
                                            <button type="button" @click="openEditModal({{ $lesson }}, '{{ route('teacher.lessons.update', $lesson->id) }}', '{{ addslashes($chapter->title) }}')" class="px-2 py-1.5 text-xs font-medium text-gray-200 dark:text-gray-200 bg-gray-600 hover:bg-gray-700 dark:hover:bg-gray-600 rounded-md transition-all duration-200">
                                                Sửa
                                            </button>

                                            {{--  NÚT XÓA (Đã gắn form delete) --}}
                                           
                                            <form action="{{ route('teacher.lessons.destroy', $lesson->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài học này không?');" class="contents">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-2 py-1.5 text-xs font-medium text-white bg-red-600 hover:bg-red-700 rounded-md transition-all duration-200">
                                                    Xóa
                                                </button>
                                            </form>

                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="text-center py-8 md:py-10">
                                <div class="mx-auto w-12 h-12 md:w-16 md:h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-3 md:mb-4">
                                    <svg class="w-6 h-6 md:w-8 md:h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-sm md:text-lg font-medium text-gray-700 dark:text-gray-300 mb-1 md:mb-2">Chưa có bài học nào</h3>
                                <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400">Thêm bài học đầu tiên vào chương này</p>
                            </div>
                            @endif

                            {{-- NÚT THÊM BÀI HỌC  --}}
                            <div class="mt-6 md:mt-8 pt-4 md:pt-6 border-t border-gray-200 dark:border-gray-700">
                                <button type="button" @click="openAddModal('{{ $chapter->id }}', '{{ addslashes($chapter->title) }}')" class="w-full group flex items-center justify-center gap-2 md:gap-3 px-4 py-3 md:px-5 md:py-4 text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg md:rounded-xl shadow-md hover:shadow-lg transition-all duration-200">
                                    <svg class="w-5 h-5 md:w-6 md:h-6 text-white group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    <span class="text-sm md:text-base font-bold">Thêm bài học mới vào chương</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-12 md:py-16 bg-gray-50 dark:bg-gray-800/50 rounded-xl md:rounded-2xl border-2 border-dashed border-gray-300 dark:border-gray-700">
                        <h3 class="text-lg md:text-2xl font-bold text-gray-900 dark:text-white mb-2 md:mb-2">Khóa học chưa có nội dung</h3>
                        <p class="text-sm md:text-base text-gray-600 dark:text-gray-400 px-4">Hãy bắt đầu bằng việc tạo chương học đầu tiên</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{--  2. MODAL DÙNG CHUNG (THÊM & SỬA) --}}
        <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            {{-- Backdrop --}}
            <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center">
                {{-- Modal Panel --}}
                <div x-show="showModal" @click.away="showModal = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl">

                    {{-- Form: Action động dựa trên biến formAction --}}
                    <form method="POST" :action="formAction">
                        @csrf
                        {{--  Nếu là Edit Mode thì thêm method PUT --}}
                        <input type="hidden" name="_method" value="PUT" :disabled="!isEditMode">

                        {{-- Header Modal --}}
                        <div class="px-8 py-6 border-b border-gray-100 dark:border-gray-700">
                            {{-- Tiêu đề động: Sửa hoặc Thêm --}}
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white" id="modal-title" x-text="isEditMode ? 'Cập nhật bài học' : 'Thêm bài học mới'"></h3>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                <span x-text="isEditMode ? 'Đang chỉnh sửa trong: ' : 'Đang thêm vào: '"></span>
                                <span x-text="currentChapterTitle" class="font-semibold text-blue-500 dark:text-blue-400"></span>
                            </p>
                        </div>

                        {{-- Body Modal --}}
                        <div class="px-8 py-8 space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tên bài học <span class="text-red-500">*</span></label>
                                {{-- x-model để bind dữ liệu 2 chiều --}}
                                <input type="text" name="title" x-model="form.title" required class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all py-3 px-4" placeholder="VD: Cài đặt môi trường...">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Link Video (Youtube/Vimeo)</label>
                                <input type="url" name="video_url" x-model="form.video_url" class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all py-3 px-4" placeholder="https://youtube.com/...">
                            </div>

                            <div class="flex gap-6">
                                <div class="w-1/2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Thời lượng</label>
                                    <input type="text" name="duration" x-model="form.duration" class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all py-3 px-4" placeholder="10:05">
                                </div>
                                <div class="w-1/2 flex items-end pb-4">
                                    <label class="inline-flex items-center cursor-pointer group select-none">
                                        <div class="relative flex items-center">
                                            <input type="checkbox" name="is_preview" x-model="form.is_preview" value="1" class="peer h-6 w-6 cursor-pointer appearance-none rounded-md border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 checked:border-blue-600 checked:bg-blue-600 focus:ring-2 focus:ring-blue-500/20 transition-all">
                                            <svg class="pointer-events-none absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 text-white opacity-0 peer-checked:opacity-100 w-4 h-4" viewBox="0 0 14 14" fill="none">
                                                <path d="M3 8L6 11L11 3.5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                        </div>
                                        <span class="ml-3 text-base font-medium text-gray-700 dark:text-gray-300 group-hover:text-blue-500 dark:group-hover:text-blue-400 transition-colors">Cho xem thử (Free)</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Footer Modal --}}
                        <div class="px-8 py-6 bg-gray-50 dark:bg-gray-700/30 flex flex-row-reverse gap-4 rounded-b-2xl border-t border-gray-100 dark:border-gray-700">
                            {{-- Nút Lưu thay đổi text tùy vào chế độ --}}
                            <button type="submit" class="inline-flex justify-center rounded-xl bg-blue-600 px-8 py-3.5 text-base font-bold text-white shadow-lg shadow-blue-500/30 hover:bg-blue-700 hover:shadow-blue-500/40 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all" x-text="isEditMode ? 'Cập nhật' : 'Lưu Bài Học'">
                            </button>
                            <button type="button" @click="showModal = false" class="inline-flex justify-center rounded-xl bg-transparent border border-gray-300 dark:border-gray-500 px-8 py-3.5 text-base font-bold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none transition-all">
                                Hủy bỏ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div> {{-- Kết thúc x-data div --}}
</x-app-layout>
