<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the shipment items table.
     */
    public function up(): void
    {
        Schema::create('shipment_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('shipment_id')
                ->constrained('shipments')
                ->cascadeOnDelete();

            $table->string('item_name', 150);
            $table->string('description', 500)->nullable();

            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_weight_kg', 12, 3)->nullable();
            $table->decimal('unit_volume_m3', 12, 3)->nullable();

            $table->timestamps();

            $table->index('item_name');
        });
    }

    /**
     * Drop the shipment items table.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_items');
    }
};