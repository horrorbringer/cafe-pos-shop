<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE orders DROP CONSTRAINT IF EXISTS orders_status_check');
            DB::statement('ALTER TABLE payments DROP CONSTRAINT IF EXISTS payments_method_check');
            DB::statement('ALTER TABLE stock_movements DROP CONSTRAINT IF EXISTS stock_movements_type_check');
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->string('status', 50)->default('draft')->change();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->string('method', 50)->default('cash')->change();
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->string('type', 50)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('status', 50)->default('pending')->change();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->string('method', 50)->default('cash')->change();
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->string('type', 50)->change();
        });
    }
};
