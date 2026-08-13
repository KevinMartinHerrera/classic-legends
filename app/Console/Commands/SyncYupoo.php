<?php

namespace App\Console\Commands;

use App\Services\YupooImporter;
use Throwable;

class SyncYupoo extends AbstractYupooSyncCommand
{
    protected $signature = 'yupoo:sync {--url= : Yupoo albums URL to scrape} {--pages= : Limit pages for the scraper}';

    protected $description = 'Run Playwright scraper and import Yupoo albums into MySQL';

    public function handle(YupooImporter $importer): int
    {
        try {
            $pageLimit = is_numeric($this->option('pages')) ? max(1, (int) $this->option('pages')) : null;
            $url = is_string($this->option('url')) && trim((string) $this->option('url')) !== ''
                ? trim((string) $this->option('url'))
                : null;

            $this->line('<info>==============================================</info>');
            $this->line('<info>  Yupoo Sync</info>');
            $this->line('<info>  URL: '.($url ?? 'config default').'</info>');
            $this->line('<info>  Páginas: '.($pageLimit ?? 'auto').'</info>');
            $this->line('<info>==============================================</info>');

            $summary = [
                'records' => 0,
                'products_created' => 0,
                'products_updated' => 0,
                'categories_created' => 0,
                'images_synced' => 0,
                'errors' => [],
            ];

            if ($url !== null) {
                $result = $this->syncGallery($importer, $url, $pageLimit);
                $pageSummary = $result['summary'];

                $summary['records'] += $pageSummary['records'];
                $summary['products_created'] += $pageSummary['products_created'];
                $summary['products_updated'] += $pageSummary['products_updated'];
                $summary['categories_created'] += $pageSummary['categories_created'];
                $summary['images_synced'] += $pageSummary['images_synced'];
                $summary['errors'] = array_merge($summary['errors'], $pageSummary['errors']);
            } else {
                $pageLimit = $pageLimit ?? 9;

                for ($page = 1; $page <= $pageLimit; $page++) {
                    try {
                        $result = $this->syncPage($importer, $page);
                        $pageSummary = $result['summary'];

                        $summary['records'] += $pageSummary['records'];
                        $summary['products_created'] += $pageSummary['products_created'];
                        $summary['products_updated'] += $pageSummary['products_updated'];
                        $summary['categories_created'] += $pageSummary['categories_created'];
                        $summary['images_synced'] += $pageSummary['images_synced'];
                        $summary['errors'] = array_merge($summary['errors'], $pageSummary['errors']);
                    } catch (Throwable $exception) {
                        $summary['errors'][] = 'Pagina '.$page.': '.$exception->getMessage();
                        $this->error('Pagina '.$page.' fallo, continuo con la siguiente.');
                    }
                }
            }

            $this->line('<comment>----------------------------------------------</comment>');
            $this->line('<comment>  Productos: '.$summary['products_created'].' nuevos, '.$summary['products_updated'].' actualizados</comment>');
            $this->line('<comment>  Imágenes: '.$summary['images_synced'].'</comment>');
            $this->line('<comment>  Categorías: '.$summary['categories_created'].'</comment>');
            $this->line('<comment>  Errores: '.count($summary['errors']).'</comment>');
            $this->line('<comment>----------------------------------------------</comment>');

            $this->info('Sincronizacion completada.');

            return self::SUCCESS;
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }
    }
}
