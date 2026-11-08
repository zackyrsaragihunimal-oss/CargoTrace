
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add CargoTrace user profile and access control fields.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)
                ->default('customer')
                ->after('password');

            $table->string('phone', 25)
                ->nullable()
                ->after('name');

            $table->boolean('is_active')
                ->default(true)
                ->after('role');

            $table->index('role');
            $table->index('is_active');
        });
    }

    /**
     * Remove CargoTrace user profile and access control fields.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropIndex(['is_active']);

            $table->dropColumn([
                'role',
                'phone',
                'is_active',
            ]);
        });
    }
};
