<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Vote;

class ResetPhase2Votes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'votes:reset-phase2 {--force : Skip confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset (delete) all votes from Phase 2 only';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = Vote::where('phase', 2)->count();

        if ($count === 0) {
            $this->info('No Phase 2 votes found. Nothing to delete.');
            return 0;
        }

        $this->warn("Found {$count} Phase 2 votes to delete.");

        if (!$this->option('force')) {
            if (!$this->confirm('Are you sure you want to delete ALL Phase 2 votes? This action cannot be undone.')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        $deleted = Vote::where('phase', 2)->delete();

        $this->info("Successfully deleted {$deleted} Phase 2 votes.");

        return 0;
    }
}
