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
          Schema::create('part_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); 
            $table->string('equipment_name');
            $table->date('order_date');
            $table->string('order_number');
            $table->string('part_name');
            $table->text('specifications');
            $table->string('package');
            $table->integer('quantity');
            $table->text('description');
            $table->boolean('supply_approval')->default(0);
            $table->boolean('ceo_approval')->default(0);
            $table->enum('status', ['new', 'sent', 'pending', 'approved', 'failed'])->default('new');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('part_orders');
    }
};
