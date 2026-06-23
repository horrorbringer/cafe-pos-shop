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
        Schema::create('notification_rules', function (Blueprint $table) {
            $table->id();
            $table->string('event_code');
            $table->string('channel_code');
            $table->boolean('is_enabled')->default(false);
            $table->foreignId('template_id')->nullable()->constrained('settings')->nullOnDelete();
            $table->integer('cooldown_minutes')->default(60);
            $table->timestamps();

            $table->unique(['event_code', 'channel_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_rules');
    }
};
