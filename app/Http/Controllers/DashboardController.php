<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $phase = $request->get('phase', 1);

        // Get candidates with sum of scores, ordered by highest score
        $candidates = \App\Models\Candidate::withSum(['votes' => function($query) use ($phase) {
                if ($phase != 'all') {
                    $query->where('phase', $phase);
                }
            }], 'points')
            ->orderByDesc('votes_sum_points')
            ->get();
            
        // Total voters
        $totalVotersQuery = \App\Models\Vote::distinct('user_id');
        if ($phase != 'all') {
            $totalVotersQuery->where('phase', $phase);
        }
        $totalVoters = $totalVotersQuery->count('user_id');

        // Total possible voters (assuming all non-admin users)
        $totalUsers = \App\Models\User::where('email', '!=', 'admin@example.com')->count();
        $participationRate = $totalUsers > 0 ? ($totalVoters / $totalUsers) * 100 : 0;
        
        $currentPhase = \App\Models\Setting::where('key', 'current_phase')->value('value') ?? 1;

        return view('dashboard.index', compact('candidates', 'totalVoters', 'totalUsers', 'participationRate', 'phase', 'currentPhase'));
    }

    public function updatePhase(Request $request)
    {
        $request->validate([
            'phase' => 'required|in:1,2',
        ]);

        \App\Models\Setting::updateOrCreate(
            ['key' => 'current_phase'],
            ['value' => $request->phase]
        );

        return redirect()->back()->with('success', 'Tahap voting berhasil diperbarui ke Tahap ' . $request->phase);
    }

    public function generateDummyVotes(Request $request)
    {
        $phase = \App\Models\Setting::where('key', 'current_phase')->value('value') ?? 1;
        $users = \App\Models\User::where('email', '!=', 'admin@example.com')
            ->whereDoesntHave('votes', function($q) use ($phase) {
                $q->where('phase', $phase);
            })
            ->get();

        if ($users->isEmpty()) {
            return redirect()->back()->with('error', 'Semua pengguna sudah melakukan voting pada fase ini.');
        }

        $candidates = \App\Models\Candidate::all(); // Retrieve once

        // Filter finalists for Phase 2
        if ($phase == 2) {
            $candidates = $candidates->where('is_finalist', true)->values();
        }

        if ($candidates->count() < ($phase == 2 ? 5 : 3)) {
             return redirect()->back()->with('error', 'Kandidat tidak cukup untuk melakukan voting (Butuh ' . ($phase == 2 ? 5 : 3) . ').');
        }

        $voteCount = 0;

        foreach ($users as $user) {
            // Randomly select distinct candidates
            $randomCandidates = $candidates->random($phase == 2 ? 5 : 3);
            
            // Phase 1: 5, 3, 1
            // Phase 2: 5, 4, 3, 2, 1
            $points = $phase == 2 ? [5, 4, 3, 2, 1] : [5, 3, 1];
            
            foreach ($randomCandidates as $index => $candidate) {
                \App\Models\Vote::create([
                    'user_id' => $user->id,
                    'candidate_id' => $candidate->id,
                    'priority' => $index + 1,
                    'points' => $points[$index],
                    'phase' => $phase,
                ]);
            }
            $voteCount++;
        }

        return redirect()->back()->with('success', "Berhasil menambahkan dummy vote untuk $voteCount pengguna.");
    }
}
