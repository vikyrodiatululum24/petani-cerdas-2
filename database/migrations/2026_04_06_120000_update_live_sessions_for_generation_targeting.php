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
        Schema::table('live_sessions', function (Blueprint $table): void {
            $table->string('generation')->nullable()->after('subscription_id');
            $table->index('generation');

            $table->foreignId('user_id')->nullable()->change();
            $table->foreignId('subscription_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('live_sessions', function (Blueprint $table): void {
            $table->dropIndex(['generation']);
            $table->dropColumn('generation');

            $table->foreignId('user_id')->nullable(false)->change();
            $table->foreignId('subscription_id')->nullable(false)->change();
        });
    }
};
