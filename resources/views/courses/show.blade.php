<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Chi tiết khóa học') }}
        </h2>
    </x-slot>

    {{-- NỀN CHUNG --}}
    <div class="py-12 bg-gray-100 dark:bg-gray-900">

        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- THÔNG BÁO FLASH --}}
            @if (session('status'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="mb-6 bg-green-900/50 border border-green-500 text-green-200 px-4 py-3 rounded relative backdrop-blur-sm">
                {{ session('status') }}
            </div>
            @endif

            {{-- CONTAINER CHÍNH --}}
            <div class="bg-gray-900/80 backdrop-blur-xl text-gray-100 overflow-hidden shadow-2xl sm:rounded-3xl p-8 space-y-12 border border-white/10">

                {{-- 1. TIÊU ĐỀ KHÓA HỌC --}}
                <div class="text-center">
                    <h1 class="text-4xl md:text-5xl font-extrabold text-white leading-tight mb-6 tracking-tight drop-shadow-lg">
                        {{ $course->title }}
                    </h1>

                    {{-- 2. THANH THỐNG KÊ --}}
                    <div class="flex flex-wrap justify-center items-center gap-3" x-data="{ 
                            likes: {{ $course->likes_count ?? 0 }}, 
                            dislikes: {{ $course->dislikes_count ?? 0 }}, 
                            myReaction: '{{ $userReaction ? $userReaction->type : '' }}',
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

                        {{-- Số học viên --}}
                        <div class="flex items-center px-4 py-2 bg-[#1f2937] rounded-lg shadow-sm" title="Số học viên">
                            <svg class="w-6 h-6 text-blue-500 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <span class="text-xl font-bold text-gray-200">{{ $course->enrollments_count }}</span>
                        </div>

                        {{-- Nút Like --}}
                        <button @click="react('like')" :disabled="isLoading" class="flex items-center px-4 py-2 bg-[#1f2937] rounded-lg transition hover:bg-gray-700 shadow-sm group" :class="myReaction === 'like' ? 'ring-1 ring-green-500 bg-gray-800' : ''">
                            <svg class="w-6 h-6 mr-2 transition-transform active:scale-110" :class="myReaction === 'like' ? 'text-green-500 fill-current' : 'text-green-500 fill-current'" viewBox="0 0 24 24">
                                <path d="M14 9.5a2 2 0 00-2-2h-2.5V3.5a1.5 1.5 0 00-2.66-1.06l-6.5 6.5A1.5 1.5 0 00.5 10v9A1.5 1.5 0 002 20.5h14.88a1.5 1.5 0 001.45-1.14l2.5-9A1.5 1.5 0 0019.38 8H14z" /></svg>
                            <span class="text-xl font-bold text-gray-200" x-text="likes"></span>
                        </button>

                        {{-- Nút Dislike --}}
                        <button @click="react('dislike')" :disabled="isLoading" class="flex items-center px-4 py-2 bg-[#1f2937] rounded-lg transition hover:bg-gray-700 shadow-sm group" :class="myReaction === 'dislike' ? 'ring-1 ring-red-500 bg-gray-800' : ''">
                            <svg class="w-6 h-6 mr-2 transition-transform active:scale-110" :class="myReaction === 'dislike' ? 'text-red-500 fill-current' : 'text-red-500 fill-current'" viewBox="0 0 24 24">
                                <path d="M10 14.5a2 2 0 002 2h2.5v4a1.5 1.5 0 002.66 1.06l6.5-6.5A1.5 1.5 0 0023.5 14v-9A1.5 1.5 0 0022 3.5H7.12a1.5 1.5 0 00-1.45 1.14l-2.5 9a1.5 1.5 0 001.45 1.86H10z" /></svg>
                            <span class="text-xl font-bold text-gray-200" x-text="dislikes"></span>
                        </button>
                    </div>
                </div>

                {{-- 3. HÌNH ẢNH KHÓA HỌC --}}
                <div class="relative group rounded-2xl overflow-hidden shadow-2xl border border-white/10">
                    <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-transparent to-transparent opacity-60"></div>
                    <img src="{{ Str::startsWith($course->image_path, 'http') ? $course->image_path : asset('storage/' . $course->image_path) }}" alt="{{ $course->title }}" class="w-full h-auto max-h-[500px] object-cover transform group-hover:scale-105 transition duration-700" onerror="this.onerror=null;this.src='https://via.placeholder.com/800x450?text=No+Image'">
                </div>

                {{-- 4. NÚT HÀNH ĐỘNG --}}
                <div class="bg-gray-800/40 p-8 rounded-2xl text-center border border-white/10 backdrop-blur-md shadow-inner">
                    @auth
                    @if(Auth::user()->enrollments->contains('course_id', $course->id))
                    <div class="mb-6"><span class="text-green-400 font-bold text-lg flex justify-center items-center gap-2 bg-green-500/10 py-2 px-4 rounded-full inline-flex"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>Bạn đã sở hữu khóa học này</span></div>
                    @php $firstLesson = $course->chapters->first()?->lessons->first(); @endphp
                    @if($firstLesson)
                    <a href="{{ route('lesson.show', [$course->id, $firstLesson->id]) }}" class="inline-block w-full sm:w-auto min-w-[250px] bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-500 hover:to-emerald-500 text-white font-bold py-4 px-10 rounded-xl text-xl shadow-lg hover:shadow-green-500/30 transition transform hover:-translate-y-1 border border-green-500/30">TIẾP TỤC HỌC</a>
                    @else
                    <button disabled class="inline-block w-full sm:w-auto min-w-[250px] bg-gray-700 cursor-not-allowed text-gray-400 font-bold py-4 px-10 rounded-xl text-xl border border-gray-600">CHƯA CÓ BÀI HỌC</button>
                    @endif
                    @else
                    <div class="mb-8"><span class="text-gray-400 text-sm uppercase tracking-wider font-semibold">Giá khóa học</span>
                        <div class="text-4xl md:text-5xl font-extrabold text-white mt-2 drop-shadow-lg">{{ number_format($course->price) }} <span class="text-2xl md:text-3xl text-gray-300">VNĐ</span></div>
                    </div>
                    <a href="{{ route('payment.checkout', $course) }}" class="inline-block w-full sm:w-auto min-w-[300px] bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold py-4 px-10 rounded-xl text-xl shadow-lg hover:shadow-indigo-500/30 transition transform hover:-translate-y-1 border border-indigo-500/30 uppercase tracking-wide">Mua khóa học ngay</a>
                    @endif
                    @else
                    <div class="mb-8"><span class="text-gray-400 text-sm uppercase tracking-wider font-semibold">Giá khóa học</span>
                        <div class="text-4xl md:text-5xl font-extrabold text-white mt-2 drop-shadow-lg">{{ number_format($course->price) }} <span class="text-2xl md:text-3xl text-gray-300">VNĐ</span></div>
                    </div>
                    <a href="{{ route('login') }}" class="inline-block w-full sm:w-auto min-w-[300px] bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 px-10 rounded-xl text-xl shadow-lg hover:shadow-blue-500/30 transition transform hover:-translate-y-1 border border-blue-500/30">ĐĂNG NHẬP ĐỂ MUA</a>
                    @endauth
                </div>

                {{-- 5. GIỚI THIỆU --}}
                <div class="prose prose-lg prose-invert max-w-none text-gray-300">
                    <h3 class="text-3xl font-bold mb-6 text-white flex items-center"><span class="bg-indigo-500 w-2 h-8 mr-4 rounded-full"></span>Giới thiệu khóa học</h3>
                    <div class="leading-relaxed whitespace-pre-line text-lg text-gray-300/90">{{ $course->description }}</div>
                    <div class="mt-10 flex items-center p-6 bg-gray-800/60 rounded-2xl border border-white/10 hover:border-indigo-500/50 transition duration-300 group">
                        <div class="w-16 h-16 bg-indigo-900/50 rounded-full flex items-center justify-center font-bold text-indigo-300 uppercase text-2xl border-2 border-indigo-500/30 group-hover:border-indigo-400 transition">{{ substr($course->teacher->name ?? 'T', 0, 1) }}</div>
                        <div class="ml-6">
                            <p class="text-xs text-gray-400 uppercase tracking-widest mb-1 font-semibold">Giảng viên</p><a href="{{ route('teacher.profile', $course->teacher_id) }}" class="font-bold text-white text-2xl hover:text-indigo-400 transition">{{ $course->teacher->name ?? 'Ẩn danh' }}</a>
                        </div>
                    </div>
                </div>

                {{-- 6. NỘI DUNG BÀI HỌC --}}
                <div>
                    <h3 class="text-3xl font-bold mb-8 text-white flex items-center"><span class="bg-indigo-500 w-2 h-8 mr-4 rounded-full"></span>Nội dung bài học</h3>
                    @if($course->chapters->count() > 0)
                    <div class="space-y-4">
                        @foreach($course->chapters as $chapter)
                        <div x-data="{ open: false }" class="border border-white/10 bg-gray-800/40 rounded-xl overflow-hidden shadow-sm backdrop-blur-sm transition-all duration-300 hover:border-gray-600">
                            <button @click="open = !open" class="w-full flex justify-between items-center p-5 bg-gray-800/60 hover:bg-gray-800 transition cursor-pointer text-white group">
                                <span class="font-bold text-xl text-left flex items-center">{{ $chapter->title }}<span class="ml-3 text-xs font-normal text-gray-400 bg-gray-900/50 px-3 py-1 rounded-full border border-gray-700/50 group-hover:border-gray-600">{{ $chapter->lessons->count() }} bài</span></span>
                                <svg :class="{'rotate-180': open}" class="w-6 h-6 transform transition-transform duration-300 text-gray-500 group-hover:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="open" x-collapse class="bg-gray-900/30" style="display: none;">
                                @if($chapter->lessons->count() > 0)
                                @foreach($chapter->lessons as $lesson)
                                @php
                                $canView = false;
                                $isTrial = ($loop->parent->first && $loop->first);
                                if ($isTrial) { $canView = true; } elseif (Auth::check()) {
                                $user = Auth::user();
                                if ($user->role === 'admin' || $user->id === $course->teacher_id || $course->enrollments->contains('user_id', $user->id)) { $canView = true; }
                                }
                                @endphp
                                @if($canView)
                                <a href="{{ route('lesson.show', [$course->id, $lesson->id]) }}" class="block w-full group/lesson">
                                    <div class="p-4 pl-6 border-t border-white/5 flex justify-between items-center hover:bg-green-900/10 transition cursor-pointer relative overflow-hidden">
                                        <div class="absolute inset-0 bg-green-400/5 opacity-0 group-hover/lesson:opacity-100 transition duration-300"></div>
                                        <div class="flex items-center gap-4 relative z-10">
                                            <div class="w-10 h-10 rounded-full bg-green-900/30 flex items-center justify-center group-hover/lesson:bg-green-600/80 transition duration-300 border border-green-500/20 group-hover/lesson:border-green-500/50">
                                                <svg class="w-5 h-5 text-green-400 group-hover/lesson:text-white transition duration-300 ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M8 5v14l11-7z" /></svg>
                                            </div>
                                            <span class="text-gray-200 group-hover/lesson:text-white font-medium text-lg transition duration-300">{{ $lesson->title }}</span>
                                        </div>
                                        <div class="relative z-10">
                                            @if($isTrial)
                                            <span class="text-xs font-bold bg-green-500/20 text-green-300 px-3 py-1.5 rounded-full border border-green-500/30 uppercase tracking-wider">Học thử</span>
                                            @else
                                            <span class="text-xs font-bold text-green-400 border border-green-500/30 px-3 py-1.5 rounded-full group-hover/lesson:bg-green-500/20 transition">Vào học</span>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                                @else
                                <div class="block w-full cursor-not-allowed select-none bg-gray-950/50">
                                    <div class="p-4 pl-6 border-t border-white/5 flex justify-between items-center opacity-40 grayscale">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-full bg-gray-800/50 flex items-center justify-center border border-gray-700/30">
                                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                </svg>
                                            </div>
                                            <span class="text-lg font-medium text-gray-500">{{ $lesson->title }}</span>
                                        </div>
                                        <div class="flex items-center"><span class="text-xs font-semibold bg-gray-800/80 text-gray-400 px-3 py-1.5 rounded-full border border-gray-700">🔒 Mua để mở</span></div>
                                    </div>
                                </div>
                                @endif
                                @endforeach
                                @else
                                <div class="p-6 text-gray-500 italic text-center">Chưa có bài giảng trong chương này.</div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="p-8 bg-yellow-900/20 text-yellow-200 rounded-2xl text-center border border-yellow-700/30 text-lg">Khóa học đang trong quá trình cập nhật nội dung. Vui lòng quay lại sau!</div>
                    @endif
                </div>

                {{-- 7. BÌNH LUẬN & HỎI ĐÁP --}}
                <div class="pt-10 border-t border-white/10">
                    <h3 class="text-3xl font-bold mb-8 text-white flex items-center"><span class="bg-indigo-500 w-2 h-8 mr-4 rounded-full"></span>Bình luận & Hỏi đáp</h3>

                    @auth
                    <div class="flex items-start gap-5 mb-10 bg-gray-800/40 p-6 rounded-2xl border border-white/10">
                        <div class="w-12 h-12 bg-indigo-600 rounded-full flex-shrink-0 flex items-center justify-center text-white font-bold uppercase border-2 border-indigo-400/50 text-lg shadow-md">{{ substr(Auth::user()->name, 0, 1) }}</div>
                        <div class="flex-grow">
                            <form action="{{ route('course.comment', $course) }}" method="POST">
                                @csrf
                                <textarea name="content" rows="4" class="w-full bg-gray-900/50 border-gray-700 text-white rounded-xl shadow-inner focus:border-indigo-500 focus:ring-indigo-500 focus:ring-opacity-50 placeholder-gray-500 p-4 transition" placeholder="Chia sẻ suy nghĩ hoặc câu hỏi của bạn về khóa học này..." required></textarea>
                                <div class="mt-4 flex justify-end">
                                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white px-8 py-3 rounded-xl font-bold transition shadow-lg hover:shadow-indigo-500/20 border border-indigo-500/50">Gửi bình luận</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @else
                    <div class="bg-gray-800/60 p-8 rounded-2xl text-center mb-10 border border-white/10 backdrop-blur-sm">
                        <p class="text-gray-300 text-lg">Vui lòng <a href="{{ route('login') }}" class="text-indigo-400 font-bold hover:text-indigo-300 hover:underline transition">đăng nhập</a> để tham gia thảo luận cùng giảng viên và các học viên khác.</p>
                    </div>
                    @endauth

                    <div class="space-y-8">
                        @forelse($comments as $comment)
                        <div class="flex gap-5 group transition duration-300">
                            <div class="flex-shrink-0 w-12 h-12 bg-gray-700/80 rounded-full flex items-center justify-center text-gray-300 font-bold uppercase text-sm border border-gray-600 group-hover:border-gray-500 transition shadow-sm">{{ substr($comment->user->name, 0, 1) }}</div>
                            <div class="flex-grow">
                                <div class="bg-gray-800/80 p-5 rounded-2xl rounded-tl-none border border-white/10 group-hover:border-gray-600/80 transition shadow-sm relative top-2">
                                    <div class="flex justify-between items-start mb-3">
                                        {{-- Tên và thời gian --}}
                                        <div>
                                            <h4 class="font-bold text-white text-lg">{{ $comment->user->name }}</h4>
                                            <span class="text-sm text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                        </div>

                                        {{-- 👇 LOGIC XÓA BÌNH LUẬN (CHÍNH CHỦ HOẶC ADMIN) --}}
                                        @if(Auth::check() && (Auth::id() === $comment->user_id || Auth::user()->role === 'admin'))
                                        <form action="{{ route('comment.destroy', $comment->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa bình luận này không?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-500 hover:text-red-500 transition p-1 rounded-md hover:bg-red-500/10" title="Xóa bình luận">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                    <p class="text-gray-300 leading-relaxed text-base">{{ $comment->content }}</p>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-12 text-gray-500 italic border-2 border-dashed border-gray-800 rounded-2xl">
                            <svg class="w-16 h-16 mx-auto text-gray-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            Chưa có bình luận nào. Hãy là người đầu tiên chia sẻ cảm nghĩ!
                        </div>
                        @endforelse
                        <div class="mt-8">{{ $comments->links() }}</div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
