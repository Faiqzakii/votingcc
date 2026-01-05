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
        Schema::table('candidates', function (Blueprint $table) {
            $table->boolean('is_finalist')->default(false);
        });

        Schema::table('votes', function (Blueprint $table) {
            $table->integer('phase')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->dropColumn('is_finalist');
        });

        Schema::table('votes', function (Blueprint $table) {
            $table->dropColumn('phase');
        });
    }
};
