<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cổng thanh toán MoMo (Giả lập)</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">

    <div class="bg-white rounded-xl shadow-2xl overflow-hidden max-w-4xl w-full flex flex-col md:flex-row">
        {{-- Cột Trái: Thông tin đơn hàng --}}
        <div class="bg-[#A50064] p-8 md:w-1/2 text-white flex flex-col justify-between">
            <div>
                <img src="https://upload.wikimedia.org/wikipedia/vi/f/fe/MoMo_Logo.png" class="w-16 mb-6 bg-white rounded p-1">
                <h2 class="text-2xl font-bold mb-2">Thanh toán qua Ví MoMo</h2>
                <p class="opacity-80">Mô phỏng môi trường Sandbox</p>
            </div>

            <div class="mt-8 space-y-4">
                <div>
                    <p class="text-sm opacity-70 uppercase tracking-wide">Đơn hàng</p>
                    <p class="font-medium text-lg">{{ $orderInfo }}</p>
                </div>
                <div>
                    <p class="text-sm opacity-70 uppercase tracking-wide">Số tiền</p>
                    <p class="font-bold text-3xl">{{ number_format($amount) }} đ</p>
                </div>
            </div>

            <div class="mt-8 text-xs opacity-60">
                Lưu ý: Đây là giao diện giả lập phục vụ mục đích học tập/đồ án. Không có tiền thật bị trừ.
            </div>
        </div>

        {{-- Cột Phải: Quét mã QR --}}
        <div class="p-8 md:w-1/2 flex flex-col items-center justify-center bg-white">
            <h3 class="text-gray-800 font-bold text-xl mb-4">Quét mã để thanh toán</h3>

            <div class="border-2 border-[#A50064] p-2 rounded-lg mb-6 relative group">
                {{-- Ảnh QR Code giả --}}
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=MomoSimulationPayment" alt="QR Code" class="w-48 h-48">

                {{-- Hiệu ứng quét --}}
                <div class="absolute top-0 left-0 w-full h-1 bg-[#A50064] shadow-[0_0_10px_#A50064] animate-[scan_2s_infinite]"></div>
            </div>

            <p class="text-gray-500 text-center text-sm mb-8">
                Sử dụng App <strong>MoMo</strong> hoặc ứng dụng Camera hỗ trợ QR code để quét mã.
            </p>

            <div class="w-full space-y-3">
                {{-- Nút Thành công --}}
                <form action="{{ route('momo.simulation.success') }}" method="POST">
                    @csrf
                    <input type="hidden" name="redirectUrl" value="{{ $redirectUrl }}">
                    <button type="submit" class="w-full bg-[#A50064] hover:bg-[#8d0055] text-white font-bold py-3 px-4 rounded transition">
                        XÁC NHẬN THANH TOÁN (Thành công)
                    </button>
                </form>

                {{-- Nút Thất bại --}}
                <form action="{{ route('momo.simulation.cancel') }}" method="POST">
                    @csrf
                    <input type="hidden" name="redirectUrl" value="{{ $redirectUrl }}">
                    <button type="submit" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-3 px-4 rounded transition">
                        HỦY GIAO DỊCH (Thất bại)
                    </button>
                </form>
            </div>
        </div>
    </div>

    <style>
        @keyframes scan {
            0% {
                top: 0%;
            }

            50% {
                top: 100%;
            }

            100% {
                top: 0%;
            }
        }

    </style>
</body>
</html>
