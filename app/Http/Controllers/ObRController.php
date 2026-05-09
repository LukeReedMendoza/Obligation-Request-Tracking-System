<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\ObligationRequest;
use Illuminate\Http\Request;

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

        ObligationRequest::create([
            'obr_number' => $request->obr_number,
            'obr_date' => $request->obr_date,
            'status' => 'entry',
            'pc1_time_in' => now(), 
        ]);

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

        return back()->with('success', 'Finalized! Returned to PC 1.');
    }

   public function finalRelease($id)
    {
        $obr = ObligationRequest::find($id); // Safety Net
        if (!$obr) return back(); 
        
        $finishTime = now();
        
        // AUTO-CALCULATION
        $startTime = $obr->pc1_time_in ? \Carbon\Carbon::parse($obr->pc1_time_in) : $obr->created_at;
        $totalMinutes = $startTime->diffInMinutes($finishTime);
        
        $obr->update([
            'status' => 'completed',
            'pc1_final_release' => $finishTime, 
            'total_minutes' => $totalMinutes 
        ]);

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

            // THE FIX 1: Clearer Column Headers for the Excel file
            $columns = [
                'Date', 
                'Time Received', 
                'ObR No.', 
                'Time', 
                'Analyze and Control', 
                'Time', 
                'Checked and Clarified by:', 
                'Time Released', 
                'Total Time', 
                'Remarks'                    
            ];

            // Formatter for the very last column (Total Duration)
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
                
                // BOM Fix for proper MS Excel rendering
                fputs($file, $bom =(chr(0xEF) . chr(0xBB) . chr(0xBF))); 
                fputcsv($file, $columns); 

                foreach ($obrs as $obr) {
                    // Time Received (Start)
                    $start = $obr->created_at ? \Carbon\Carbon::parse($obr->created_at) : now();
                    
                    // THE FIX 2: Format to exact clock time (e.g. "01:45 PM") instead of minutes
                    $pc1_done_time = $obr->pc1_time_out ? \Carbon\Carbon::parse($obr->pc1_time_out)->format('h:i A') : 'N/A';
                    $pc2_done_time = $obr->pc2_time_out ? \Carbon\Carbon::parse($obr->pc2_time_out)->format('h:i A') : 'N/A';
                    
                    // Final Release Time
                    $pc3_start = $obr->pc3_time_in ? \Carbon\Carbon::parse($obr->pc3_time_in) : $start;
                    $final_done = $obr->pc1_final_release ? \Carbon\Carbon::parse($obr->pc1_final_release) : ($obr->updated_at ? \Carbon\Carbon::parse($obr->updated_at) : $pc3_start);

                    // We still calculate the grand total duration for the very last column
                    $total_system_mins = max(0, $start->diffInSeconds($final_done) / 60);

                    fputcsv($file, [
                        $obr->obr_date ? \Carbon\Carbon::parse($obr->obr_date)->format('M d, Y') : 'N/A', 
                        $start->format('h:i A'), 
                        $obr->obr_number ?? 'N/A', 
                        $pc1_done_time, // Replaced duration with clock time
                        $obr->analyze_control_data ?? 'N/A', 
                        $pc2_done_time, // Replaced duration with clock time
                        $obr->pc3_signatory ?? 'N/A', 
                        $final_done->format('h:i A'), // Final clock time
                        $formatTime($total_system_mins), 
                        $obr->pc3_remarks ?? 'None' 
                    ]);
                }
                fclose($file);
            };

            // StreamDownload prevents freezing buttons and crashing headers
            return response()->streamDownload($callback, $filename, [
                "Content-type"        => "text/csv",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ]);

        } catch (\Exception $e) {
            // Safety Net: Bounces the user safely back if an error occurs
            return redirect()->back()->withErrors('Export Error: ' . $e->getMessage());
        }
    }
}