<?php

namespace App\Console\Commands;

use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ArchiveOldVisits extends Command
{
    protected $signature   = 'visits:archive-old';
    protected $description = 'Archive visits >1 year old; soft-delete archived visits >2 years old.';

    public function handle(): void
    {
        $now = Carbon::now();

        $archived = Visit::whereNull('archived_at')
            ->where('status', Visit::STATUS_FINALIZED)
            ->where('visited_at', '<', $now->copy()->subYear())
            ->update(['archived_at' => $now]);
        $this->info("Archived {$archived} visit(s) older than 1 year.");

        $toDelete = Visit::withoutTrashed()
            ->whereNotNull('archived_at')
            ->where('visited_at', '<', $now->copy()->subYears(2))
            ->get();
        $count = 0;
        foreach ($toDelete as $visit) { $visit->delete(); $count++; }
        $this->info("Soft-deleted {$count} archived visit(s) older than 2 years.");
    }
}
