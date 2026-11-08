<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the shipment exceptions table.
     */
    public function up(): void
    {
        Schema::create('shipment_exceptions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('shipment_id')
                ->constrained('shipments')
                ->restrictOnDelete();

            $table->foreignId('tracking_event_id')
                ->nullable()
                ->constrained('tracking_events')
                ->nullOnDelete();

            $table->foreignId('reported_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('exception_code', 50);
            $table->string('title', 150);
            $table->text('description')->nullable();

            $table->string('severity', 20)->default('MEDIUM');
            $table->string('status', 20)->default('OPEN');

            $table->dateTime('detected_at');
            $table->dateTime('resolved_at')->nullable();

            $table->text('resolution_notes')->nullable();

            $table->timestamps();

            $table->index(['shipment_id', 'status']);
            $table->index(['severity', 'status']);
            $table->index('exception_code');
            $table->index('detected_at');
        });
    }

    /**
     * Drop the shipment exceptions table.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_exceptions');
    }
};