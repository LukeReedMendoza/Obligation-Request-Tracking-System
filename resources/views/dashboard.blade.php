@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="{{ asset('flatpickr.min.css') }}">
    <style>
        /* MODERN EMERALD THEME */
        .flatpickr-calendar { 
            background: #ffffff !important;
            border-radius: 1.5rem !important; 
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.2) !important; 
            border: 1px solid #e2e8f0 !important;
            padding: -10px;
            z-index: 99999 !important;
        }
        .flatpickr-day.selected, .flatpickr-day.selected:hover { 
            background: #059669 !important; 
            border-color: #059669 !important; 
            color: white !important;
            font-weight: 800 !important;
            border-radius: 12px !important;
        }
        .flatpickr-day.today {
            border-color: #10b981 !important; 
            color: #065f46 !important;
            font-weight: 900 !important;
            background: #ecfdf5 !important;
        }
        .flatpickr-day.today:hover { background: #d1fae5 !important; }
        .flatpickr-months .flatpickr-month { color: #1e293b !important; font-weight: 800 !important; }
        .flatpickr-weekday { color: #94a3b8 !important; font-weight: 700 !important; text-transform: uppercase; font-size: 10px !important; }
    </style>

    @if(session('success'))
        <div class="mb-6 px-4 py-3 bg-green-100 text-green-800 rounded-lg w-full max-w-4xl font-medium text-center shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 px-4 py-3 bg-red-100 text-red-800 border-2 border-red-200 rounded-lg w-full max-w-4xl font-medium shadow-sm">
            <div class="flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <span><strong>Entry Failed:</strong> Please fix the errors below.</span>
            </div>
            <ul class="mt-2 text-sm list-disc list-inside text-left w-fit mx-auto">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($role == 'pc1')
    <div class="w-full max-w-4xl bg-white rounded-2xl shadow-lg border border-slate-100 p-12 mb-10 text-center">
        <h2 class="text-3xl font-black text-slate-800 mb-2">New Entry Station</h2>
        <p class="text-slate-500 mb-8 font-medium">Click the button below to officially "Time In" a new Obligation Request.</p>
        
        <button onclick="toggleModal('obrModal')" class="bg-blue-600 hover:bg-blue-700 text-white text-xl font-black py-5 px-16 rounded-2xl shadow-xl hover:shadow-2xl transition-all active:scale-95 transform">
            TIME IN (START)
        </button>
    </div>

    <div id="obrModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="toggleModal('obrModal')"></div>
        
        <div class="relative bg-white w-full max-w-md rounded-3xl shadow-2xl overflow-hidden transform transition-all">
            <div class="bg-blue-600 p-6 text-center text-white">
                <h3 class="text-xl font-bold">Log New Request</h3>
                <p class="text-blue-100 text-xs mt-1 uppercase tracking-widest font-bold">Station: PC 1 Entry</p>
            </div>
            
            <form action="{{ route('obr.store') }}" method="POST" class="p-8">
                @csrf
                <input type="hidden" name="obr_date" value="{{ now()->format('Y-m-d') }}">

                <div class="mb-6">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Enter ObR Number</label>
                    <input type="text" name="obr_number" id="obr_input" placeholder="e.g. 2026-0001" required autofocus
                           class="w-full px-5 py-4 rounded-xl border-2 border-slate-100 bg-slate-50 text-2xl font-black text-center focus:border-blue-500 focus:ring-0 outline-none transition-all">
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="toggleModal('obrModal')" 
                            class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-4 rounded-xl transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="flex-[2] bg-blue-600 hover:bg-blue-700 text-white font-black py-4 rounded-xl shadow-lg shadow-blue-200 transition-all active:scale-95">
                        CONFIRM & LOG
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <div class="w-full max-w-7xl">
        <div class="flex flex-col md:flex-row justify-between items-center mb-12 border-b border-slate-200 pb-6 gap-4">
            <h2 class="text-2xl font-black text-slate-800 tracking-tight italic">Active ObR Tracking List</h2>
            
            <div class="flex flex-wrap gap-4 items-center justify-center">
                <div class="flex items-center bg-gradient-to-r from-emerald-500 to-teal-600 px-5 py-2.5 rounded-xl shadow-md border border-emerald-400/20">
                    <span class="text-[10px] font-black uppercase tracking-widest text-emerald-50 mr-3 opacity-80">Completed Today:</span>
                    <span class="text-xl font-black text-white leading-none">{{ $todayCompleted ?? 0 }}</span>
                </div>

                <form action="{{ route('obr.export') }}" method="GET" class="flex items-center gap-1 bg-white p-1 rounded-2xl shadow-sm border border-slate-200">
                    <div class="flex items-center bg-slate-50 rounded-xl px-3 py-1 border border-transparent focus-within:border-emerald-500 transition-all">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-tighter mr-2">From</span>
                        <input type="text" id="start_date" name="start_date" placeholder="Select Date" 
                               class="text-xs bg-transparent border-none focus:ring-0 p-1 w-24 text-slate-700 font-bold cursor-pointer">
                    </div>
                    
                    <div class="flex items-center bg-slate-50 rounded-xl px-3 py-1 border border-transparent focus-within:border-emerald-500 transition-all">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-tighter mr-2">To</span>
                        <input type="text" id="end_date" name="end_date" placeholder="Select Date" 
                               class="text-xs bg-transparent border-none focus:ring-0 p-1 w-24 text-slate-700 font-bold cursor-pointer">
                    </div>

                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest shadow-md shadow-emerald-200 transition-all active:scale-95 flex items-center gap-2">
                        <span>📊</span> Export
                    </button>
                </form>

                <span class="px-5 py-2.5 bg-slate-900 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-md">
                    Role: PC {{ strtoupper(str_replace('pc', '', $role)) }}
                </span>
            </div>
        </div>

        <div class="mb-12 bg-white p-2 rounded-2xl shadow-sm border border-slate-100 max-w-2xl mx-auto">
            <input type="text" id="searchInput" placeholder="🔍 Search ObR Number (e.g., 2026-001)..." 
                   class="w-full px-6 py-4 rounded-xl border-none bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all font-bold text-slate-700 text-center text-lg">
        </div>
        
        <div id="obr-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($requests as $request)
                <div class="obr-card bg-white/90 backdrop-blur-md rounded-3xl p-8 border border-slate-200 shadow-xl flex flex-col items-center justify-between transition-all duration-300"
                     data-obr="{{ strtolower($request->obr_number) }}" 
                     data-timestamp="{{ $request->created_at->timestamp }}"
                     data-status="{{ $request->status }}">
                    
                    <div class="card-body text-center mb-6 w-full transition-all duration-300">
                        <span class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400">Reference No.</span>
                        <h3 class="text-3xl font-black text-slate-900 leading-none mt-1">{{ $request->obr_number }}</h3>
                        
                        <p class="text-sm font-bold text-slate-500 mt-2">
                            Date: <span class="text-slate-700">{{ $request->obr_date?->format('M d, Y') }}</span>
                        </p>

                        <div class="mt-4 mb-2">
                            <span class="px-4 py-1.5 bg-blue-50 text-blue-700 border border-blue-200 rounded-full text-[10px] font-black uppercase tracking-widest shadow-sm">
                                {{ str_replace('_', ' ', $request->status) }}
                            </span>
                        </div>
                        
                        @if($request->total_minutes)
                            <div class="mt-5 bg-emerald-50 text-emerald-700 border border-emerald-200 p-2 rounded-xl text-xs font-black uppercase tracking-widest inline-block px-6">
                                Total Time: {{ $request->total_minutes }} mins
                            </div>
                        @endif
                    </div>

                    <div class="w-full relative">
                        <div class="override-actions hidden w-full">
                            <button type="button" onclick="unlockCard(this)" class="w-full bg-slate-800 hover:bg-rose-600 text-white font-black py-4 rounded-xl shadow-lg transition-all text-xs uppercase tracking-widest flex items-center justify-center gap-2">
                                <span>🔒</span> Queued (Click to Override)
                            </button>
                        </div>

                        <div class="normal-actions w-full">
                            @if($role == 'pc1' && $request->status == 'entry')
                                <form action="{{ route('obr.pc1_release', $request->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-black py-4 rounded-xl shadow-lg transition-all active:scale-95">
                                        Release to PC 2
                                    </button>
                                </form>
                            
                            @elseif($role == 'pc2' && $request->status == 'processing')
                                <form action="{{ route('obr.pc2_process', $request->id) }}" method="POST" class="flex flex-col gap-3">
                                    @csrf @method('PATCH')
                                    <textarea name="analyze_control_data" placeholder="Analyze & Control Data..." required class="w-full p-4 rounded-xl border border-slate-200 text-center outline-none focus:ring-2 focus:ring-amber-500 text-sm font-medium"></textarea>
                                    <button class="w-full bg-amber-500 hover:bg-amber-600 text-white font-black py-4 rounded-xl shadow-lg transition-all active:scale-95">
                                        Done (Move to PC 3)
                                    </button>
                                </form>
                            
                            @elseif($role == 'pc3' && $request->status == 'in_transit')
                                <form action="{{ route('obr.pc3_receive', $request->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-black py-4 rounded-xl shadow-lg transition-all active:scale-95">
                                        Start Final Check
                                    </button>
                                </form>
                            
                            @elseif($role == 'pc3' && $request->status == 'pending_final_review')
                                <form action="{{ route('obr.pc3_release', $request->id) }}" method="POST" class="flex flex-col gap-3">
                                    @csrf @method('PATCH')
                                    <select name="pc3_signatory" required class="w-full p-4 rounded-xl border border-slate-200 text-center outline-none focus:ring-2 focus:ring-emerald-500 text-sm font-bold text-slate-700 appearance-none cursor-pointer bg-slate-50">
                                        <option value="" disabled selected>-- Checked and Clarified by: --</option>
                                        <option value="Administrative Aide">Administrative Aide</option>
                                        <option value="Administrative Officer">Administrative Officer</option>
                                        <option value="Budget Officer">Budget Officer</option>
                                    </select>
                                    <textarea name="pc3_remarks" placeholder="Optional Remarks..." class="w-full p-3 rounded-xl border border-slate-200 text-center outline-none focus:ring-2 focus:ring-emerald-500 text-xs font-medium"></textarea>
                                    <button class="w-full bg-teal-600 hover:bg-teal-700 text-white font-black py-4 rounded-xl shadow-lg transition-all active:scale-95">
                                        Finalize Entry
                                    </button>
                                </form>
                            
                            @elseif($role == 'pc1' && $request->status == 'ready_for_release')
                                <form action="{{ route('obr.final_release', $request->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button class="w-full bg-red-600 hover:bg-red-700 text-white font-black py-4 rounded-xl shadow-lg transition-all active:scale-95">
                                        Final Release & Lock
                                    </button>
                                </form>
                            
                            @else
                                <div class="text-center py-4 px-2 bg-slate-50 border border-slate-100 rounded-xl">
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">
                                        Awaiting Next Station...
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 text-center bg-white/50 backdrop-blur-sm rounded-3xl border-2 border-dashed border-slate-200">
                    <p class="text-slate-400 font-bold text-xl">Queue is currently empty.</p>
                </div>
            @endforelse
        </div>
    </div>

    <div id="overrideModal" class="fixed inset-0 z-[150] hidden flex items-start justify-center pt-60 p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-xl" onclick="closeOverrideModal()"></div>
        <div class="relative bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl overflow-hidden transform transition-all">
            <div class="bg-red-700 p-8 text-center text-white relative">
                <img src="{{ asset('3DMunicipalLogo.ico') }}" alt="Seal" class="w-20 h-20 mx-auto mb-4 object-contain">
                <h3 class="text-xl font-black uppercase tracking-tight">Official Authorization</h3>
                <p class="text-red-100 text-[10px] font-bold uppercase tracking-widest mt-1 opacity-70">PC 3 Control</p>
            </div>
            <div class="p-8 text-center">
                <p class="text-slate-500 font-bold leading-relaxed text-base">You are bypassing the <span class="text-slate-900 font-black italic">FIFO</span> protocol.</p>
                <div class="h-px w-12 bg-slate-100 mx-auto my-5"></div>
                <p class="text-red-700 font-black text-xs uppercase tracking-widest italic">"Is this a priority?"</p>
            </div>
            <div class="flex flex-col p-6 bg-slate-50/80 gap-3">
                <button onclick="confirmOverride()" class="w-full bg-red-700 hover:bg-red-800 text-white font-black py-4 rounded-2xl shadow-xl transition-all active:scale-95 uppercase tracking-widest text-xs">Authorize</button>
                <button onclick="closeOverrideModal()" class="w-full bg-transparent text-slate-400 font-bold py-2 text-[9px] uppercase tracking-widest">Cancel</button>
            </div>
        </div>
    </div>

    <script src="{{ asset('flatpickr.min.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const currentRole = "{{ strtolower($role) }}";
            
            // Editor Fix: converts Blade Boolean to standard JS String check
            const hasErrors = "{{ $errors->any() ? 'true' : 'false' }}" === 'true';
            if (hasErrors) { toggleModal('obrModal'); }

            const fpConfig = {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "M j, Y",
                disableMobile: "true",
                animate: true,
                maxDate: "today",
                static: false,
                appendTo: document.body
            };

            flatpickr("#start_date", fpConfig);
            flatpickr("#end_date", fpConfig);
            
            // Initial sort
            const container = document.getElementById('obr-container');
            const cards = Array.from(container.querySelectorAll('.obr-card'));
            cards.sort((a, b) => parseInt(a.getAttribute('data-timestamp')) - parseInt(b.getAttribute('data-timestamp')));
            cards.forEach(card => container.appendChild(card));
            if (currentRole === 'pc3') applyQueueLogic();

            // Auto-Refresh Logic
            let lastPulse = null;
            function checkForUpdates() {
                fetch("{{ url('/obr-pulse') }}")
                    .then(response => response.text())
                    .then(currentPulse => {
                        if (lastPulse === null) { lastPulse = currentPulse; } 
                        else if (currentPulse !== lastPulse && currentPulse !== "0") {
                            lastPulse = currentPulse; 
                            fetch(window.location.href)
                                .then(response => response.text())
                                .then(html => {
                                    const parser = new DOMParser();
                                    const newDoc = parser.parseFromString(html, "text/html");
                                    document.getElementById('obr-container').innerHTML = newDoc.getElementById('obr-container').innerHTML;
                                    
                                    const newContainer = document.getElementById('obr-container');
                                    const newCards = Array.from(newContainer.querySelectorAll('.obr-card'));
                                    newCards.sort((a, b) => parseInt(a.getAttribute('data-timestamp')) - parseInt(b.getAttribute('data-timestamp')));
                                    newCards.forEach(card => newContainer.appendChild(card));
                                    
                                    if (currentRole === 'pc3') applyQueueLogic();
                                });
                        }
                    });
            }
            setInterval(checkForUpdates, 2000);
        });

        function toggleModal(modalId) {
            const modal = document.getElementById(modalId);
            if(modal) {
                modal.classList.toggle('hidden');
                if (!modal.classList.contains('hidden')) { 
                    const input = document.getElementById('obr_input');
                    if(input) input.focus(); 
                }
            }
        }

        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const cards = document.querySelectorAll('.obr-card');
            cards.forEach(card => {
                const obrNumber = card.getAttribute('data-obr');
                card.style.display = obrNumber.includes(searchTerm) ? 'flex' : 'none';
            });
            if ("{{ strtolower($role) }}" === 'pc3') applyQueueLogic();
        });

        function applyQueueLogic() {
            if ("{{ strtolower($role) }}" !== 'pc3') return;
            const container = document.getElementById('obr-container');
            const cards = Array.from(container.querySelectorAll('.obr-card')).filter(card => card.style.display !== 'none');
            let foundFirstActionable = false;
            
            cards.forEach((card) => {
                const cardBody = card.querySelector('.card-body');
                const normalActions = card.querySelector('.normal-actions');
                const overrideActions = card.querySelector('.override-actions');
                const status = card.getAttribute('data-status');
                const isWaitingForPc3 = (status === 'in_transit' || status === 'pending_final_review');
                
                if (isWaitingForPc3) {
                    if (!foundFirstActionable) {
                        foundFirstActionable = true;
                        cardBody.style.opacity = "1";
                        cardBody.style.filter = "grayscale(0%)";
                        card.classList.add('ring-4', 'ring-blue-400');
                        card.classList.remove('ring-rose-400');
                        if(normalActions) normalActions.style.display = 'block';
                        if(overrideActions) overrideActions.style.display = 'none';
                    } else {
                        cardBody.style.opacity = "0.2";
                        cardBody.style.filter = "grayscale(100%)";
                        card.classList.remove('ring-4', 'ring-blue-400', 'ring-rose-400');
                        if(normalActions) normalActions.style.display = 'none';
                        if(overrideActions) overrideActions.style.display = 'block';
                    }
                } else {
                    cardBody.style.opacity = "0.5"; 
                    cardBody.style.filter = "grayscale(50%)";
                    card.classList.remove('ring-4', 'ring-blue-400', 'ring-rose-400');
                    if(normalActions) normalActions.style.display = 'block';
                    if(overrideActions) overrideActions.style.display = 'none';
                }
            });
        }

        let pendingUnlockCard = null;
        function unlockCard(btn) {
            pendingUnlockCard = btn.closest('.obr-card');
            document.getElementById('overrideModal').classList.remove('hidden');
        }

        function closeOverrideModal() { document.getElementById('overrideModal').classList.add('hidden'); }

        function confirmOverride() {
            if (pendingUnlockCard) {
                const cardBody = pendingUnlockCard.querySelector('.card-body');
                cardBody.style.opacity = "1";
                cardBody.style.filter = "grayscale(0%)";
                pendingUnlockCard.classList.add('ring-4', 'ring-rose-400');
                pendingUnlockCard.querySelector('.override-actions').style.display = 'none';
                pendingUnlockCard.querySelector('.normal-actions').style.display = 'block';
                closeOverrideModal();
            }
        }

        document.addEventListener('submit', function(e) {
            // THE FIX: Ignore GET requests like the Export form so the button doesn't freeze
            if (e.target.method && e.target.method.toUpperCase() === 'GET') {
                return;
            }

            const submitBtn = e.target.querySelector('button[type="submit"]') || e.target.querySelector('button:not([type="button"])');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                if(!submitBtn.querySelector('svg') && !submitBtn.querySelector('span')) {
                    submitBtn.innerText = 'Processing...';
                }
            }
        });
    </script>
@endsection