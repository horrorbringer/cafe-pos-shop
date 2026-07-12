<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->index('created_at');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index('is_available');
        });

        Schema::table('notification_logs', function (Blueprint $table) {
            $table->index('status');
            $table->index('event_code');
            $table->index('channel_code');
            $table->index('sent_at');
        });

        Schema::table('notification_recipients', function (Blueprint $table) {
            $table->index('channel_code');
            $table->index('is_active');
        });

        Schema::table('notification_rules', function (Blueprint $table) {
            $table->index('channel_code');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['is_available']);
        });

        Schema::table('notification_logs', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['event_code']);
            $table->dropIndex(['channel_code']);
            $table->dropIndex(['sent_at']);
        });

        Schema::table('notification_recipients', function (Blueprint $table) {
            $table->dropIndex(['channel_code']);
            $table->dropIndex(['is_active']);
        });

        Schema::table('notification_rules', function (Blueprint $table) {
            $table->dropIndex(['channel_code']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });
    }
};
