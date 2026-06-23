<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (! Schema::hasColumn('order_items', 'product_name')) {
                $table->string('product_name')->nullable()->after('product_id');
            }
            if (! Schema::hasColumn('order_items', 'variant_name')) {
                $table->string('variant_name')->nullable()->after('product_name');
            }
            if (! Schema::hasColumn('order_items', 'notes')) {
                $table->text('notes')->nullable()->after('total_price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['product_name', 'variant_name', 'notes']);
        });
    }
};
