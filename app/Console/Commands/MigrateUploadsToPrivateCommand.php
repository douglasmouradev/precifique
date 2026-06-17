<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateUploadsToPrivateCommand extends Command
{
    protected $signature = 'precifique:migrate-uploads-to-private
                            {--dry-run : Apenas lista arquivos sem mover}';

    protected $description = 'Move fotos/logos do disco public para uploads privado';

    public function handle(): int
    {
        $public = Storage::disk('public');
        $private = Storage::disk('uploads');
        $dryRun = (bool) $this->option('dry-run');
        $moved = 0;

        foreach (['products', 'logos'] as $prefix) {
            if (! $public->exists($prefix)) {
                continue;
            }

            foreach ($public->allFiles($prefix) as $path) {
                if ($dryRun) {
                    $this->line("Moveria: {$path}");
                    $moved++;

                    continue;
                }

                if ($private->exists($path)) {
                    continue;
                }

                $private->put($path, $public->get($path));
                $public->delete($path);
                $moved++;
            }
        }

        $this->info($dryRun
            ? "Dry-run: {$moved} arquivo(s) seriam migrados."
            : "Migrados {$moved} arquivo(s) para disco privado.");

        if (! $dryRun && $moved > 0) {
            $this->comment('Configure nginx para bloquear /storage/products/ e /storage/logos/');
        }

        return self::SUCCESS;
    }
}
