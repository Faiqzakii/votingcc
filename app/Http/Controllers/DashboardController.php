<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $phase = $request->get('phase', 1);
        
        // Only accept phase 1 or 2
        if (!in_array($phase, [1, 2, '1', '2'])) {
            $phase = 1;
        }
        $phase = (int) $phase;

        // Get candidates with sum of scores, ordered by highest score
        $candidatesQuery = \App\Models\Candidate::withSum(['votes' => function($query) use ($phase) {
                $query->where('phase', $phase);
            }], 'points');
        
        // For Phase 2, only show finalists in the ranking
        if ($phase == 2) {
            $candidatesQuery->where('is_finalist', true);
        }
        
        $candidates = $candidatesQuery->orderByDesc('votes_sum_points')->get();
            
        // Total voters
        $totalVoters = \App\Models\Vote::where('phase', $phase)
            ->distinct('user_id')
            ->count('user_id');

        // Total possible voters (assuming all non-admin users)
        $totalUsers = \App\Models\User::where('email', '!=', 'admin@example.com')->count();
        $participationRate = $totalUsers > 0 ? ($totalVoters / $totalUsers) * 100 : 0;
        
        $currentPhase = \App\Models\Setting::where('key', 'current_phase')->value('value') ?? 1;

        return view('dashboard.index', compact('candidates', 'totalVoters', 'totalUsers', 'participationRate', 'phase', 'currentPhase'));
    }

    public function updatePhase(Request $request)
    {
        $request->validate([
            'phase' => 'required|in:1,2,3',
        ]);

        \App\Models\Setting::updateOrCreate(
            ['key' => 'current_phase'],
            ['value' => $request->phase]
        );

        $msg = $request->phase == 3 ? 'Voting berhasil ditutup.' : 'Tahap voting berhasil diperbarui ke Tahap ' . $request->phase;

        return redirect()->back()->with('success', $msg);
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

    public function export(Request $request)
    {
        $phase = $request->get('phase', 1);
        
        // Ensure phase is 1 or 2
        if (!in_array($phase, [1, 2])) {
            $phase = 1;
        }

        $filename = 'hasil_voting_tahap_' . $phase . '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\VoteResultsExport($phase),
            $filename
        );
    }
}
