<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('obligation_requests', function (Blueprint $table) {
            $table->id();
            $table->string('obr_number')->unique();
            $table->date('obr_date');
            
            // PC 1
            $table->timestamp('pc1_time_in')->nullable();
            $table->timestamp('pc1_time_out')->nullable(); 
            
            // PC 2
            $table->string('analyze_control_data')->nullable();
            $table->timestamp('pc2_time_out')->nullable();
            
            // PC 3
            $table->timestamp('pc3_time_in')->nullable();
            $table->string('pc3_signatory')->nullable();
            $table->text('pc3_remarks')->nullable();
            $table->timestamp('pc3_time_out')->nullable();
            
            // PC 1 Final
            $table->timestamp('pc1_final_release')->nullable();
            
            // ADDED: Required for the final calculation in the Controller
            $table->decimal('total_minutes', 8, 2)->nullable();
            
            // FIXED: 'final_boss' changed to 'pending_final_review' to match controller
            $table->enum('status', [
                'entry', 'processing', 'in_transit', 'pending_final_review', 'ready_for_release', 'completed'
            ])->default('entry');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('obligation_requests');
    }
};