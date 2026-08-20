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
        Schema::table('villages', function (Blueprint $table) {
            $table->text('carousel_images')->nullable()->after('chief_image');
        });

        Schema::table('village_groups', function (Blueprint $table) {
            $table->text('carousel_images')->nullable()->after('image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('villages', function (Blueprint $table) {
            $table->dropColumn('carousel_images');
        });

        Schema::table('village_groups', function (Blueprint $table) {
            $table->dropColumn('carousel_images');
        });
    }
};
