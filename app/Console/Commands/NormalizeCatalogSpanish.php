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
            $source = $categoria->nombre_original ?: $categoria->titulo;
            $newTitle = $this->cleanForDatabase($importer->spanishCategoryTitle($source));

            if ($newTitle !== $categoria->titulo) {
                $categoria->titulo = $newTitle;
                $categoria->nombre_es = $newTitle;
                $categoria->nombre_original = $newTitle;
                $categoria->save();
                $categoriesUpdated++;
            }
        }

        foreach (Producto::query()->with(['categoria', 'imagenes'])->get() as $producto) {
            $sourceTitle = $producto->nombre_original ?: $producto->titulo;
            if ($this->isNbaProduct($sourceTitle, $producto->categoria?->nombre_original ?: $producto->categoria?->titulo ?? '')) {
                $newTitle = $this->cleanForDatabase($this->normalizeNbaTitle($sourceTitle));
                $newDescription = $this->cleanForDatabase($this->normalizeNbaDescription($sourceTitle, $producto->descripcion ?? '', $importer));
                $newNombreEs = $this->cleanForDatabase($this->normalizeNbaTitle($sourceTitle));
            } else {
                $newTitle = $this->cleanForDatabase($importer->spanishTitle((string) $producto->yupoo_album_id, $sourceTitle));
                $newDescription = $this->cleanForDatabase($this->normalizeProductDescription($producto->descripcion ?? '', $importer));
                $newNombreEs = $this->cleanForDatabase($importer->translateProduct($producto->nombre_original ?: $producto->titulo, $producto->categoria?->nombre_original ?: $producto->categoria?->titulo ?? ''));
            }

            $dirty = false;

            if ($newTitle !== $producto->titulo) {
                $producto->titulo = $newTitle;
                $dirty = true;
            }

            if ($newNombreEs !== $producto->nombre_es) {
                $producto->nombre_es = $newNombreEs;
                $dirty = true;
            }

            if ($producto->nombre_original !== $newTitle) {
                $producto->nombre_original = $newTitle;
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
                $cleanAlt = $this->cleanForDatabase($this->normalizeProductDescription($alt, $importer));

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
        $clean = $this->cleanForDatabase($importer->cleanSpanishLabel($value));

        return $this->cleanForDatabase(
            str_ireplace(
                ['Adults And Youth', 'Adults and Youth', 'Youth', 'Adults', 'Kid Kit', 'Kids Kit', 'Retro Jersey', 'Jersey', 'Baseball', 'MLB', 'City Edition', 'Earned Edition', 'Statement Edition', 'Association Edition', 'Official Jersey', 'Replica Jersey', 'Player Edition'],
                ['adultos y niños', 'adultos y niños', 'niños', 'adultos', 'uniforme infantil', 'uniforme infantil', 'uniforme retro', 'uniforme', 'béisbol', 'MLB', 'edición ciudad', 'edición ganada', 'edición declaración', 'edición asociación', 'uniforme oficial', 'réplica de uniforme', 'versión jugador'],
                $clean
            )
        );
    }

    private function isNbaProduct(string $title, string $categoryName = ''): bool
    {
        $haystack = mb_strtolower($title.' '.$categoryName);

        return str_contains($haystack, 'nba')
            || str_contains($haystack, '魔术')
            || str_contains($haystack, '骑士')
            || str_contains($haystack, '凯尔特人')
            || str_contains($haystack, '湖人')
            || str_contains($haystack, '勇士')
            || str_contains($haystack, '尼克斯')
            || str_contains($haystack, '雄鹿')
            || str_contains($haystack, '太阳')
            || str_contains($haystack, '火箭')
            || str_contains($haystack, '独行侠')
            || str_contains($haystack, '热火')
            || str_contains($haystack, '掘金')
            || str_contains($haystack, '森林狼')
            || str_contains($haystack, '快船')
            || str_contains($haystack, '公牛')
            || str_contains($haystack, '老鹰')
            || str_contains($haystack, '猛龙')
            || str_contains($haystack, '篮网')
            || str_contains($haystack, '黄蜂')
            || str_contains($haystack, '国王')
            || str_contains($haystack, '步行者')
            || str_contains($haystack, '雷霆')
            || str_contains($haystack, '灰熊')
            || str_contains($haystack, '鹈鹕')
            || str_contains($haystack, '开拓者')
            || str_contains($haystack, '爵士')
            || str_contains($haystack, '奇才');
    }

    private function normalizeNbaTitle(string $value): string
    {
        $value = str_replace(['#', '＃'], ' ', $value);
        $value = preg_replace('/[\p{So}\p{Cn}]+/u', ' ', $value) ?? $value;

        $teamMap = [
            '魔术' => 'Magic',
            '骑士' => 'Cavaliers',
            '凯尔特人' => 'Celtics',
            '湖人' => 'Lakers',
            '勇士' => 'Warriors',
            '尼克斯' => 'Knicks',
            '雄鹿' => 'Bucks',
            '太阳' => 'Suns',
            '火箭' => 'Rockets',
            '独行侠' => 'Mavericks',
            '热火' => 'Heat',
            '掘金' => 'Nuggets',
            '森林狼' => 'Timberwolves',
            '快船' => 'Clippers',
            '公牛' => 'Bulls',
            '活塞' => 'Pistons',
            '老鹰' => 'Hawks',
            '猛龙' => 'Raptors',
            '篮网' => 'Nets',
            '黄蜂' => 'Hornets',
            '国王' => 'Kings',
            '步行者' => 'Pacers',
            '雷霆' => 'Thunder',
            '灰熊' => 'Grizzlies',
            '鹈鹕' => 'Pelicans',
            '开拓者' => 'Trail Blazers',
            '爵士' => 'Jazz',
            '奇才' => 'Wizards',
        ];

        $variant = null;
        $lower = mb_strtolower($value);
        if (str_contains($lower, 'home') || str_contains($value, '主场')) {
            $variant = 'local';
        } elseif (str_contains($lower, 'away') || str_contains($value, '客场')) {
            $variant = 'visitante';
        } elseif (str_contains($lower, 'third') || str_contains($value, '第三')) {
            $variant = 'tercera';
        }

        $city = null;
        $number = null;
        $player = null;

        foreach ($teamMap as $needle => $team) {
            if (str_contains($value, $needle)) {
                $city = $team;
                break;
            }
        }

        if (preg_match('/\b(\d{1,3})\b/', $value, $matches)) {
            $number = $matches[1];
        }

        if (preg_match('/\b([A-Z][A-Z\-]{2,}(?:\s+[A-Z][A-Z\-]{2,})*)\b/', $value, $matches)) {
            $player = trim($matches[1]);
        }

        $parts = ['Uniforme NBA'];

        if ($city !== null) {
            $parts[] = $city;
        }

        if ($player !== null) {
            $parts[] = $player;
        }

        if ($number !== null) {
            $parts[] = $number;
        }

        if ($variant !== null) {
            $parts[] = $variant;
        }

        return implode(' · ', $parts);
    }

    private function normalizeNbaDescription(string $title, string $description, YupooImporter $importer): string
    {
        $base = $this->normalizeNbaTitle($title);
        $cleanDescription = $this->cleanForDatabase($importer->cleanSpanishLabel($description));

        if ($cleanDescription === '') {
            return $base;
        }

        return $base.' · '.preg_replace('/\b(?:nba|n.b.a\.?|home|away|third|local|visitante|tercera|uniforme|camiseta|shirt|jersey|kit)\b/i', '', $cleanDescription) ?: $base;
    }

    private function cleanForDatabase(string $value): string
    {
        $value = preg_replace('/[\p{So}\p{Cn}]+/u', ' ', $value) ?? $value;
        $value = preg_replace('/[\x{4e00}-\x{9fff}]+/u', ' ', $value) ?? $value;
        $value = preg_replace('/\b(?:nba|n.b.a\.?|mlb|baseball|uniforme|camiseta|shirt|jersey|kit|home|away|third|special|player|version|kids?|youth|adults?|training|classic|retro|city|edition|earned|statement|association|official|replica|swingman|authentic|men|women|boys?|girls?)\b/i', ' ', $value) ?? $value;
        $value = preg_replace('/\s+/', ' ', $value) ?? $value;

        return trim($value, " \t\n\r\0\x0B-_|/");
    }
}
