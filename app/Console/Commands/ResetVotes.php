<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ResetVotes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'votes:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset votes data, phase setting, and candidate finalist status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->confirm('This will delete ALL votes and reset the voting phase. Are you sure?')) {
            $this->info('Operation cancelled.');
            return;
        }

        $this->info('Resetting votes...');
        \Illuminate\Support\Facades\DB::table('votes')->truncate();
        
        $this->info('Resetting phase to 1...');
        \App\Models\Setting::updateOrCreate(
            ['key' => 'current_phase'],
            ['value' => 1]
        );

        $this->info('Clearing finalist status from candidates...');
        \App\Models\Candidate::query()->update(['is_finalist' => false]);

        $this->info('Vote system reset successfully!');
    }
}
