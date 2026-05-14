<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\ObligationRequest;
use Illuminate\Http\Request;
use App\Events\ObrMoved; // ADDED: For real-time updates
use Carbon\Carbon;       // ADDED: For easier time handling

class ObRController extends Controller
{
  public function dashboard($role)
    {
        // 1. THE SILENT LOGIN: Instantly logs them in based on the clicked step
        if ($role === 'pc1') Auth::loginUsingId(1);
        if ($role === 'pc2') Auth::loginUsingId(2);
        if ($role === 'pc3') Auth::loginUsingId(3);

        // 2. Get active requests
        $requests = \App\Models\ObligationRequest::where('status', '!=', 'completed')->get();

        // 3. The Daily Achievement Metric
        $todayCompleted = \App\Models\ObligationRequest::where('status', 'completed')
            ->whereDate('updated_at', \Carbon\Carbon::now('Asia/Manila')->toDateString())
            ->count();

        return view('dashboard', compact('requests', 'role', 'todayCompleted'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'obr_number' => 'required|string|unique:obligation_requests,obr_number',
            'obr_date' => 'required|date',
        ]);

        $obr = ObligationRequest::create([
            'obr_number' => $request->obr_number,
            'obr_date' => $request->obr_date,
            'status' => 'entry',
            'pc1_time_in' => now(), 
        ]);

        // ADDED: Updates PC 2 and PC 3 dashboards instantly
        ObrMoved::dispatch($obr);

        return back()->with('success', 'ObR Logged Successfully!');
    }

    // PC 1 physically passes the paper to PC 2
    public function pc1Release($id) 
    {
        $obr = ObligationRequest::find($id); // Safety Net
        if (!$obr) return back(); 

        $obr->update([
            'status' => 'processing',
            'pc1_time_out' => now(), 
        ]);

        // ADDED: Updates PC 2 and PC 3 dashboards instantly
        ObrMoved::dispatch($obr);

        return back()->with('success', 'ObR sent to PC 2!');
    }

    // PC 2 finishes analyzing and passes it to PC 3
    public function pc2Process(Request $request, $id) 
    {
        $obr = ObligationRequest::find($id); // Safety Net
        if (!$obr) return back(); 

        $obr->update([
            'status' => 'in_transit',
            'analyze_control_data' => $request->analyze_control_data,
            'pc2_time_out' => now(), 
        ]);

        // ADDED: Updates PC 2 and PC 3 dashboards instantly
        ObrMoved::dispatch($obr);

        return back()->with('success', 'Processing done! Sent to PC 3.');
    }

    // PC 3 officially receives the paper on their desk
    public function pc3Receive($id)
    {
        $obr = ObligationRequest::find($id); // Safety Net
        if (!$obr) return back(); 

        $obr->update([
            'status' => 'pending_final_review', 
            'pc3_time_in' => now(), 
        ]);
        
        // ADDED: Updates PC 2 and PC 3 dashboards instantly
        ObrMoved::dispatch($obr);

        return redirect()->back();
    }

    // PC 3 finishes and returns it to PC 1
    public function pc3Release(Request $request, $id)
    {
        $obr = ObligationRequest::find($id); // Safety Net
        if (!$obr) return back(); 

        $obr->update([
            'status' => 'ready_for_release',
            'pc3_remarks' => $request->pc3_remarks,
            'pc3_signatory' => $request->pc3_signatory, 
            'pc3_time_out' => now() 
        ]);

        // ADDED: Updates PC 2 and PC 3 dashboards instantly
        ObrMoved::dispatch($obr);

        return back()->with('success', 'Finalized! Returned to PC 1.');
    }

   public function finalRelease($id)
    {
        $obr = ObligationRequest::find($id); // Safety Net
        if (!$obr) return back(); 
        
        $finishTime = now();
        
        // AUTO-CALCULATION
        $startTime = $obr->pc1_time_in ? \Carbon\Carbon::parse($obr->pc1_time_in) : $obr->created_at;
        
        // ADDED: Fixed calculation to get accurate decimal minutes (e.g. 1.5 mins)
        $totalMinutes = $startTime->diffInSeconds($finishTime) / 60;
        
        $obr->update([
            'status' => 'completed',
            'pc1_final_release' => $finishTime, 
            'total_minutes' => $totalMinutes 
        ]);

        // ADDED: Updates PC 2 and PC 3 dashboards instantly
        ObrMoved::dispatch($obr);

        return back()->with('success', 'ObR Officially Completed and Time Calculated!');
    }

