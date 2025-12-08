<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Thanh toán đơn hàng') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-8">

                {{-- CỘT TRÁI: THÔNG TIN ĐƠN HÀNG --}}
                <div class="w-full lg:w-2/3 bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">Thông tin khóa học</h3>
                    <div class="flex gap-4 border-b dark:border-gray-700 pb-4">
                        <img src="{{ Str::startsWith($course->image_path, 'http') ? $course->image_path : asset('storage/' . $course->image_path) }}" class="w-32 h-20 object-cover rounded-md">
                        <div>
                            <h4 class="font-semibold text-gray-800 dark:text-gray-200">{{ $course->title }}</h4>
                            <p class="text-sm text-gray-500">Giảng viên: {{ $course->teacher->name }}</p>
                        </div>
                    </div>

                    <div class="mt-4 flex justify-between items-center text-gray-900 dark:text-white">
                        <span>Giá gốc:</span>
                        <span class="line-through text-gray-500">{{ number_format($course->price * 1.2) }} đ</span>
                    </div>
                    <div class="flex justify-between items-center text-xl font-bold text-red-600 mt-2">
                        <span>Thành tiền:</span>
                        <span>{{ number_format($course->price) }} VNĐ</span>
                    </div>
                </div>

                {{-- CỘT PHẢI: PHƯƠNG THỨC THANH TOÁN --}}
                <div class="w-full lg:w-1/3 bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">Chọn phương thức thanh toán</h3>

                    <form action="{{ route('payment.process', $course) }}" method="POST">
                        @csrf

                        {{-- Option Momo --}}
                        <label class="flex items-center gap-3 p-3 border dark:border-gray-700 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 mb-3 has-[:checked]:border-pink-500 has-[:checked]:bg-pink-50 dark:has-[:checked]:bg-gray-900">
                            <input type="radio" name="payment_method" value="momo" checked class="text-pink-600 focus:ring-pink-500">
                            <div class="flex items-center gap-2">
                                <img src="https://upload.wikimedia.org/wikipedia/vi/f/fe/MoMo_Logo.png" class="w-8 h-8 rounded">
                                <span class="font-medium text-gray-900 dark:text-white">Ví điện tử MoMo</span>
                            </div>
                        </label>

                        {{-- Option Chuyển khoản (Ví dụ) --}}
                        <label class="flex items-center gap-3 p-3 border dark:border-gray-700 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 mb-6">
                            <input type="radio" name="payment_method" value="bank" class="text-indigo-600 focus:ring-indigo-500">
                            <div class="flex items-center gap-2">
                                <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                                <span class="font-medium text-gray-900 dark:text-white">Chuyển khoản ngân hàng</span>
                            </div>
                        </label>

                        <button type="submit" class="w-full bg-pink-600 hover:bg-pink-700 text-white font-bold py-3 px-4 rounded-lg transition">
                            THANH TOÁN {{ number_format($course->price) }} Đ
                        </button>

                        <p class="text-xs text-center text-gray-500 mt-4">
                            Bằng việc thanh toán, bạn đồng ý với điều khoản sử dụng của chúng tôi.
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
