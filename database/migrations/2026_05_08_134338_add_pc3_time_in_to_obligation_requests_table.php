<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('obligation_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('obligation_requests', 'pc3_time_in')) {
                $table->timestamp('pc3_time_in')->nullable()->after('pc2_time_out');
            }
        });
    }

    public function down(): void
    {
        Schema::table('obligation_requests', function (Blueprint $table) {
            $table->dropColumn('pc3_time_in');
        });
    }
};