   public function exportReport(Request $request) 
    {
        try {
            $query = ObligationRequest::where('status', 'completed')
                                      ->orderBy('created_at', 'desc');

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $start = \Carbon\Carbon::parse($request->start_date)->startOfDay();
                $end = \Carbon\Carbon::parse($request->end_date)->endOfDay();
                
                $query->whereBetween('pc1_final_release', [$start, $end]);
            }

            $obrs = $query->get();

            $fileDateInfo = $request->filled('start_date') 
                ? "_{$request->start_date}_to_{$request->end_date}" 
                : "_" . date('Y-m-d');
                
            $filename = "ObR_Report" . $fileDateInfo . ".csv";

            // UPDATED: Columns arranged exactly in the requested order
            $columns = [
                'Date', 
                'Time Received', 
                'ObR No.', 
                'Process 1 Duration',
                'Time (PC 1 Done)', 
                'Analyze and Control', 
                'Process 2 Duration',             // <--- Make sure this comma is here!
                'Checked and Clarified by', 
                'Process 3 Duration',
                'Time Released', 
                'Remarks'                    
            ];

            $formatTime = function($mins) {
                $mins = (int) round($mins); 
                if ($mins <= 0) return "< 1 min";
                if ($mins < 60) return "{$mins} mins";
                $hrs = floor($mins / 60);
                $rem_mins = $mins % 60;
                return "{$hrs} hr " . ($rem_mins > 0 ? "{$rem_mins} mins" : "");
            };

            $callback = function() use($obrs, $columns, $formatTime) {
                $file = fopen('php://output', 'w');
                
                fputs($file, $bom =(chr(0xEF) . chr(0xBB) . chr(0xBF))); 
                fputcsv($file, $columns); 

                foreach ($obrs as $obr) {
                    // Time Parsing
                    $start = $obr->created_at ? \Carbon\Carbon::parse($obr->created_at) : now();
                    $pc1_in = $obr->pc1_time_in ? \Carbon\Carbon::parse($obr->pc1_time_in) : $start;
                    $pc1_out = $obr->pc1_time_out ? \Carbon\Carbon::parse($obr->pc1_time_out) : null;
                    $pc2_out = $obr->pc2_time_out ? \Carbon\Carbon::parse($obr->pc2_time_out) : null;
                    $pc3_in = $obr->pc3_time_in ? \Carbon\Carbon::parse($obr->pc3_time_in) : null;
                    $pc3_out = $obr->pc3_time_out ? \Carbon\Carbon::parse($obr->pc3_time_out) : null;
                    $final_done = $obr->pc1_final_release ? \Carbon\Carbon::parse($obr->pc1_final_release) : ($obr->updated_at ? \Carbon\Carbon::parse($obr->updated_at) : ($pc3_out ?? $start));

                    // Individual PC Duration Calculations
                    $pc1_duration = ($pc1_in && $pc1_out) ? $formatTime($pc1_in->diffInSeconds($pc1_out) / 60) : 'N/A';
                    $pc2_duration = ($pc1_out && $pc2_out) ? $formatTime($pc1_out->diffInSeconds($pc2_out) / 60) : 'N/A';
                    $pc3_duration = ($pc3_in && $pc3_out) ? $formatTime($pc3_in->diffInSeconds($pc3_out) / 60) : 'N/A';

                    // Clock Time Formatting
                    $pc1_done_time = $pc1_out ? $pc1_out->format('h:i A') : 'N/A';

                    // Putting data exactly matching the column order
                    fputcsv($file, [
                        $obr->obr_date ? \Carbon\Carbon::parse($obr->obr_date)->format('M d, Y') : 'N/A', 
                        $start->format('h:i A'), 
                        $obr->obr_number ?? 'N/A', 
                        $pc1_duration,
                        $pc1_done_time, 
                        $obr->analyze_control_data ?? 'N/A', 
                        $pc2_duration, 
                        $obr->pc3_signatory ?? 'N/A', 
                        $pc3_duration, 
                        $final_done->format('h:i A'), 
                        $obr->pc3_remarks ?? 'None' 
                    ]);
                }
                fclose($file);
            };

            return response()->streamDownload($callback, $filename, [
                "Content-type"        => "text/csv",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Export Error: ' . $e->getMessage());
        }
    }
    }