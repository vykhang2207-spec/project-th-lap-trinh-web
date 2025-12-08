<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Chỉnh sửa khóa học: ') }} {{ $course->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">

                {{-- Form Sửa Khóa học --}}
                <form method="POST" action="{{ route('teacher.courses.update', $course) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT') {{-- BẮT BUỘC: Sử dụng method spoofing để gọi hàm update() --}}

                    <div class="space-y-6">
                        {{-- Tiêu đề Khóa học --}}
                        <div>
                            <x-input-label for="title" :value="__('Tên Khóa học')" />
                            {{-- Đổ dữ liệu cũ vào form --}}
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" value="{{ old('title', $course->title) }}" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                        </div>

                        {{-- Mô tả --}}
                        <div>
                            <x-input-label for="description" :value="__('Mô tả chi tiết')" />
                            <textarea id="description" name="description" rows="5" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>{{ old('description', $course->description) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        {{-- Giá tiền và Hình ảnh (Cùng hàng) --}}
                        <div class="flex space-x-4">
                            <div class="w-1/2">
                                <x-input-label for="price" :value="__('Giá tiền (VNĐ)')" />
                                <x-text-input id="price" name="price" type="number" step="1000" min="0" class="mt-1 block w-full" value="{{ old('price', $course->price) }}" required />
                                <x-input-error class="mt-2" :messages="$errors->get('price')" />
                            </div>

                            <div class="w-1/2">
                                <x-input-label for="image" :value="__('Ảnh đại diện (Để trống nếu không thay đổi)')" />
                                <input id="image" name="image" type="file" class="mt-1 block w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm p-2" accept="image/*" />

                                {{-- Hiển thị ảnh hiện tại --}}
                                @if ($course->image_path)
                                <p class="text-sm text-gray-500 mt-2">Ảnh hiện tại:</p>
                                <img src="{{ Str::startsWith($course->image_path, 'http') ? $course->image_path : asset('storage/' . $course->image_path) }}" class="w-20 h-20 object-cover rounded mt-1">
                                @endif

                                <x-input-error class="mt-2" :messages="$errors->get('image')" />
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Lưu Thay Đổi') }}</x-primary-button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
