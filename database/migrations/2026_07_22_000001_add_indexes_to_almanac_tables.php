<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Helper to add index safely if table exists
        Schema::table('administrative_divisions', function (Blueprint $table) {
            $table->index('country_id', 'idx_admin_div_country');
            $table->index('parent_id', 'idx_admin_div_parent');
            $table->index('type_id', 'idx_admin_div_type');
            $table->index('name', 'idx_admin_div_name');
        });

        Schema::table('village_groups', function (Blueprint $table) {
            $table->index('parent_id', 'idx_vg_parent');
            $table->index('name', 'idx_vg_name');
        });

        Schema::table('villages', function (Blueprint $table) {
            $table->index('village_group_id', 'idx_v_group');
            $table->index('name', 'idx_v_name');
        });

        Schema::table('advertisements', function (Blueprint $table) {
            $table->index('position', 'idx_ad_position');
            $table->index('type', 'idx_ad_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('administrative_divisions', function (Blueprint $table) {
            $table->dropIndex('idx_admin_div_country');
            $table->dropIndex('idx_admin_div_parent');
            $table->dropIndex('idx_admin_div_type');
            $table->dropIndex('idx_admin_div_name');
        });

        Schema::table('village_groups', function (Blueprint $table) {
            $table->dropIndex('idx_vg_parent');
            $table->dropIndex('idx_vg_name');
        });

        Schema::table('villages', function (Blueprint $table) {
            $table->dropIndex('idx_v_group');
            $table->dropIndex('idx_v_name');
        });

        Schema::table('advertisements', function (Blueprint $table) {
            $table->dropIndex('idx_ad_position');
            $table->dropIndex('idx_ad_type');
        });
    }
};
