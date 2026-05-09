<?php

use App\Http\Controllers\ObRController;
use Illuminate\Support\Facades\Route;

// Landing page is now the Role Selector
Route::get('/', function () {
    // FORCE them back to the 'Viewer' profile (User 4) so they don't ghost a station
    \Illuminate\Support\Facades\Auth::loginUsingId(4);
    
    return view('role_selector');
});

// Dashboard now requires a role in the URL (e.g., /dashboard/pc1)
Route::get('/dashboard/{role}', [ObRController::class, 'dashboard'])->name('dashboard');

// PC 1 Actions
Route::post('/obr/store', [ObRController::class, 'store'])->name('obr.store');
Route::patch('/obr/{id}/pc1-release', [ObRController::class, 'pc1Release'])->name('obr.pc1_release');
Route::patch('/obr/{id}/final-release', [ObRController::class, 'finalRelease'])->name('obr.final_release');

// PC 2 Actions
Route::patch('/obr/{id}/pc2-process', [ObRController::class, 'pc2Process'])->name('obr.pc2_process');

// PC 3 Actions
Route::patch('/obr/{id}/pc3-receive', [ObRController::class, 'pc3Receive'])->name('obr.pc3_receive');
Route::patch('/obr/{id}/pc3-release', [ObRController::class, 'pc3Release'])->name('obr.pc3_release');

Route::get('/obr/export', [\App\Http\Controllers\ObRController::class, 'exportReport'])->name('obr.export');

use App\Events\PingEvent;

Route::get('/send-ping', function () {
    PingEvent::dispatch("Hello PC 2! The Budget Office Radar is online!");
    return "Ping successfully fired into the network!";
}); // <-- THIS WAS MISSING!

// THE SILENT POLLER BACKEND: Returns the latest activity timestamp
Route::get('/obr-pulse', function () {
    // Looks at the database and returns the exact time the last document was touched
    $latest = \App\Models\ObligationRequest::max('updated_at');
    
    // Returns it as plain text to the browser
    return response($latest ?? '0');
});