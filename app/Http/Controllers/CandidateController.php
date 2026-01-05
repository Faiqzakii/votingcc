<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CandidateController extends Controller
{
    public function index()
    {
        $candidates = \App\Models\Candidate::all();
        return view('candidates.index', compact('candidates'));
    }

    public function create()
    {
        return view('candidates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'photo_url' => 'nullable|url', // Optional, if using external URL
        ]);

        \App\Models\Candidate::create([
            'name' => $request->name,
            'description' => $request->description,
            'photo_path' => $request->photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($request->name) . '&background=random',
        ]);

        return redirect()->route('candidates.index')->with('success', 'Kandidat berhasil dibuat.');
    }

    public function edit(\App\Models\Candidate $candidate)
    {
        return view('candidates.edit', compact('candidate'));
    }

    public function update(Request $request, \App\Models\Candidate $candidate)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'photo_url' => 'nullable|url',
        ]);

        $candidate->update([
            'name' => $request->name,
            'description' => $request->description,
            'photo_path' => $request->photo_url ?? $candidate->photo_path,
            'is_finalist' => $request->has('is_finalist'),
        ]);

        return redirect()->route('candidates.index')->with('success', 'Kandidat berhasil diperbarui.');
    }

    public function destroy(\App\Models\Candidate $candidate)
    {
        $candidate->delete();
        return redirect()->route('candidates.index')->with('success', 'Kandidat berhasil dihapus.');
    }
}
