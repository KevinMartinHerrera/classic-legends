<?php

namespace App\Console\Commands;

use App\Services\YupooImporter;
use Throwable;

abstract class SyncYupooPage extends AbstractYupooSyncCommand
{
    abstract protected function pageNumber(): int;

    public function handle(YupooImporter $importer): int
    {
        try {
            $page = $this->pageNumber();
            $this->line('<info>==============================================</info>');
            $this->line('<info>  Yupoo Page '.$page.'</info>');
            $this->line('<info>==============================================</info>');

            $result = $this->syncPage($importer, $page);
            $summary = $result['summary'];

            $this->line('<comment>----------------------------------------------</comment>');
            $this->line('<comment>  Productos: '.$summary['products_created'].' nuevos, '.$summary['products_updated'].' actualizados</comment>');
            $this->line('<comment>  Imágenes: '.$summary['images_synced'].'</comment>');
            $this->line('<comment>  Categorías: '.$summary['categories_created'].'</comment>');
            $this->line('<comment>  Errores: '.count($summary['errors']).'</comment>');
            $this->line('<comment>----------------------------------------------</comment>');

            $this->info('Pagina '.$page.' completada.');

            return self::SUCCESS;
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }
    }
}
