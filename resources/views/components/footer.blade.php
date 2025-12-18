<footer class="bg-white dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800 pt-16 pb-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">

            {{-- Cột 1: Logo & Giới thiệu --}}
            <div class="space-y-4">
                <div class="flex items-center gap-2">
                    <x-application-logo class="w-10 h-10 fill-current text-indigo-600" />
                    <span class="text-xl font-bold text-gray-900 dark:text-white">CourseMaster</span>
                </div>
                <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                    Nền tảng học lập trình trực tuyến hàng đầu. Chúng tôi cung cấp các khóa học thực chiến giúp bạn từ con số 0 trở thành chuyên gia.
                </p>
                {{-- Social Icons --}}
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-400 hover:text-indigo-600 transition">
                        <span class="sr-only">Facebook</span>
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" /></svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-indigo-600 transition">
                        <span class="sr-only">GitHub</span>
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" /></svg>
                    </a>
                </div>
            </div>

            {{-- Cột 2: Khám phá --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider mb-4">Khám phá</h3>
                <ul class="space-y-3">
                    <li><a href="#" class="text-base text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400">Khóa học mới</a></li>
                    <li><a href="#" class="text-base text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400">Chủ đề nổi bật</a></li>
                    <li><a href="#" class="text-base text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400">Blog lập trình</a></li>
                    <li><a href="#" class="text-base text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400">Lộ trình học</a></li>
                </ul>
            </div>

            {{-- Cột 3: Hỗ trợ --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider mb-4">Hỗ trợ</h3>
                <ul class="space-y-3">
                    <li><a href="#" class="text-base text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400">Trung tâm trợ giúp</a></li>
                    <li><a href="#" class="text-base text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400">Điều khoản sử dụng</a></li>
                    <li><a href="#" class="text-base text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400">Chính sách bảo mật</a></li>
                    <li><a href="#" class="text-base text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400">Liên hệ hợp tác</a></li>
                </ul>
            </div>

            {{-- Cột 4: Đăng ký nhận tin --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider mb-4">Đăng ký nhận tin</h3>
                <p class="text-gray-500 dark:text-gray-400 text-sm mb-4">Nhận thông báo về khóa học mới và ưu đãi đặc biệt.</p>
                <form class="flex flex-col gap-2">
                    <input type="email" placeholder="Email của bạn" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <button type="button" class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold transition">Đăng ký</button>
                </form>
            </div>
        </div>

        <div class="border-t border-gray-100 dark:border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-base text-gray-400 text-center md:text-left">
                &copy; {{ date('Y') }} CourseMaster. All rights reserved.
            </p>

        </div>
    </div>
</footer>
