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
        if (!Schema::hasTable('ad_views')) {
            Schema::create('ad_views', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('advertisement_id');
                $table->string('ip_address', 45)->nullable();
                $table->string('session_id', 100)->nullable();
                $table->string('user_agent_hash', 64)->nullable();
                $table->timestamps();

                $table->index(['advertisement_id', 'ip_address', 'session_id'], 'ad_unique_view_idx');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ad_views');
    }
};
