<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the tracking events table.
     */
    public function up(): void
    {
        Schema::create('tracking_events', function (Blueprint $table) {
            $table->id();

            $table->foreignId('shipment_id')
                ->constrained('shipments')
                ->restrictOnDelete();

            $table->foreignId('location_id')
                ->nullable()
                ->constrained('locations')
                ->nullOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('event_type', 50);
            $table->string('status', 30)->nullable();

            $table->dateTime('event_at');

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->string('location_name', 150)->nullable();
            $table->text('description')->nullable();

            $table->timestamps();

            $table->index(['shipment_id', 'event_at']);
            $table->index('event_type');
            $table->index('status');
        });
    }

    /**
     * Drop the tracking events table.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracking_events');
    }
};