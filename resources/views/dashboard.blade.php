@extends('layouts.app')

@section('content')
    <div class="absolute top-0 left-1/4 w-96 h-96 bg-emerald-100/30 rounded-full blur-3xl pointer-events-none -z-10 ambient-blob-1"></div>
    <div class="absolute top-1/3 right-1/4 w-96 h-96 bg-blue-100/20 rounded-full blur-3xl pointer-events-none -z-10 ambient-blob-2"></div>

    <link rel="stylesheet" href="{{ asset('flatpickr.min.css') }}">
    <style>
        .flatpickr-calendar { background: #ffffff !important; border-radius: 1.5rem !important; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.2) !important; border: 1px solid #e2e8f0 !important; padding: -10px; z-index: 99999 !important; }
        .flatpickr-day.selected, .flatpickr-day.selected:hover { background: #059669 !important; border-color: #059669 !important; color: white !important; font-weight: 800 !important; border-radius: 12px !important; }
        .flatpickr-day.today { border-color: #10b981 !important; color: #065f46 !important; font-weight: 900 !important; background: #ecfdf5 !important; }
        .flatpickr-day.today:hover { background: #d1fae5 !important; }
        .flatpickr-months .flatpickr-month { color: #1e293b !important; font-weight: 800 !important; }
        .flatpickr-weekday { color: #94a3b8 !important; font-weight: 700 !important; text-transform: uppercase; font-size: 10px !important; }
        .obr-card { transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1) !important; position: relative; }
        .obr-card:hover { transform: translateY(-5px) scale(1.01) !important; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15) !important; border-color: #cbd5e1 !important; }
        @keyframes ambientPulse { 0%, 100% { transform: scale(1) translate(0px, 0px); opacity: 0.4; } 50% { transform: scale(1.08) translate(15px, -15px); opacity: 0.6; } }
        .ambient-blob-1 { animation: ambientPulse 8s infinite ease-in-out; }
        .ambient-blob-2 { animation: ambientPulse 12s infinite ease-in-out reverse; }
        button, a { transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important; }
    </style>

    <div id="alert-container" class="w-full max-w-4xl mx-auto flex flex-col items-center">
        @if(session('success'))
            <div class="mb-6 px-4 py-3 bg-green-100 text-green-800 rounded-lg w-full font-medium text-center shadow-sm">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-6 px-4 py-3 bg-red-100 text-red-800 border-2 border-red-200 rounded-lg w-full font-medium shadow-sm">
                <div class="flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span><strong>Entry Failed:</strong> Please fix the errors below.</span>
                </div>
                <ul class="mt-2 text-sm list-disc list-inside text-left w-fit mx-auto">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif
    </div>

    <div id="offlineOverlay" class="fixed inset-0 z-[9999] hidden flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm transition-all duration-300">
        <div class="bg-white p-8 rounded-3xl shadow-2xl text-center max-w-md w-full border border-rose-100">
            <div class="mx-auto h-20 w-20 bg-rose-50 rounded-full flex items-center justify-center mb-6">
                <svg class="h-10 w-10 text-rose-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3m8.293 8.293l1.414 1.414"></path></svg>
            </div>
            <h2 class="text-2xl font-black text-slate-800 mb-2 tracking-tight">Connection Lost</h2>
            <p class="text-slate-500 text-sm font-medium mb-8 leading-relaxed">We temporarily lost connection to the PC 1 Server. Please ensure the main computer is turned on and connected to the network.</p>
            <div class="flex items-center justify-center gap-3 text-rose-600 font-bold bg-rose-50 py-3 px-4 rounded-xl">
                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                Attempting to reconnect...
            </div>
        </div>
    </div>

    @php
        $nextRole = ''; $nextLabel = '';
        if ($role === 'pc1') { $nextRole = 'pc2'; $nextLabel = 'Go to PC 2'; }
        elseif ($role === 'pc2') { $nextRole = 'pc3'; $nextLabel = 'Go to PC 3'; }
        elseif ($role === 'pc3') { $nextRole = 'pc4'; $nextLabel = 'Go to PC 4'; }
        elseif ($role === 'pc4') { $nextRole = 'pc1'; $nextLabel = 'Go to PC 1'; }
    @endphp

    <div class="w-full max-w-7xl mx-auto mb-6 flex justify-between items-center">
        <a href="{{ url('/') }}" class="inline-flex items-center px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-black uppercase tracking-widest rounded-xl shadow-md transition-all active:scale-95 gap-2 group">
            <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Main Menu
        </a>
        @if($nextRole)
        <a href="{{ url('/dashboard/'.$nextRole) }}" class="inline-flex items-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black uppercase tracking-widest rounded-xl shadow-md transition-all active:scale-95 gap-2 group">
            {{ $nextLabel }}
            <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
        </a>
        @endif
    </div>

    @if($role == 'pc1')
    <div class="w-full max-w-4xl bg-white rounded-2xl shadow-lg border border-slate-100 p-12 mb-10 text-center">
        <h2 class="text-3xl font-black text-slate-800 mb-2">New Entry Station</h2>
        <p class="text-slate-500 mb-8 font-medium">Click the button below to officially "Time In" a new Obligation Request.</p>
        <button onclick="toggleModal('obrModal')" class="bg-blue-600 hover:bg-blue-700 text-white text-xl font-black py-5 px-16 rounded-2xl shadow-xl hover:shadow-2xl transition-all active:scale-95 transform">TIME IN (START)</button>
    </div>

    <div id="obrModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="toggleModal('obrModal')"></div>
        <div class="relative bg-white w-full max-w-md rounded-3xl shadow-2xl overflow-hidden transform transition-all">
            <div class="bg-blue-600 p-6 text-center text-white">
                <h3 class="text-xl font-bold">Log New Request</h3>
                <p class="text-blue-100 text-[10px] font-black uppercase tracking-widest mt-1">Station: PC 1 Entry</p>
            </div>
            <form action="{{ route('obr.store') }}" method="POST" class="p-8">
                @csrf
                <input type="hidden" name="obr_date" value="{{ now()->format('Y-m-d') }}">
                <div class="mb-6">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Enter ObR Number</label>
                    <input type="text" name="obr_number" id="obr_input" placeholder="e.g. 2026-0001" required autofocus class="w-full px-5 py-4 rounded-xl border-2 border-slate-100 bg-slate-50 text-2xl font-black text-center focus:border-blue-500 focus:ring-0 outline-none transition-all">
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="toggleModal('obrModal')" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-4 rounded-xl transition-colors">Cancel</button>
                    <button type="submit" class="flex-[2] bg-blue-600 hover:bg-blue-700 text-white font-black py-4 rounded-xl shadow-lg shadow-blue-200 transition-all active:scale-95">CONFIRM & LOG</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <div class="w-full max-w-7xl">
        <div class="flex flex-col md:flex-row justify-between items-center mb-12 border-b border-slate-200 pb-6 gap-4">
            <h2 class="text-2xl font-black text-slate-800 tracking-tight italic">Active ObR Tracking List</h2>
            <div class="flex flex-wrap gap-4 items-center justify-center">
                <form action="{{ route('obr.export') }}" method="GET" class="flex items-center gap-1 bg-white p-1 rounded-2xl shadow-sm border border-slate-200">
                    <div class="flex items-center bg-slate-50 rounded-xl px-3 py-1 border border-transparent focus-within:border-emerald-500 transition-all">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-tighter mr-2">From</span>
                        <input type="text" id="start_date" name="start_date" placeholder="Select Date" class="text-xs bg-transparent border-none focus:ring-0 p-1 w-24 text-slate-700 font-bold cursor-pointer">
                    </div>
                    <div class="flex items-center bg-slate-50 rounded-xl px-3 py-1 border border-transparent focus-within:border-emerald-500 transition-all">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-tighter mr-2">To</span>
                        <input type="text" id="end_date" name="end_date" placeholder="Select Date" class="text-xs bg-transparent border-none focus:ring-0 p-1 w-24 text-slate-700 font-bold cursor-pointer">
                    </div>
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest shadow-md shadow-emerald-200 transition-all active:scale-95 flex items-center gap-2">Export</button>
                </form>
                <span class="px-5 py-2.5 bg-slate-900 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-md">Role: PC {{ strtoupper(str_replace('pc', '', $role)) }}</span>
            </div>
        </div>

        <div class="mb-12 bg-white p-2 rounded-2xl shadow-sm border border-slate-100 max-w-2xl mx-auto">
            <input type="text" id="searchInput" placeholder="Search ObR Number (e.g., 2026-001)..." class="w-full px-6 py-4 rounded-xl border-none bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all font-bold text-slate-700 text-center text-lg">
        </div>
        
        <div id="obr-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($requests as $request)
                <div class="obr-card bg-white/90 backdrop-blur-md rounded-3xl p-8 border border-slate-200 shadow-xl flex flex-col items-center justify-between transition-all duration-300" 
                     data-obr="{{ strtolower($request->obr_number) }}" 
                     data-obr-raw="{{ $request->obr_number }}"
                     data-timestamp="{{ $request->created_at->timestamp }}" 
                     data-status="{{ $request->status }}"
                     data-date="{{ $request->obr_date?->format('M d, Y') }}"
                     data-analyze="{{ $request->analyze_control_data }}"
                     data-signatory="{{ $request->pc3_signatory }}"
                     data-remarks="{{ $request->pc3_remarks }}">
                    
                    <div class="card-body text-center mb-6 w-full transition-all duration-300">
                        <div class="flex items-center justify-center gap-2 mb-1">
                            <span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span></span>
                            <span class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400">Obligation Request no.</span>
                        </div>
                        <h3 class="text-3xl font-black text-slate-900 leading-none mt-1">{{ $request->obr_number }}</h3>
                        <p class="text-sm font-bold text-slate-500 mt-2">Date: <span class="text-slate-700">{{ $request->obr_date?->format('M d, Y') }}</span></p>
                        <div class="mt-4 mb-2"><span class="px-4 py-1.5 bg-blue-50 text-blue-700 border border-blue-200 rounded-full text-[10px] font-black uppercase tracking-widest shadow-sm">{{ str_replace('_', ' ', $request->status) }}</span></div>
                    </div>

                    <div class="w-full relative">
                        <div class="override-actions hidden w-full">
                            <button type="button" onclick="unlockCard(this)" class="w-full bg-slate-800 hover:bg-rose-600 text-white font-black py-4 rounded-xl shadow-lg transition-all text-xs uppercase tracking-widest flex items-center justify-center gap-2">Queued (Click to Override)</button>
                        </div>
                        <div class="normal-actions w-full">
                            @if($role == 'pc1' && $request->status == 'entry')
                                <form action="{{ route('obr.pc1_release', $request->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-black py-4 rounded-xl shadow-lg transition-all active:scale-95 text-sm uppercase tracking-widest">Release to PC 2</button>
                                </form>
                            @elseif($role == 'pc2' && $request->status == 'processing')
                                <form action="{{ route('obr.pc2_process', $request->id) }}" method="POST" class="flex flex-col gap-3">
                                    @csrf @method('PATCH')
                                    <textarea name="analyze_control_data" rows="3" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 placeholder:text-slate-400 text-center placeholder:text-center outline-none focus:bg-white focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all shadow-inner" placeholder="Analyze and Control Data (Optional Notes)..."></textarea>
                                    <button class="w-full bg-amber-500 hover:bg-amber-600 text-white font-black py-4 rounded-xl shadow-lg transition-all active:scale-95 text-sm uppercase tracking-widest">Done (Move to PC 3)</button>
                                </form>
                            @elseif($role == 'pc3' && $request->status == 'in_transit')
                                <form action="{{ route('obr.pc3_receive', $request->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-black py-4 rounded-xl shadow-lg transition-all active:scale-95 text-sm uppercase tracking-widest">Start Final Check</button>
                                </form>
                            @elseif($role == 'pc3' && $request->status == 'pending_final_review')
                                <form action="{{ route('obr.pc3_release', $request->id) }}" method="POST" class="flex flex-col gap-3">
                                    @csrf @method('PATCH')
                                    <select name="pc3_signatory" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 outline-none focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all shadow-inner cursor-pointer" style="text-align-last: center;">
                                        <option value="">-- Select Signatory (Optional) --</option>
                                        <option value="Administrative Aide">Administrative Aide</option>
                                        <option value="Administrative Officer">Administrative Officer</option>
                                        <option value="Budget Officer">Budget Officer</option>
                                    </select>
                                    <textarea name="pc3_remarks" rows="2" placeholder="Optional Remarks..." class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 placeholder:text-slate-400 text-center placeholder:text-center outline-none focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all shadow-inner"></textarea>
                                    <button class="w-full bg-teal-600 hover:bg-teal-700 text-white font-black py-4 rounded-xl shadow-lg transition-all active:scale-95 text-sm uppercase tracking-widest">Finalize Entry</button>
                                </form>
                            
                            @elseif($role == 'pc4' && $request->status == 'ready_for_release')
                                <button type="button" onclick="openPreviewModal(this)" data-release-url="{{ route('obr.final_release', $request->id) }}" class="w-full bg-red-600 hover:bg-red-700 text-white font-black py-4 rounded-xl shadow-lg transition-all active:scale-95 text-sm uppercase tracking-widest">
                                    Preview Data & Lock
                                </button>
                            
                            @else
                                <div class="text-center py-4 px-2 bg-slate-50 border border-slate-100 rounded-xl">
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Awaiting Next Station...</p>
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

    <div id="previewModal" class="fixed inset-0 z-[200] hidden flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-sm" onclick="closePreviewModal()"></div>
        <div class="relative bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl overflow-hidden transform transition-all">
            <div class="bg-slate-800 p-6 text-center text-white">
                <h3 class="text-xl font-black uppercase tracking-tight">Final Data Review</h3>
                <p id="preview-obr-number" class="text-blue-300 text-sm font-black uppercase tracking-widest mt-1">OBR-XXXX</p>
            </div>
            
            <form id="previewForm" action="" method="POST" class="p-8 flex flex-col gap-4">
                @csrf @method('PATCH')

                <div class="flex justify-between items-center text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-3 mb-2">
                    <span>Date Logged:</span>
                    <span id="preview-date" class="text-slate-800 text-xs">Date</span>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Analyze & Control Data (PC 2)</label>
                    <textarea name="analyze_control_data" id="preview-analyze" rows="2" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 outline-none focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all shadow-inner"></textarea>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Signatory (PC 3)</label>
                    <select name="pc3_signatory" id="preview-signatory" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 outline-none focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all shadow-inner" style="text-align-last: center;">
                        <option value="">-- Select Signatory (Optional) --</option>
                        <option value="Administrative Aide">Administrative Aide</option>
                        <option value="Administrative Officer">Administrative Officer</option>
                        <option value="Budget Officer">Budget Officer</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Remarks (PC 3)</label>
                    <textarea name="pc3_remarks" id="preview-remarks" rows="2" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 outline-none focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all shadow-inner"></textarea>
                </div>

                <div class="flex gap-3 mt-4">
                    <button type="button" onclick="closePreviewModal()" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-4 rounded-xl transition-colors uppercase tracking-widest text-xs">Cancel</button>
                    <button type="submit" class="flex-[2] bg-red-600 hover:bg-red-700 text-white font-black py-4 rounded-xl shadow-lg transition-all active:scale-95 uppercase tracking-widest text-xs flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        Confirm & Lock
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="{{ asset('flatpickr.min.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const currentRole = "{{ strtolower($role) }}";
            const hasErrors = "{{ $errors->any() ? 'true' : 'false' }}" === 'true';
            if (hasErrors) { toggleModal('obrModal'); }

            const fpConfig = { dateFormat: "Y-m-d", altInput: true, altFormat: "M j, Y", disableMobile: "true", animate: true, maxDate: "today", static: false, appendTo: document.body };
            flatpickr("#start_date", fpConfig);
            flatpickr("#end_date", fpConfig);
            
            const container = document.getElementById('obr-container');
            const cards = Array.from(container.querySelectorAll('.obr-card'));
            cards.sort((a, b) => parseInt(a.getAttribute('data-timestamp')) - parseInt(b.getAttribute('data-timestamp')));
            cards.forEach(card => container.appendChild(card));
            if (currentRole === 'pc3') applyQueueLogic();

            let lastPulse = null;
            let failedAttempts = 0; 

            function checkForUpdates() {
                fetch("{{ url('/obr-pulse') }}")
                    .then(response => {
                        if (!response.ok) throw new Error("Server disconnected");
                        failedAttempts = 0;
                        document.getElementById('offlineOverlay').classList.add('hidden');
                        return response.text();
                    })
                    .then(currentPulse => {
                        if (lastPulse === null) { lastPulse = currentPulse; } 
                        else if (currentPulse !== lastPulse && currentPulse !== "0") {
                            lastPulse = currentPulse; 
                            
                            const activeEl = document.activeElement;
                            const activeCard = activeEl ? activeEl.closest('.obr-card') : null;
                            const activeObrId = activeCard ? activeCard.getAttribute('data-obr') : null;
                            const activeFieldName = activeEl ? activeEl.name : null;
                            const activeValue = activeEl ? activeEl.value : null;

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

                                    if (activeObrId && activeFieldName) {
                                        const restoredCard = document.querySelector(`.obr-card[data-obr="${activeObrId}"]`);
                                        if (restoredCard) {
                                            const restoredField = restoredCard.querySelector(`[name="${activeFieldName}"]`);
                                            if (restoredField) {
                                                restoredField.value = activeValue;
                                                restoredField.focus();
                                                if (restoredField.tagName === 'TEXTAREA' || restoredField.type === 'text') {
                                                    const valLength = restoredField.value.length;
                                                    restoredField.setSelectionRange(valLength, valLength);
                                                }
                                            }
                                        }
                                    }
                                });
                        }
                    })
                    .catch(error => {
                        failedAttempts++;
                        if (failedAttempts >= 3) { document.getElementById('offlineOverlay').classList.remove('hidden'); }
                    });
            }
            // CHANGED: Heartbeat slowed down to 3 seconds to reduce network traffic and slowness
            setInterval(checkForUpdates, 3000); 
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

        function openPreviewModal(btn) {
            const card = btn.closest('.obr-card');
            const releaseUrl = btn.getAttribute('data-release-url');

            const obrNo = card.getAttribute('data-obr-raw');
            const date = card.getAttribute('data-date');
            const analyze = card.getAttribute('data-analyze');
            const signatory = card.getAttribute('data-signatory');
            const remarks = card.getAttribute('data-remarks');

            document.getElementById('preview-obr-number').innerText = obrNo;
            document.getElementById('preview-date').innerText = date;
            document.getElementById('preview-analyze').value = analyze || '';
            document.getElementById('preview-signatory').value = signatory || '';
            document.getElementById('preview-remarks').value = remarks || '';

            document.getElementById('previewForm').action = releaseUrl;
            document.getElementById('previewModal').classList.remove('hidden');
        }

        function closePreviewModal() { document.getElementById('previewModal').classList.add('hidden'); }

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
            if (e.target.method && e.target.method.toUpperCase() === 'GET') { return; }
            e.preventDefault(); 

            const form = e.target;
            const submitBtn = form.querySelector('button[type="submit"]') || form.querySelector('button:not([type="button"])');
            const originalText = submitBtn ? submitBtn.innerHTML : '';

            // FIXED: Instantly overwrite the button HTML so it visually locks and shows PROCESSING!
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed', 'animate-pulse');
                submitBtn.innerHTML = 'PROCESSING...';
            }

            const formData = new FormData(form);

            fetch(form.action, {
                method: form.method || 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const newDoc = parser.parseFromString(html, "text/html");

                const container = document.getElementById('obr-container');
                const newContainer = newDoc.getElementById('obr-container');
                if (container && newContainer) {
                    container.innerHTML = newContainer.innerHTML;
                    const cards = Array.from(container.querySelectorAll('.obr-card'));
                    cards.sort((a, b) => parseInt(a.getAttribute('data-timestamp')) - parseInt(b.getAttribute('data-timestamp')));
                    cards.forEach(card => container.appendChild(card));
                    if ("{{ strtolower($role) }}" === 'pc3') applyQueueLogic();
                }

                const alertContainer = document.getElementById('alert-container');
                const newAlertContainer = newDoc.getElementById('alert-container');
                if (alertContainer && newAlertContainer) {
                    alertContainer.innerHTML = newAlertContainer.innerHTML;
                }

                if (form.closest('#obrModal')) {
                    toggleModal('obrModal');
                    form.reset();
                    const dInput = form.querySelector('input[name="obr_date"]');
                    if (dInput) dInput.value = new Date().toISOString().split('T')[0];
                }
                
                closeOverrideModal();
                closePreviewModal();
            })
            .catch(error => {
                console.error("Background network error:", error);
                
                // RESTORE BUTTON: If the network fails, it brings the button back
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed', 'animate-pulse');
                    submitBtn.innerHTML = originalText;
                }
            });
        });
    </script>
@endsection