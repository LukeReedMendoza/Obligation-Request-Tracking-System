<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('obligation_requests', function (Blueprint $table) {
            // Check if pc1_time_out is missing before adding it
            if (!Schema::hasColumn('obligation_requests', 'pc1_time_out')) {
                $table->timestamp('pc1_time_out')->nullable()->after('pc1_time_in');
            }
            
            // Check if pc2_time_out is missing before adding it
            if (!Schema::hasColumn('obligation_requests', 'pc2_time_out')) {
                $table->timestamp('pc2_time_out')->nullable()->after('pc1_time_out');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('obligation_requests', function (Blueprint $table) {
            $table->dropColumn(['pc1_time_out', 'pc2_time_out']);
        });
    }
};