
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the shipments table.
     */
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();

            $table->string('shipment_number', 50)->unique();

            $table->foreignId('customer_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('carrier_id')
                ->constrained('carriers')
                ->restrictOnDelete();

            $table->foreignId('origin_location_id')
                ->constrained('locations')
                ->restrictOnDelete();

            $table->foreignId('destination_location_id')
                ->constrained('locations')
                ->restrictOnDelete();

            $table->string('cargo_type', 100);

            $table->decimal('total_weight_kg', 12, 3)->nullable();
            $table->decimal('total_volume_m3', 12, 3)->nullable();

            $table->dateTime('expected_departure_at');
            $table->dateTime('expected_arrival_at');

            $table->dateTime('estimated_arrival_at')->nullable();
            $table->dateTime('actual_departure_at')->nullable();
            $table->dateTime('actual_arrival_at')->nullable();

            $table->string('current_status', 30)
                ->default('CREATED');

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index('current_status');
            $table->index('expected_arrival_at');
            $table->index('estimated_arrival_at');
            $table->index(['customer_id', 'current_status']);
            $table->index(['carrier_id', 'current_status']);
        });
    }

    /**
     * Drop the shipments table.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
