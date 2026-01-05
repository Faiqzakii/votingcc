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
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('candidate_id')->constrained()->onDelete('cascade');
            $table->integer('priority'); // 1, 2, 3
            $table->integer('points'); // 5, 3, 1
            $table->unique(['user_id', 'priority']); // Prevent checking same priority twice? No, prevent voting same candidate?
            // Actually, logical constraint: One user 3 votes total.
            // unique(['user_id', 'candidate_id']) -> User cannot vote for same candidate twice
            $table->unique(['user_id', 'candidate_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
