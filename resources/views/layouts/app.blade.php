<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ObR Tracker - Municipality of Gerona</title>

    <link rel="stylesheet" href="{{ asset('build/assets/app-CyPD0-iE.css') }}?v=OFFLINE">

    <script src="{{ asset('build/assets/app-a6NN0lTC.js') }}?v=RADAR_ONLINE" defer></script>
</head>
<body class="bg-slate-50 font-sans text-slate-900 antialiased h-screen overflow-hidden flex flex-col relative">

    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden select-none opacity-[0.06] flex flex-wrap justify-center items-center gap-x-24 gap-y-40">
        @for ($i = 0; $i < 40; $i++)
            <div class="text-[5vw] font-black uppercase -rotate-12 tracking-widest whitespace-nowrap">
                Budget Office
            </div>
        @endfor
    </div>

    <header class="flex-none z-50 shadow-xl bg-white">
        <div class="bg-white border-b border-slate-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                    
                    <div class="flex items-center gap-5">
                        <div class="flex-shrink-0">
                            <img class="h-32 w-32 object-contain drop-shadow-md" src="{{ asset('3DMunicipalLogo.ico') }}" alt="Municipality of Gerona Logo">
                        </div>

                        <div class="flex flex-col justify-center text-left">
                            <span class="text-sm font-semibold text-slate-500 leading-tight">Republic of the Philippines</span>
                            <span class="text-sm font-semibold text-slate-500 leading-tight uppercase">Province of Tarlac</span>
                            <h1 class="text-3xl font-black text-slate-900 tracking-tight uppercase mt-1">Municipality of Gerona</h1>
                            <div class="mt-2">
                                <span class="text-sm font-bold text-blue-800 bg-blue-100 border border-blue-200 px-4 py-1.5 rounded-full shadow-sm">Budget Office</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex-shrink-0">
                        <div class="flex items-center gap-3 bg-slate-50 border border-slate-200 px-5 py-3 rounded-xl shadow-inner text-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div id="live-clock" class="text-lg font-mono font-bold text-slate-700">Loading time...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-hidden bg-slate-50 border-t border-slate-100 py-1.5">
            <div class="animate-marquee whitespace-nowrap text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                Municipality of Gerona Budget Office • ObR Tracking System • Serbisyong May Puso, Serbisiyong Totoo • 
                Municipality of Gerona Budget Office • ObR Tracking System • Serbisyong May Puso, Serbisiyong Totoo •
                Municipality of Gerona Budget Office • ObR Tracking System • Serbisyong May Puso, Serbisiyong Totoo •
            </div>
        </div>

        <div class="h-1 bg-blue-800 w-full"></div>
        <div class="h-1 bg-red-600 w-full"></div>
    </header>

    <main class="flex-grow overflow-y-auto z-10 relative p-6 sm:p-10 custom-scrollbar">
        <div class="max-w-7xl mx-auto flex flex-col items-center">
            @yield('content')
        </div>
    </main>

    <style>
        /* Prevents the "rubber-band" bounce effect in browsers */
        html, body {
            overscroll-behavior: none;
        }

        /* Styling the scrollbar for a cleaner look in Laragon */
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        @keyframes marquee {
            0% { transform: translateX(0%); }
            100% { transform: translateX(-33.3%); } 
        }
        .animate-marquee {
            display: inline-block;
            animation: marquee 30s linear infinite;
        }
    </style>

    <script>
        function updateTime() {
            const now = new Date();
            const options = { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' };
            document.getElementById('live-clock').innerText = now.toLocaleString('en-US', options);
        }
        setInterval(updateTime, 1000);
        updateTime();
    </script>
</body>
</html>