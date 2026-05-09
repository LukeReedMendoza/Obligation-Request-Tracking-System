<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObligationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'obr_number', 
        'obr_date', 
        'status', 
        'analyze_control_data', 
        'pc3_remarks', 
        'pc3_signatory',     // Confirmed: The security lock is open for this
        'pc1_time_in', 
        'pc1_time_out',      
        'pc2_time_out',      
        'pc3_time_in',       
        'pc3_time_out',      
        'pc1_final_release',
        'total_minutes' 
    ];

    protected $casts = [
        'obr_date' => 'date',
        'pc1_time_in' => 'datetime',
        'pc1_time_out' => 'datetime',
        'pc2_time_out' => 'datetime',
        'pc3_time_in' => 'datetime',
        'pc3_time_out' => 'datetime',
        'pc1_final_release' => 'datetime',
    ];
}