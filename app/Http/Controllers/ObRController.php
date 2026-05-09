<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\ObligationRequest;
use Illuminate\Http\Request;
use App\Events\ObrMoved; 


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
        $obr = ObligationRequest::findOrFail($id);
        $obr->update([
            'status' => 'processing',
            'pc1_time_out' => now(), // THE STOPWATCH FOR PC 1
        ]);
        ObrMoved::dispatch();

        return back()->with('success', 'ObR sent to PC 2!');
    }

    // PC 2 finishes analyzing and passes it to PC 3
    public function pc2Process(Request $request, $id) 
    {
        $obr = ObligationRequest::findOrFail($id);
        $obr->update([
            'status' => 'in_transit',
            'analyze_control_data' => $request->analyze_control_data,
            'pc2_time_out' => now(), // THE STOPWATCH FOR PC 2
        ]);
        ObrMoved::dispatch();

        return back()->with('success', 'Processing done! Sent to PC 3.');
    }

    // PC 3 officially receives the paper on their desk
    public function pc3Receive($id)
    {
        $obr = ObligationRequest::findOrFail($id);
        $obr->update([
            'status' => 'pending_final_review', 
            'pc3_time_in' => now(), 
        ]);
        
        event(new ObrMoved()); 
        return redirect()->back();
    }

    // PC 3 finishes and returns it to PC 1
    public function pc3Release(Request $request, $id)
    {
        $obr = ObligationRequest::findOrFail($id);
        $obr->update([
            'status' => 'ready_for_release',
            'pc3_remarks' => $request->pc3_remarks,
            'pc3_signatory' => $request->pc3_signatory, // <-- FIXED: IT NOW CATCHES THE DROPDOWN
            'pc3_time_out' => now() 
        ]);
        ObrMoved::dispatch();

        return back()->with('success', 'Finalized! Returned to PC 1.');
    }

   public function finalRelease($id)
    {
        $obr = ObligationRequest::findOrFail($id);
        
        // 1. Capture the exact finish time
        $finishTime = now();
        
        // 2. AUTO-CALCULATION: Figure out the total minutes
        $startTime = \Carbon\Carbon::parse($obr->pc1_time_in);
        $totalMinutes = $startTime->diffInMinutes($finishTime);
        
        // 3. Save everything permanently to the database
        $obr->update([
            'status' => 'completed',
            'pc1_final_release' => $finishTime, 
            'total_minutes' => $totalMinutes 
        ]);
        ObrMoved::dispatch();

        return back()->with('success', 'ObR Officially Completed and Time Calculated!');
    }

    public function exportReport(Request $request) // <-- ADDED REQUEST HERE
    {
        // 1. Start the query for completed ObRs
        $query = ObligationRequest::where('status', 'completed')
                                  ->orderBy('created_at', 'desc');

        // 2. Apply the Date Filter if the user selected dates!
        if ($request->filled('start_date') && $request->filled('end_date')) {
            // We use startOfDay() and endOfDay() to capture the entire 24 hours of both days
            $start = \Carbon\Carbon::parse($request->start_date)->startOfDay();
            $end = \Carbon\Carbon::parse($request->end_date)->endOfDay();
            
            // Filter by the exact time the document was completely finalized
            $query->whereBetween('pc1_final_release', [$start, $end]);
        }

        // 3. Actually run the query to get the results
        $obrs = $query->get();

        // 4. Update the filename to show the requested date range
        $fileDateInfo = $request->filled('start_date') 
            ? "_{$request->start_date}_to_{$request->end_date}" 
            : "_" . date('Y-m-d');
            
        $filename = "ObR_Report" . $fileDateInfo . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'Date',                      
            'Time Received',             
            'ObR No.',                   
            'Time',                      
            'Analyze and Control',       
            'Time',                      
            'Checked and Clarified by:', 
            'Time Release',              
            'Total Time',                
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
            fputcsv($file, $columns); 

            foreach ($obrs as $obr) {
                $start = $obr->created_at;
                $pc1_done = $obr->pc1_time_out ? \Carbon\Carbon::parse($obr->pc1_time_out) : $start; 
                $pc2_done = $obr->pc2_time_out ? \Carbon\Carbon::parse($obr->pc2_time_out) : $pc1_done;
                $pc3_start = $obr->pc3_time_in ? \Carbon\Carbon::parse($obr->pc3_time_in) : $pc2_done;
                $final_done = $obr->updated_at;

                $pc1_mins = $start->diffInSeconds($pc1_done) / 60;
                $pc2_mins = $pc1_done->diffInSeconds($pc2_done) / 60;
                $pc3_total_mins = $pc3_start->diffInSeconds($final_done) / 60;
                $total_system_mins = $start->diffInSeconds($final_done) / 60;

                fputcsv($file, [
                    $obr->obr_date ? \Carbon\Carbon::parse($obr->obr_date)->format('M d, Y') : 'N/A', 
                    $start->format('h:i A'), 
                    $obr->obr_number, 
                    $formatTime($pc1_mins), 
                    $obr->analyze_control_data ?? 'N/A', 
                    $formatTime($pc2_mins), 
                    $obr->pc3_signatory ?? 'N/A', 
                    $formatTime($pc3_total_mins), 
                    $formatTime($total_system_mins), 
                    $obr->pc3_remarks ?? 'None' 
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

}