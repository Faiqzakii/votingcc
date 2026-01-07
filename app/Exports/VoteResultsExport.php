<?php

namespace App\Exports;

use App\Models\Candidate;
use App\Models\Vote;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VoteResultsExport implements WithMultipleSheets
{
    protected $phase;

    public function __construct($phase)
    {
        $this->phase = $phase;
    }

    public function sheets(): array
    {
        return [
            new RankingSheet($this->phase),
            new VoteDistributionSheet($this->phase),
        ];
    }
}

class RankingSheet implements FromArray, WithTitle, WithHeadings, WithStyles
{
    protected $phase;

    public function __construct($phase)
    {
        $this->phase = $phase;
    }

    public function title(): string
    {
        return 'Rekapitulasi Tahap ' . $this->phase;
    }

    public function headings(): array
    {
        return [
            'Peringkat',
            'Nama Kandidat',
            'Deskripsi',
            'Total Poin',
            'Jumlah Pemilih',
        ];
    }

    public function array(): array
    {
        $candidatesQuery = Candidate::withSum(['votes' => function($query) {
            $query->where('phase', $this->phase);
        }], 'points');

        // For Phase 2, only include finalists
        if ($this->phase == 2) {
            $candidatesQuery->where('is_finalist', true);
        }

        $candidates = $candidatesQuery->orderByDesc('votes_sum_points')->get();

        $data = [];
        foreach ($candidates as $index => $candidate) {
            $voterCount = Vote::where('candidate_id', $candidate->id)
                ->where('phase', $this->phase)
                ->distinct('user_id')
                ->count('user_id');

            $data[] = [
                $index + 1,
                $candidate->name,
                $candidate->description,
                $candidate->votes_sum_points ?? 0,
                $voterCount,
            ];
        }

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

class VoteDistributionSheet implements FromArray, WithTitle, WithHeadings, WithStyles
{
    protected $phase;

    public function __construct($phase)
    {
        $this->phase = $phase;
    }

    public function title(): string
    {
        return 'Sebaran Suara Tahap ' . $this->phase;
    }

    public function headings(): array
    {
        if ($this->phase == 2) {
            return [
                'Peringkat',
                'Nama Kandidat',
                'Rank 1 (5 poin)',
                'Rank 2 (4 poin)',
                'Rank 3 (3 poin)',
                'Rank 4 (2 poin)',
                'Rank 5 (1 poin)',
                'Total Poin',
            ];
        }

        return [
            'Peringkat',
            'Nama Kandidat',
            'Rank 1 (5 poin)',
            'Rank 2 (3 poin)',
            'Rank 3 (1 poin)',
            'Total Poin',
        ];
    }

    public function array(): array
    {
        $candidatesQuery = Candidate::withSum(['votes' => function($query) {
            $query->where('phase', $this->phase);
        }], 'points');

        // For Phase 2, only include finalists
        if ($this->phase == 2) {
            $candidatesQuery->where('is_finalist', true);
        }

        $candidates = $candidatesQuery->orderByDesc('votes_sum_points')->get();

        $data = [];
        foreach ($candidates as $index => $candidate) {
            $row = [
                $index + 1,
                $candidate->name,
            ];

            if ($this->phase == 2) {
                for ($priority = 1; $priority <= 5; $priority++) {
                    $row[] = Vote::where('candidate_id', $candidate->id)
                        ->where('phase', $this->phase)
                        ->where('priority', $priority)
                        ->count();
                }
            } else {
                for ($priority = 1; $priority <= 3; $priority++) {
                    $row[] = Vote::where('candidate_id', $candidate->id)
                        ->where('phase', $this->phase)
                        ->where('priority', $priority)
                        ->count();
                }
            }

            $row[] = $candidate->votes_sum_points ?? 0;
            $data[] = $row;
        }

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
