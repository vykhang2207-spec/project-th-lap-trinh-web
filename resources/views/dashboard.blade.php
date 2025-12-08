<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Khóa học của tôi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if($myCourses->isEmpty())
                    <div class="text-center py-10">
                        <p class="text-gray-500 mb-4">Bạn chưa đăng ký khóa học nào.</p>
                        <a href="{{ route('home') }}" class="inline-block bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                            Khám phá khóa học ngay
                        </a>
                    </div>
                    @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @foreach($myCourses as $enrollment)
                        {{-- Biến $enrollment->course chính là thông tin khóa học --}}
                        <div class="border dark:border-gray-700 rounded-lg overflow-hidden hover:shadow-lg transition duration-300 bg-white dark:bg-gray-900 flex flex-col h-full">

                            <a href="{{ route('course.show', $enrollment->course) }}" class="block relative group">
                                {{-- Ảnh khóa học --}}
                                <img src="{{ Str::startsWith($enrollment->course->image_path, 'http') ? $enrollment->course->image_path : asset('storage/' . $enrollment->course->image_path) }}" alt="{{ $enrollment->course->title }}" class="w-full h-40 object-cover group-hover:opacity-90 transition">

                                {{-- Nút Play hiện lên khi hover --}}
                                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition duration-300 bg-black/40">
                                    <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" /></svg>
                                </div>
                            </a>

                            <div class="p-4 flex flex-col flex-1">
                                <h3 class="font-bold text-lg mb-2 line-clamp-2 min-h-[3.5rem]">
                                    <a href="{{ route('course.show', $enrollment->course) }}">
                                        {{ $enrollment->course->title }}
                                    </a>
                                </h3>

                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                    GV: {{ $enrollment->course->teacher->name }}
                                </p>

                                {{-- Progress Bar (Giả lập) --}}
                                <div class="mt-auto">
                                    <div class="flex justify-between text-xs mb-1">
                                        <span>Tiến độ</span>
                                        <span class="font-bold text-indigo-600">0%</span> {{-- Sau này sẽ tính thật --}}
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                        <div class="bg-indigo-600 h-2.5 rounded-full" style="width: 0%"></div>
                                    </div>

                                    <a href="{{ route('course.show', $enrollment->course) }}" class="mt-4 block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold py-2 px-4 rounded transition">
                                        Vào học
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
