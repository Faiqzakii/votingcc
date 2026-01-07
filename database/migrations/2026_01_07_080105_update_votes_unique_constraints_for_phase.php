<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Updates unique constraints on votes table to include 'phase' column,
     * allowing users to vote in multiple phases without constraint violations.
     */
    public function up(): void
    {
        // Get current indexes to check what needs to be dropped
        $indexes = Schema::getIndexes('votes');
        $indexNames = array_column($indexes, 'name');
        
        // Strategy: Create new indexes first, then drop old ones
        // This ensures foreign keys always have a supporting index
        
        // Create new unique constraints that include phase (if they don't exist)
        if (!in_array('votes_user_id_priority_phase_unique', $indexNames)) {
            DB::statement('ALTER TABLE votes ADD UNIQUE votes_user_id_priority_phase_unique (user_id, priority, phase)');
        }
        if (!in_array('votes_user_id_candidate_id_phase_unique', $indexNames)) {
            DB::statement('ALTER TABLE votes ADD UNIQUE votes_user_id_candidate_id_phase_unique (user_id, candidate_id, phase)');
        }
        
        // Now safe to drop old unique constraints since new ones exist
        // Refresh index list after adding new ones
        $indexes = Schema::getIndexes('votes');
        $indexNames = array_column($indexes, 'name');
        
        if (in_array('votes_user_id_priority_unique', $indexNames)) {
            DB::statement('ALTER TABLE votes DROP INDEX votes_user_id_priority_unique');
        }
        if (in_array('votes_user_id_candidate_id_unique', $indexNames)) {
            DB::statement('ALTER TABLE votes DROP INDEX votes_user_id_candidate_id_unique');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Create old constraints first
        DB::statement('ALTER TABLE votes ADD UNIQUE votes_user_id_priority_unique (user_id, priority)');
        DB::statement('ALTER TABLE votes ADD UNIQUE votes_user_id_candidate_id_unique (user_id, candidate_id)');
        
        // Then drop the new ones
        DB::statement('ALTER TABLE votes DROP INDEX votes_user_id_priority_phase_unique');
        DB::statement('ALTER TABLE votes DROP INDEX votes_user_id_candidate_id_phase_unique');
    }
};
