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
        Schema::table('payments', function (Blueprint $table) {
            $table->string('provider_code')->nullable()->after('order_id');
            $table->string('currency')->default('USD')->after('amount');
            $table->string('status')->default('pending')->after('currency');
            $table->string('provider_reference')->nullable()->after('status');
            $table->json('provider_payload')->nullable()->after('provider_reference');
            $table->timestamp('paid_at')->nullable()->after('provider_payload');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'provider_code',
                'currency',
                'status',
                'provider_reference',
                'provider_payload',
                'paid_at',
            ]);
        });
    }
};
