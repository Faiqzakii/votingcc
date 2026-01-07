<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\UniqueConstraintViolationException;
use App\Models\Vote;
use App\Models\Setting;
use App\Models\Candidate;

class VoteController extends Controller
{
    public function index()
    {
        $currentPhase = Setting::where('key', 'current_phase')->value('value') ?? 1;

        if ($currentPhase == 3) {
            return view('vote.closed');
        }

        if ($currentPhase == 2) {
            return redirect()->route('vote.phase2');
        }

        $user = Auth::user();
        
        // Check if user has voted
        if (Vote::where('user_id', $user->id)->where('phase', 1)->exists()) {
            return view('vote.done');
        }

        $candidates = Candidate::all();
        return view('vote.index', compact('candidates'));
    }

    public function store(Request $request)
    {
        $currentPhase = Setting::where('key', 'current_phase')->value('value') ?? 1;
        if ($currentPhase == 3) {
            return redirect()->route('vote.closed'); 
        }

        $request->validate([
            'candidate_1' => 'required',
            'candidate_2' => 'required|different:candidate_1',
            'candidate_3' => 'required|different:candidate_1|different:candidate_2',
        ], [
            'candidate_2.different' => 'Anda tidak dapat memilih kandidat yang sama dua kali.',
            'candidate_3.different' => 'Anda tidak dapat memilih kandidat yang sama dua kali.',
        ]);

        $user = Auth::user();

        try {
            DB::transaction(function () use ($request, $user) {
                // Lock check: Use lockForUpdate to prevent race conditions
                $existingVote = Vote::where('user_id', $user->id)
                    ->where('phase', 1)
                    ->lockForUpdate()
                    ->first();

                if ($existingVote) {
                    throw new \Exception('ALREADY_VOTED');
                }

                $votes = [
                    ['candidate_id' => $request->candidate_1, 'priority' => 1, 'points' => 5],
                    ['candidate_id' => $request->candidate_2, 'priority' => 2, 'points' => 3],
                    ['candidate_id' => $request->candidate_3, 'priority' => 3, 'points' => 1],
                ];

                foreach ($votes as $voteData) {
                    Vote::create([
                        'user_id' => $user->id,
                        'candidate_id' => $voteData['candidate_id'],
                        'priority' => $voteData['priority'],
                        'points' => $voteData['points'],
                        'phase' => 1,
                    ]);
                }
            });
        } catch (UniqueConstraintViolationException $e) {
            // Database constraint caught a duplicate - user already voted
            return redirect()->route('vote.done')->with('info', 'Suara Anda sudah tercatat sebelumnya.');
        } catch (\Exception $e) {
            if ($e->getMessage() === 'ALREADY_VOTED') {
                return redirect()->back()->withErrors(['msg' => 'Anda sudah memberikan suara.']);
            }
            throw $e;
        }

        return redirect()->route('vote.done')->with('success', 'Terima kasih telah berpartisipasi!');
    }

    public function closed() {
        return view('vote.closed');
    }

    public function done() {
        return view('vote.done');
    }

    // --- PHASE 2 ---

    public function indexPhase2()
    {
        $currentPhase = Setting::where('key', 'current_phase')->value('value') ?? 1;

        if ($currentPhase == 3) {
            return view('vote.closed');
        }

        if ($currentPhase == 1) {
            return redirect()->route('vote');
        }

        $user = Auth::user();
        
        // Check if user has voted in Phase 2
        if (Vote::where('user_id', $user->id)->where('phase', 2)->exists()) {
            return view('vote.done'); // Can use same done view or specific one
        }

        // Get finalists only and shuffle the order
        $candidates = Candidate::where('is_finalist', true)->get()->shuffle();

        if ($candidates->isEmpty()) {
            // Optional: Handle case where no finalists are set yet
            return view('vote.no_finalists'); // Need to create this or redirect
        }

        return view('vote.phase2', compact('candidates'));
    }

    public function storePhase2(Request $request)
    {
        $currentPhase = Setting::where('key', 'current_phase')->value('value') ?? 1;
        if ($currentPhase == 3) {
            return redirect()->route('vote.closed');
        }

        $request->validate([
            'candidate_1' => 'required',
            'candidate_2' => 'required|different:candidate_1',
            'candidate_3' => 'required|different:candidate_1|different:candidate_2',
            'candidate_4' => 'required|different:candidate_1|different:candidate_2|different:candidate_3',
            'candidate_5' => 'required|different:candidate_1|different:candidate_2|different:candidate_3|different:candidate_4',
        ], [
            'different' => 'Anda tidak dapat memilih kandidat yang sama lebih dari satu kali.',
        ]);

        $user = Auth::user();

        try {
            DB::transaction(function () use ($request, $user) {
                // Lock check: Use lockForUpdate to prevent race conditions
                $existingVote = Vote::where('user_id', $user->id)
                    ->where('phase', 2)
                    ->lockForUpdate()
                    ->first();

                if ($existingVote) {
                    throw new \Exception('ALREADY_VOTED');
                }

                $votes = [
                    ['candidate_id' => $request->candidate_1, 'priority' => 1, 'points' => 5],
                    ['candidate_id' => $request->candidate_2, 'priority' => 2, 'points' => 4],
                    ['candidate_id' => $request->candidate_3, 'priority' => 3, 'points' => 3],
                    ['candidate_id' => $request->candidate_4, 'priority' => 4, 'points' => 2],
                    ['candidate_id' => $request->candidate_5, 'priority' => 5, 'points' => 1],
                ];

                foreach ($votes as $voteData) {
                    Vote::create([
                        'user_id' => $user->id,
                        'candidate_id' => $voteData['candidate_id'],
                        'priority' => $voteData['priority'],
                        'points' => $voteData['points'],
                        'phase' => 2,
                    ]);
                }
            });
        } catch (UniqueConstraintViolationException $e) {
            // Database constraint caught a duplicate - user already voted
            return redirect()->route('vote.done')->with('info', 'Suara Anda sudah tercatat sebelumnya.');
        } catch (\Exception $e) {
            if ($e->getMessage() === 'ALREADY_VOTED') {
                return redirect()->back()->withErrors(['msg' => 'Anda sudah memberikan suara untuk Tahap 2.']);
            }
            throw $e;
        }

        return redirect()->route('vote.done')->with('success', 'Terima kasih telah berpartisipasi dalam Tahap 2!');
    }
}
