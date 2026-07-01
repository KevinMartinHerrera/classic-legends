<?php

namespace App\Console\Commands;

use App\Services\YupooImporter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

abstract class AbstractYupooSyncCommand extends Command
{
    protected function syncPage(YupooImporter $importer, int $page): array
    {
        $outputFile = $this->pageOutputFile($page);
        File::ensureDirectoryExists(dirname($outputFile));

        $this->info("Pagina {$page}: ejecutando Playwright...");

        $process = new Process([
            'node',
            'scraper/scrape.js',
            '--pages=1',
            '--start-page='.$page,
            '--output-file='.$outputFile,
        ], base_path());

        $process->setTimeout(null);
        $process->setIdleTimeout(null);
        $process->run(function (string $type, string $buffer): void {
            $this->output->write($buffer);
        });

        if (! $process->isSuccessful()) {
            $message = trim($process->getErrorOutput() ?: $process->getOutput() ?: 'Scraper failed');
            throw new \RuntimeException($message);
        }

        $this->info("Pagina {$page}: importando a MySQL...");

        return $importer->import($outputFile);
    }

    protected function pageOutputFile(int $page): string
    {
        return storage_path('app/yupoo/pages/page-'.$page.'.json');
    }
}
