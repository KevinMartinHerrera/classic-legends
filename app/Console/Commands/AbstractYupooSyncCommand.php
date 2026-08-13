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

        $process = new Process($this->scraperCommand([
            '--pages=1',
            '--start-page='.$page,
        ], $outputFile), base_path());

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

    protected function syncGallery(YupooImporter $importer, string $url, ?int $pages = null): array
    {
        $pageCount = $pages ?? 1;
        $startPage = $this->pageNumberFromUrl($url) ?? 1;
        $summary = [
            'records' => 0,
            'products_created' => 0,
            'products_updated' => 0,
            'categories_created' => 0,
            'images_synced' => 0,
            'errors' => [],
        ];

        for ($offset = 0; $offset < $pageCount; $offset++) {
            $page = $startPage + $offset;
            $pageUrl = $this->pageUrl($url, $page);
            $outputFile = $this->pageOutputFile($page);
            File::ensureDirectoryExists(dirname($outputFile));

            $this->info("Pagina {$page}: ejecutando Playwright...");

            $process = new Process($this->scraperCommand([
                '--url='.$pageUrl,
                '--pages=1',
                '--start-page='.$page,
            ], $outputFile), base_path());

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

            $result = $importer->import($outputFile);
            $pageSummary = $result['summary'];

            $summary['records'] += $pageSummary['records'];
            $summary['products_created'] += $pageSummary['products_created'];
            $summary['products_updated'] += $pageSummary['products_updated'];
            $summary['categories_created'] += $pageSummary['categories_created'];
            $summary['images_synced'] += $pageSummary['images_synced'];
            $summary['errors'] = array_merge($summary['errors'], $pageSummary['errors']);
        }

        return ['summary' => $summary];
    }

    private function pageUrl(string $url, int $page): string
    {
        $parts = parse_url($url);

        if ($parts === false) {
            return $url;
        }

        $query = [];

        if (! empty($parts['query'])) {
            parse_str($parts['query'], $query);
        }

        $query['page'] = $page;

        $scheme = $parts['scheme'] ?? 'https';
        $host = $parts['host'] ?? '';
        $path = $parts['path'] ?? '';
        $port = isset($parts['port']) ? ':'.$parts['port'] : '';
        $user = $parts['user'] ?? '';
        $pass = isset($parts['pass']) ? ':'.$parts['pass'] : '';
        $auth = $user !== '' ? $user.$pass.'@' : '';
        $fragment = isset($parts['fragment']) ? '#'.$parts['fragment'] : '';
        $queryString = http_build_query($query);

        return $scheme.'://'.$auth.$host.$port.$path.($queryString !== '' ? '?'.$queryString : '').$fragment;
    }

    private function pageNumberFromUrl(string $url): ?int
    {
        $query = parse_url($url, PHP_URL_QUERY);

        if (! is_string($query) || $query === '') {
            return null;
        }

        parse_str($query, $params);

        if (! isset($params['page']) || ! is_numeric($params['page'])) {
            return null;
        }

        return max(1, (int) $params['page']);
    }

    /**
     * @param array<int, string> $extraArguments
     * @return array<int, string>
     */
    private function scraperCommand(array $extraArguments, string $outputFile): array
    {
        return array_merge([
            'node',
            'scraper/scrape.js',
            ...$extraArguments,
            '--output-file='.$outputFile,
        ]);
    }

    protected function pageOutputFile(int $page): string
    {
        return storage_path('app/yupoo/pages/page-'.$page.'.json');
    }
}
