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
        Schema::table('products', function (Blueprint $table) {
            $table->json('tags')->nullable()->after('is_available');
            $table->json('images')->nullable()->after('image');
            $table->json('allergens')->nullable()->after('images');
            $table->integer('calories')->nullable()->after('allergens');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['tags', 'images', 'allergens', 'calories']);
        });
    }
};
