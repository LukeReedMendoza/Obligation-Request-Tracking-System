@extends('layouts.app')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto mt-10">
    
    <a href="{{ url('/dashboard/pc1') }}" class="group relative bg-white rounded-[2rem] p-10 text-center shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-slate-100 flex flex-col items-center justify-center">
        <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 bg-blue-100 text-blue-700 border border-blue-200 px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-[0.2em] shadow-sm">
            Step 1
        </div>
        
        <div id="pulse-pc1" class="absolute top-5 right-5 hidden items-center gap-1.5 bg-blue-50 px-3 py-1 rounded-full border border-blue-100 shadow-sm transition-all duration-300">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
            </span>
            <span class="text-[8px] font-black text-blue-600 uppercase tracking-widest">In Use</span>
        </div>

        <div class="w-20 h-20 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center text-3xl font-black mb-6 group-hover:scale-110 transition-transform">
            ⏱️
        </div>
        <h3 class="text-2xl font-black text-slate-800 tracking-tight mb-3">Time Received</h3>
        <p class="text-slate-500 text-sm font-medium leading-relaxed px-4">
            Log new incoming ObRs and perform the final system locks.
        </p>
    </a>

    <a href="{{ url('/dashboard/pc2') }}" class="group relative bg-white rounded-[2rem] p-10 text-center shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-slate-100 flex flex-col items-center justify-center">
        <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 bg-amber-100 text-amber-700 border border-amber-200 px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-[0.2em] shadow-sm">
            Step 2
        </div>

        <div id="pulse-pc2" class="absolute top-5 right-5 hidden items-center gap-1.5 bg-amber-50 px-3 py-1 rounded-full border border-amber-100 shadow-sm transition-all duration-300">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
            </span>
            <span class="text-[8px] font-black text-amber-600 uppercase tracking-widest">In Use</span>
        </div>

        <div class="w-20 h-20 bg-amber-50 text-amber-500 rounded-full flex items-center justify-center text-3xl font-black mb-6 group-hover:scale-110 transition-transform">
            ⚙️
        </div>
        <h3 class="text-2xl font-black text-slate-800 tracking-tight mb-3">Analyze & Control</h3>
        <p class="text-slate-500 text-sm font-medium leading-relaxed px-4">
            Process documents, review data, and input control details.
        </p>
    </a>

    <a href="{{ url('/dashboard/pc3') }}" class="group relative bg-white rounded-[2rem] p-10 text-center shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-slate-100 flex flex-col items-center justify-center">
        <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 bg-emerald-100 text-emerald-700 border border-emerald-200 px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-[0.2em] shadow-sm">
            Step 3
        </div>

        <div id="pulse-pc3" class="absolute top-5 right-5 hidden items-center gap-1.5 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-100 shadow-sm transition-all duration-300">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            <span class="text-[8px] font-black text-emerald-600 uppercase tracking-widest">In Use</span>
        </div>

        <div class="w-20 h-20 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center text-3xl font-black mb-6 group-hover:scale-110 transition-transform">
            ✅
        </div>
        <h3 class="text-2xl font-black text-slate-800 tracking-tight mb-3">Checked & Clarified</h3>
        <p class="text-slate-500 text-sm font-medium leading-relaxed px-4">
            Final document review, adding remarks, and assigning signatories.
        </p>
    </a>

</div>

<script type="module">
    if (window.Echo) {
        console.log("🟢 Echo is loaded! Connecting to Reverb...");

        window.Echo.join('office-presence')
            .here((users) => {
                console.log("👥 People currently in the office:", users);
                users.forEach(user => {
                    let badge = document.getElementById('pulse-' + user.role);
                    if (badge) {
                        badge.classList.remove('hidden');
                        badge.classList.add('flex');
                    }
                });
            })
            .joining((user) => {
                console.log("👋 Someone just walked in:", user);
                let badge = document.getElementById('pulse-' + user.role);
                if (badge) {
                    badge.classList.remove('hidden');
                    badge.classList.add('flex');
                }
            })
            .leaving((user) => {
                console.log("🚪 Someone just left:", user);
                let badge = document.getElementById('pulse-' + user.role);
                if (badge) {
                    badge.classList.add('hidden');
                    badge.classList.remove('flex');
                }
            })
            .error((error) => {
                console.error("❌ REVERB CONNECTION ERROR:", error);
            });
    } else {
        console.error("🔴 window.Echo is MISSING! Your Vite Javascript isn't loading.");
    }
</script>
@endsection