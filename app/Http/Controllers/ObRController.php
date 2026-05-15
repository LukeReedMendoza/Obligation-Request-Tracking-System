<?php

namespace App\Http\Controllers;

use App\Models\ObligationRequest;
use Illuminate\Http\Request;
use App\Events\ObrMoved; 
use Carbon\Carbon;       

class ObRController extends Controller
{
    public function dashboard($role)
    {
        $requests = ObligationRequest::where('status', '!=', 'completed')->get();
        return view('dashboard', compact('requests', 'role'));
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

        ObrMoved::dispatch($obr);

        return back()->with('success', 'ObR Logged Successfully!');
    }

    public function pc1Release($id) 
    {
        $obr = ObligationRequest::find($id); 
        if (!$obr) return back(); 

        $obr->update([
            'status' => 'processing',
            'pc1_time_out' => now(), 
        ]);

        ObrMoved::dispatch($obr);

        return back()->with('success', 'ObR sent to PC 2!');
    }

    public function pc2Process(Request $request, $id) 
    {
        $obr = ObligationRequest::find($id); 
        if (!$obr) return back(); 

        $obr->update([
            'status' => 'in_transit',
            'analyze_control_data' => $request->analyze_control_data,
            'pc2_time_out' => now(), 
        ]);

        ObrMoved::dispatch($obr);

        return back()->with('success', 'Processing done! Sent to PC 3.');
    }

    public function pc3Receive($id)
    {
        $obr = ObligationRequest::find($id); 
        if (!$obr) return back(); 

        $obr->update([
            'status' => 'pending_final_review', 
            'pc3_time_in' => now(), 
        ]);
        
        ObrMoved::dispatch($obr);

        return redirect()->back();
    }

    public function pc3Release(Request $request, $id)
    {
        $obr = ObligationRequest::find($id); 
        if (!$obr) return back(); 

        $obr->update([
            'status' => 'ready_for_release',
            'pc3_remarks' => $request->pc3_remarks,
            'pc3_signatory' => $request->pc3_signatory, 
            'pc3_time_out' => now() 
        ]);

        ObrMoved::dispatch($obr);

        return back()->with('success', 'Finalized! Transferred to PC 4.');
    }

    // UPDATED: Now accepts a Request to save the preview edits!
    public function finalRelease(Request $request, $id)
    {
        $obr = ObligationRequest::find($id); 
        if (!$obr) return back(); 
        
        $finishTime = now();
        
        $startTime = $obr->pc1_time_in ? Carbon::parse($obr->pc1_time_in) : $obr->created_at;
        $totalMinutes = $startTime->diffInSeconds($finishTime) / 60;
        
        $obr->update([
            'analyze_control_data' => $request->has('analyze_control_data') ? $request->analyze_control_data : $obr->analyze_control_data,
            'pc3_signatory' => $request->has('pc3_signatory') ? $request->pc3_signatory : $obr->pc3_signatory,
            'pc3_remarks' => $request->has('pc3_remarks') ? $request->pc3_remarks : $obr->pc3_remarks,
            'status' => 'completed',
            'pc1_final_release' => $finishTime, 
            'total_minutes' => $totalMinutes 
        ]);

        ObrMoved::dispatch($obr);

        return back()->with('success', 'ObR Officially Completed and Time Calculated!');
    }

    public function exportReport(Request $request) 
    {
        try {
            $query = ObligationRequest::where('status', 'completed')
                                      ->orderBy('created_at', 'desc');

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $start = Carbon::parse($request->start_date)->startOfDay();
                $end = Carbon::parse($request->end_date)->endOfDay();
                
                $query->whereBetween('pc1_final_release', [$start, $end]);
            }

            $obrs = $query->get();

            $fileDateInfo = $request->filled('start_date') 
                ? "_{$request->start_date}_to_{$request->end_date}" 
                : "_" . date('Y-m-d');
                
            $filename = "ObR_Report" . $fileDateInfo . ".csv";

            // FIXED: Updated column headers for Process durations
            $columns = [
                'Date', 'Time Received', 'ObR No.', 'Process 1 Duration',
                'Time (PC 1 Done)', 'Analyze and Control', 'Process 2 Duration',
                'Checked and Clarified by', 'Process 3 Duration',
                'Time Released', 'Remarks'                    
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
                    $start = $obr->created_at ? Carbon::parse($obr->created_at) : now();
                    $pc1_in = $obr->pc1_time_in ? Carbon::parse($obr->pc1_time_in) : $start;
                    $pc1_out = $obr->pc1_time_out ? Carbon::parse($obr->pc1_time_out) : null;
                    $pc2_out = $obr->pc2_time_out ? Carbon::parse($obr->pc2_time_out) : null;
                    $pc3_in = $obr->pc3_time_in ? Carbon::parse($obr->pc3_time_in) : null;
                    $pc3_out = $obr->pc3_time_out ? Carbon::parse($obr->pc3_time_out) : null;
                    $final_done = $obr->pc1_final_release ? Carbon::parse($obr->pc1_final_release) : ($obr->updated_at ? Carbon::parse($obr->updated_at) : ($pc3_out ?? $start));

                    $pc1_duration = ($pc1_in && $pc1_out) ? $formatTime($pc1_in->diffInSeconds($pc1_out) / 60) : 'N/A';
                    $pc2_duration = ($pc1_out && $pc2_out) ? $formatTime($pc1_out->diffInSeconds($pc2_out) / 60) : 'N/A';
                    $pc3_duration = ($pc3_in && $pc3_out) ? $formatTime($pc3_in->diffInSeconds($pc3_out) / 60) : 'N/A';

                    $pc1_done_time = $pc1_out ? $pc1_out->format('h:i A') : 'N/A';

                    fputcsv($file, [
                        $obr->obr_date ? Carbon::parse($obr->obr_date)->format('M d, Y') : 'N/A', 
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