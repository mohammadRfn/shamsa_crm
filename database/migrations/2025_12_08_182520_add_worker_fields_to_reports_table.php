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
        Schema::table('reports', function (Blueprint $table) {
            $table->integer('workers_count')->nullable()->after('used_parts_list');
            $table->decimal('hours_per_worker', 10, 2)->nullable()->after('workers_count');
            $table->decimal('total_man_hours', 10, 2)->nullable()->after('hours_per_worker');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn(['workers_count', 'hours_per_worker', 'total_man_hours']);
        });
    }
};
