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
}
