<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Expired - ObR Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 h-screen flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-3xl shadow-2xl text-center max-w-md w-full border border-slate-200">
        
        <div class="mx-auto h-20 w-20 bg-rose-100 rounded-full flex items-center justify-center mb-6 animate-pulse">
            <svg class="h-10 w-10 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>

        <h2 class="text-3xl font-black text-slate-800 mb-3">Network Hiccup</h2>
        <p class="text-slate-500 mb-8 font-medium">
            Your connection went to sleep and your security token expired to protect the system. Don't worry, just click below to reconnect!
        </p>

        <a href="{{ url()->previous() }}" class="inline-flex w-full justify-center items-center px-6 py-4 bg-emerald-600 hover:bg-emerald-700 text-white text-lg font-black rounded-xl shadow-lg hover:shadow-emerald-500/30 transition-all active:scale-95 gap-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Refresh & Try Again
        </a>

        <div class="mt-6">
            <a href="{{ url('/') }}" class="text-sm text-slate-400 hover:text-slate-600 font-medium transition-colors">
                Or return to Main Menu
            </a>
        </div>
    </div>
</body>
</html>