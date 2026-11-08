
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the locations table.
     */
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();

            $table->string('code', 30)->nullable()->unique();
            $table->string('name', 150);

            $table->string('location_type', 30);
            $table->string('address')->nullable();

            $table->string('city', 100);
            $table->string('state_province', 100)->nullable();
            $table->string('country', 100);
            $table->char('country_code', 2)->nullable();

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index('name');
            $table->index('location_type');
            $table->index(['city', 'country']);
            $table->index('is_active');
        });
    }

    /**
     * Drop the locations table.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
