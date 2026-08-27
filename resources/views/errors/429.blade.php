<!DOCTYPE html>
<html lang="id" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Account Terblokir - 429 Too Many Requests</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#0f111a] flex items-center justify-center min-h-screen font-sans text-gray-100 antialiased selection:bg-red-500/30">
    <div class="text-center flex flex-col items-center gap-6 px-4">

        <!-- Octagon Icon with Glow -->
        <div class="relative flex items-center justify-center w-24 h-24">
            <!-- Glow behind -->
            <div class="absolute inset-0 bg-[#b94242] rounded-full blur-xl opacity-30"></div>
            <!-- Octagon SVG -->
            <svg class="w-full h-full text-[#b94242] drop-shadow-2xl relative z-10" viewBox="0 0 24 24" fill="currentColor">
                <path d="M9.82 2.37c1.34-.78 2.98-.78 4.32 0l4.98 2.89c1.34.78 2.16 2.22 2.16 3.77v5.92c0 1.55-.82 2.99-2.16 3.77l-4.98 2.89c-1.34.78-2.98.78-4.32 0l-4.98-2.89C3.5 17.94 2.68 16.5 2.68 14.95V9.03c0-1.55.82-2.99 2.16-3.77l4.98-2.89z" />
                <!-- Exclamation point inside the octagon -->
                <path d="M11.25 7h1.5v5.5h-1.5V7zm0 7.5h1.5v1.5h-1.5v-1.5z" fill="white" />
            </svg>
        </div>

        <div class="space-y-3 mt-4">
            <h1 class="text-2xl sm:text-3xl font-semibold tracking-wide text-white">Account Anda Telah Terblokir</h1>
            <p class="text-[#848d9a] tracking-wide">Anda telah merigirim terlalu banyak permintaan</p>
        </div>

        <button onclick="window.history.back()" class="mt-4 px-6 py-2.5 bg-[#1c2130] hover:bg-[#252b3e] border border-gray-700/50 text-gray-300 text-sm font-medium rounded-xl transition-colors shadow-sm">
            hubungi admin
        </button>
    </div>
</body>

</html>