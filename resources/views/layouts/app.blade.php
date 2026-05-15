<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ObR Tracking System - Municipality of Gerona</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, sans-serif; }
        .watermark-text {
            color: rgba(226, 232, 240, 0.4);
            font-size: 120px;
            font-weight: 900;
            white-space: nowrap;
            transform: rotate(-15deg);
            pointer-events: none;
            user-select: none;
        }
    </style>
</head>
<body class="bg-slate-50 relative min-h-screen overflow-x-hidden flex flex-col">

    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none flex flex-col gap-32 pt-20">
        <div class="watermark-text -ml-40">BUDGET OFFICE &nbsp;&nbsp;&nbsp; BUDGET OFFICE &nbsp;&nbsp;&nbsp; BUDGET OFFICE</div>
        <div class="watermark-text ml-20">BUDGET OFFICE &nbsp;&nbsp;&nbsp; BUDGET OFFICE &nbsp;&nbsp;&nbsp; BUDGET OFFICE</div>
        <div class="watermark-text -ml-60">BUDGET OFFICE &nbsp;&nbsp;&nbsp; BUDGET OFFICE &nbsp;&nbsp;&nbsp; BUDGET OFFICE</div>
    </div>

    <header class="relative z-10 bg-white pb-4 pt-6 px-8 flex justify-between items-center shadow-sm">
        <div class="flex items-center gap-4">
            <img src="{{ asset('3DMunicipalLogo.ico') }}" alt="Municipality Logo" class="h-20 w-20 object-contain drop-shadow-md">
            <div>
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest leading-tight">Republic of the Philippines</p>
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest leading-tight mb-1">Province of Tarlac</p>
                <h1 class="text-3xl font-black text-[#002855] tracking-tight leading-none mb-2">MUNICIPALITY OF GERONA</h1>
                <span class="px-4 py-1 bg-blue-50 text-blue-600 border border-blue-200 rounded-full text-[11px] font-black uppercase tracking-widest shadow-sm">
                    Budget Office
                </span>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <div class="bg-slate-50 border border-slate-200 px-6 py-3 rounded-2xl shadow-sm flex items-center gap-3">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span id="realtime-clock" class="text-sm font-bold text-slate-700 tracking-wider font-mono"></span>
            </div>
        </div>
    </header>

    <div class="relative z-10 shadow-md flex flex-col">
        <div class="bg-slate-100 py-1.5 border-t border-slate-200">
            <marquee scrollamount="5" class="text-[10px] font-black text-slate-400 tracking-[0.2em] uppercase flex items-center">
                MUNICIPALITY OF GERONA BUDGET OFFICE • OBR TRACKING SYSTEM • SERBISYONG MAY PUSO, SERBISYONG TOTOO • MUNICIPALITY OF GERONA BUDGET OFFICE • OBR TRACKING SYSTEM • SERBISYONG MAY PUSO, SERBISYONG TOTOO • MUNICIPALITY OF GERONA BUDGET OFFICE
            </marquee>
        </div>
        <div class="w-full h-1.5 bg-[#1361b9]"></div>
        <div class="w-full h-1.5 bg-red-600"></div>
    </div>

    <main class="relative z-10 flex-grow flex flex-col items-center justify-start p-8">
        
        @if(request()->is('/'))
            <div class="w-full max-w-[90rem] mx-auto mt-10">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    
                    <a href="{{ url('/dashboard/pc1') }}" class="relative bg-white rounded-3xl p-8 shadow-xl border border-slate-100 flex flex-col items-center text-center transition-all duration-300 hover:shadow-2xl hover:-translate-y-1 group">
                        <span class="absolute -top-3 left-1/2 transform -translate-x-1/2 px-5 py-1.5 bg-blue-100 text-blue-600 border border-blue-200 rounded-full text-[10px] font-black uppercase tracking-widest shadow-sm">
                            Step 1
                        </span>
                        <div class="h-20 w-20 bg-blue-50 rounded-full flex items-center justify-center mb-6 mt-2 group-hover:scale-105 transition-transform duration-300 shadow-inner">
                            <svg class="h-9 w-9 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-black text-slate-800 mb-3 tracking-tight">Time Received</h3>
                        <p class="text-slate-500 text-xs font-medium leading-relaxed max-w-[200px]">
                            Log new incoming ObRs and initiate the document tracking lifecycle into the system.
                        </p>
                    </a>

                    <a href="{{ url('/dashboard/pc2') }}" class="relative bg-white rounded-3xl p-8 shadow-xl border border-slate-100 flex flex-col items-center text-center transition-all duration-300 hover:shadow-2xl hover:-translate-y-1 group">
                        <span class="absolute -top-3 left-1/2 transform -translate-x-1/2 px-5 py-1.5 bg-amber-100 text-amber-600 border border-amber-200 rounded-full text-[10px] font-black uppercase tracking-widest shadow-sm">
                            Step 2
                        </span>
                        <div class="h-20 w-20 bg-amber-50 rounded-full flex items-center justify-center mb-6 mt-2 group-hover:scale-105 transition-transform duration-300 shadow-inner">
                            <svg class="h-9 w-9 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12a7.5 7.5 0 0 0 15 0m-15 0a7.5 7.5 0 1 1 15 0m-15 0H3m16.5 0H21m-1.5 0H12m-8.457 3.077 1.41-.513m14.095-5.13 1.41-.513M5.106 17.785l1.15-.964m11.49-9.642 1.149-.964M7.501 19.79l.75-1.3m7.5-12.978.75-1.3m-6.063 16.658.26-1.477m2.605-14.772.26-1.477m0 17.726-.26-1.477M10.698 4.614l-.26-1.477M16.5 19.79l-.75-1.3M7.5 4.614l-.75-1.3m-1.372 15.137-1.15-.964m14.095-11.57-1.15-.964m-15.535 5.568-1.41-.513m16.915-4.104-1.41-.513" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-black text-slate-800 mb-3 tracking-tight">Analyze & Control</h3>
                        <p class="text-slate-500 text-xs font-medium leading-relaxed max-w-[200px]">
                            Process documents, review data, and input specific control details.
                        </p>
                    </a>

                    <a href="{{ url('/dashboard/pc3') }}" class="relative bg-white rounded-3xl p-8 shadow-xl border border-slate-100 flex flex-col items-center text-center transition-all duration-300 hover:shadow-2xl hover:-translate-y-1 group">
                        <span class="absolute -top-3 left-1/2 transform -translate-x-1/2 px-5 py-1.5 bg-emerald-100 text-emerald-600 border border-emerald-200 rounded-full text-[10px] font-black uppercase tracking-widest shadow-sm">
                            Step 3
                        </span>
                        <div class="h-20 w-20 bg-emerald-50 rounded-full flex items-center justify-center mb-6 mt-2 group-hover:scale-105 transition-transform duration-300 shadow-inner">
                            <svg class="h-9 w-9 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-black text-slate-800 mb-3 tracking-tight">Checked & Clarified</h3>
                        <p class="text-slate-500 text-xs font-medium leading-relaxed max-w-[200px]">
                            Final document review, adding remarks, and assigning appropriate signatories.
                        </p>
                    </a>

                    <a href="{{ url('/dashboard/pc4') }}" class="relative bg-white rounded-3xl p-8 shadow-xl border border-slate-100 flex flex-col items-center text-center transition-all duration-300 hover:shadow-2xl hover:-translate-y-1 group">
                        <span class="absolute -top-3 left-1/2 transform -translate-x-1/2 px-5 py-1.5 bg-rose-100 text-rose-600 border border-rose-200 rounded-full text-[10px] font-black uppercase tracking-widest shadow-sm">
                            Step 4
                        </span>
                        <div class="h-20 w-20 bg-rose-50 rounded-full flex items-center justify-center mb-6 mt-2 group-hover:scale-105 transition-transform duration-300 shadow-inner">
                            <svg class="h-9 w-9 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-black text-slate-800 mb-3 tracking-tight">Final Release & Lock</h3>
                        <p class="text-slate-500 text-xs font-medium leading-relaxed max-w-[200px]">
                            Perform the official system locks, compile final durations, and complete the tracking lifecycle.
                        </p>
                    </a>

                </div>
            </div>

        @else
            @yield('content')
        @endif
        
    </main>

    <footer class="relative z-0 w-full py-0 mt-auto text-center bg-white shadow-[0_0px_0px_0px_rgba(0,0,0,0.0)]">
        <p class="text-[10px] font-bold text-slate-300 uppercase tracking-widest">
            ObR Tracking System Developed by <span class="text-slate-300 font-black">Luke Reed Chard R. Mendoza</span>
        </p>
    </footer>

    <script>
        function updateClock() {
            const now = new Date();
            const options = { 
                weekday: 'short', 
                month: 'short', 
                day: 'numeric', 
                year: 'numeric', 
                hour: 'numeric', 
                minute: '2-digit', 
                second: '2-digit' 
            };
            const clockEl = document.getElementById('realtime-clock');
            if (clockEl) clockEl.textContent = now.toLocaleString('en-US', options);
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>
</html>