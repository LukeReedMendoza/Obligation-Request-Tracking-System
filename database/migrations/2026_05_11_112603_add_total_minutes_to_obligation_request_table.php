<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('obligation_requests', function (Blueprint $table) {
            // We use float or decimal to store the calculated minutes (e.g., 1.38 mins)
            $table->decimal('total_minutes', 10, 2)->nullable()->after('pc1_final_release');
        });
    }

    public function down(): void
    {
        Schema::table('obligation_requests', function (Blueprint $table) {
            $table->dropColumn('total_minutes');
        });
    }
};