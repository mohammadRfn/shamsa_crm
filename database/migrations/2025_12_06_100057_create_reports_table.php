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
       Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); 
            $table->string('part_name');
            $table->date('request_date');
            $table->string('request_number');
            $table->string('serial_number');
            $table->string('device_model');
            $table->text('issue_description');
            $table->text('activity_report');
            $table->text('used_parts_list');
            $table->decimal('man_hours', 10, 2);
            $table->date('end_date');
            $table->boolean('technical_approval')->default(0);
            $table->boolean('request_approval')->default(0);
            $table->boolean('supply_approval')->default(0);
            $table->boolean('ceo_approval')->default(0);
            $table->enum('status', ['new', 'sent', 'pending', 'approved', 'rejected'])->default('new');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
