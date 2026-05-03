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
        Schema::create('inventory_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('product_id')->constrained();
    $table->foreignId('product_variant_id')->nullable()->constrained();
    $table->enum('type', ['purchase', 'sale', 'return', 'adjustment']);
    $table->integer('quantity_change');
    $table->integer('quantity_before');
    $table->integer('quantity_after');
    $table->timestamps();
    
    $table->index('product_id');
    $table->index('created_at');
    $table->index('type');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_logs');
    }
};
