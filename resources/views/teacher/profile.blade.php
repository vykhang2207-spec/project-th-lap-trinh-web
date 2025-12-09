<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Hồ sơ Giảng viên') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- THÔNG TIN GIẢNG VIÊN --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-8 flex items-center">

                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $teacher->name }}</h1>
                    <p class="text-gray-500 dark:text-gray-400 mt-1">{{ $teacher->email }}</p>
                    <div class="mt-3 inline-flex items-center px-3 py-1 rounded-full bg-indigo-100 text-indigo-800 text-sm font-semibold">
                        📚 {{ $courses->total() }} khóa học đã xuất bản
                    </div>
                </div>
            </div>

            {{-- DANH SÁCH KHÓA HỌC (Copy logic từ Welcome) --}}
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Các khóa học của {{ $teacher->name }}</h3>

            @if($courses->isEmpty())
            <p class="text-gray-500">Giảng viên này chưa có khóa học nào công khai.</p>
            @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach ($courses as $course)
                {{-- (Code hiển thị Card khóa học - GIỐNG TRANG HOME - Bạn có thể copy y nguyên card từ welcome.blade.php qua đây) --}}
                {{-- Để ngắn gọn mình ví dụ cấu trúc cơ bản: --}}
                <div class="bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-lg overflow-hidden shadow hover:shadow-lg transition">
                    <a href="{{ route('course.show', $course) }}">
                        <img src="{{ Str::startsWith($course->image_path, 'http') ? $course->image_path : asset('storage/' . $course->image_path) }}" class="w-full h-40 object-cover">
                    </a>
                    <div class="p-4">
                        <h4 class="font-bold mb-2 text-white"><a href="{{ route('course.show', $course) }}">{{ $course->title }}</a></h4>
                        <div class="text-red-600 font-bold">{{ number_format($course->price) }} đ</div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-6">{{ $courses->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
