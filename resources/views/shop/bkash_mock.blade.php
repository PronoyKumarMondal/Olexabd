<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>bKash Payment (Sandbox)</title>
    <!-- Tailwind CSS (CDN for speed in this standalone page) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .bkash-pink { background-color: #E2136E; }
        .bkash-pink-text { color: #E2136E; }
        .bkash-border:focus { border-color: #E2136E; ring-color: #E2136E; }
    </style>
</head>
<body class="bkash-pink min-h-screen flex items-center justify-center font-sans">
    
    <div class="bg-white p-8 rounded-lg shadow-2xl w-full max-w-md text-center transform transition-all hover:scale-105 duration-300">
        <div class="mb-6">
            <img src="https://logos-download.com/wp-content/uploads/2022/01/BKash_Logo_icon.png" alt="bKash" class="h-16 mx-auto">
        </div>
        
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Merchant Payment</h2>
        <p class="text-gray-500 mb-6">Order ID: <strong>#{{ $order_id }}</strong></p>
        
        <div class="bg-gray-100 p-4 rounded-md mb-6">
            <div class="flex justify-between mb-2">
                <span class="text-gray-600">Merchant</span>
                <span class="font-semibold">Appliance Store</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Amount</span>
                <span class="font-bold text-xl">৳ {{ $amount }}</span>
            </div>
        </div>

        <form action="{{ route('bkash.success') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="order_id_db" value="{{ $order_id_db ?? '' }}">
            
            <div>
                <input type="text" placeholder="Enter bKash Account Number" class="w-full px-4 py-3 rounded border border-gray-300 focus:outline-none focus:border-[#E2136E] focus:ring-1 focus:ring-[#E2136E]" required>
            </div>
            
            <button type="submit" class="w-full bg-[#E2136E] text-white font-bold py-3 rounded hover:bg-[#c1105e] transition duration-200">
                Confirm Payment
            </button>
        </form>
        
        <div class="mt-4 text-xs text-gray-400">
            This is a sandbox simulation. No real money will be deducted.
        </div>
    </div>

</body>
</html>
