<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Hồ sơ cá nhân') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- 1. CẬP NHẬT THÔNG TIN CÁ NHÂN --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- 2. ĐỔI MẬT KHẨU --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- 3. [MỚI] THÔNG TIN NGÂN HÀNG (Chỉ Admin & Teacher thấy) --}}
            @if(Auth::user()->role === 'teacher' || Auth::user()->role === 'admin')
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ __('Thông tin Ngân hàng') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                {{ __('Cập nhật tài khoản ngân hàng để nhận thanh toán lương hằng tháng.') }}
                            </p>
                        </header>

                        <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
                            @csrf
                            @method('patch')

                            {{-- Tên Ngân Hàng --}}
                            <div>
                                <x-input-label for="bank_name" :value="__('Tên Ngân hàng')" />
                                <select id="bank_name" name="bank_name" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="" disabled {{ old('bank_name', $user->bank_name) ? '' : 'selected' }}>Chọn ngân hàng</option>
                                    @foreach(['Vietcombank', 'MBBank', 'Techcombank', 'ACB', 'BIDV', 'VPBank', 'TPBank', 'Agribank'] as $bank)
                                    <option value="{{ $bank }}" {{ old('bank_name', $user->bank_name) === $bank ? 'selected' : '' }}>
                                        {{ $bank }}
                                    </option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('bank_name')" />
                            </div>

                            {{-- Số Tài Khoản --}}
                            <div>
                                <x-input-label for="bank_account_number" :value="__('Số tài khoản')" />
                                <x-text-input id="bank_account_number" name="bank_account_number" type="text" class="mt-1 block w-full" :value="old('bank_account_number', $user->bank_account_number)" required placeholder="VD: 4904457868" />
                                <x-input-error class="mt-2" :messages="$errors->get('bank_account_number')" />
                            </div>

                            {{-- Tên Chủ Tài Khoản --}}
                            <div>
                                <x-input-label for="bank_account_name" :value="__('Chủ tài khoản (In hoa không dấu)')" />
                                <x-text-input id="bank_account_name" name="bank_account_name" type="text" class="mt-1 block w-full uppercase" :value="old('bank_account_name', $user->bank_account_name)" required placeholder="VD: NGUYEN VAN A" />
                                <x-input-error class="mt-2" :messages="$errors->get('bank_account_name')" />
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button>{{ __('Lưu thông tin') }}</x-primary-button>

                                @if (session('status') === 'profile-updated')
                                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-gray-600 dark:text-gray-400">{{ __('Đã lưu.') }}</p>
                                @endif
                            </div>
                        </form>
                    </section>
                </div>
            </div>
            @endif

            {{-- 4. XÓA TÀI KHOẢN --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
