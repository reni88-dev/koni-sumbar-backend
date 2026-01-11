<?php

namespace App\Console\Commands;

use App\Models\Athlete;
use Illuminate\Console\Command;

class BackfillNikHash extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'athletes:backfill-nik-hash';

    /**
     * The console command description.
     */
    protected $description = 'Backfill NIK hash for existing athletes';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting NIK hash backfill...');

        $athletes = Athlete::whereNotNull('nik')
            ->whereNull('nik_hash')
            ->get();

        $count = $athletes->count();

        if ($count === 0) {
            $this->info('No athletes need hash backfill.');
            return Command::SUCCESS;
        }

        $this->info("Found {$count} athletes to backfill.");

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        foreach ($athletes as $athlete) {
            // The HasBlindIndex trait will auto-generate hash on save
            // We just need to trigger a save
            $athlete->nik_hash = Athlete::generateBlindIndex($athlete->nik);
            $athlete->saveQuietly(); // Save without firing events
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('NIK hash backfill completed!');

        return Command::SUCCESS;
    }
}
