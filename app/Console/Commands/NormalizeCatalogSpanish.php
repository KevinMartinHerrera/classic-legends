<?php

namespace App\Console\Commands;

use App\Models\Categoria;
use App\Models\Producto;
use App\Services\YupooImporter;
use Illuminate\Console\Command;

class NormalizeCatalogSpanish extends Command
{
    protected $signature = 'catalog:normalize-spanish';

    protected $description = 'Normalize catalog titles, categories and image labels into Spanish';

    public function handle(YupooImporter $importer): int
    {
        $categoriesUpdated = 0;
        $productsUpdated = 0;
        $imagesUpdated = 0;

        foreach (Categoria::query()->get() as $categoria) {
            $newTitle = $this->ensureNoEmojis($importer->spanishCategoryTitle($categoria->titulo));

            if ($newTitle !== $categoria->titulo) {
                $categoria->titulo = $newTitle;
                $categoria->nombre_es = $newTitle;
                $categoria->nombre_original = $categoria->nombre_original ?: $categoria->titulo;
                $categoria->save();
                $categoriesUpdated++;
            }
        }

        foreach (Producto::query()->with(['categoria', 'imagenes'])->get() as $producto) {
            $newTitle = $this->ensureNoEmojis($importer->spanishTitle((string) $producto->yupoo_album_id, $producto->nombre_original ?: $producto->titulo));
            $newDescription = $this->normalizeProductDescription($producto->descripcion ?? '', $importer);
            $newNombreEs = $importer->translateProduct($producto->nombre_original ?: $producto->titulo, $producto->categoria?->titulo ?? '');

            $dirty = false;

            if ($newTitle !== $producto->titulo) {
                $producto->titulo = $newTitle;
                $dirty = true;
            }

            if ($newNombreEs !== $producto->nombre_es) {
                $producto->nombre_es = $newNombreEs;
                $dirty = true;
            }

            if ($newDescription !== ($producto->descripcion ?? '')) {
                $producto->descripcion = $newDescription !== '' ? $newDescription : null;
                $dirty = true;
            }

            if ($dirty) {
                $producto->save();
                $productsUpdated++;
            }

            foreach ($producto->imagenes as $imagen) {
                $alt = trim((string) ($imagen->alt ?? ''));
                $cleanAlt = $this->normalizeProductDescription($alt, $importer);

                if ($cleanAlt !== $alt) {
                    $imagen->alt = $cleanAlt;
                    $imagen->save();
                    $imagesUpdated++;
                }
            }
        }

        $this->info('Categorias actualizadas: '.$categoriesUpdated);
        $this->info('Productos actualizados: '.$productsUpdated);
        $this->info('Imagenes actualizadas: '.$imagesUpdated);

        return self::SUCCESS;
    }

    private function normalizeProductDescription(string $value, YupooImporter $importer): string
    {
        $clean = $importer->cleanSpanishLabel($value);

        return $this->ensureNoEmojis(
            str_ireplace(
                ['Adults And Youth', 'Adults and Youth', 'Youth', 'Adults', 'Kid Kit', 'Kids Kit', 'Retro Jersey', 'Jersey'],
                ['adultos y niños', 'adultos y niños', 'niños', 'adultos', 'uniforme infantil', 'uniforme infantil', 'uniforme retro', 'uniforme'],
                $clean
            )
        );
    }

    private function ensureNoEmojis(string $value): string
    {
        $value = preg_replace('/[\p{So}\p{Cn}]+/u', ' ', $value) ?? $value;
        $value = preg_replace('/\s+/', ' ', $value) ?? $value;

        return trim($value, " \t\n\r\0\x0B-_|/");
    }
}
