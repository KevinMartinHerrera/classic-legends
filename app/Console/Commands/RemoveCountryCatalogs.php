<?php

namespace App\Console\Commands;

use App\Models\Categoria;
use Illuminate\Console\Command;

class RemoveCountryCatalogs extends Command
{
    protected $signature = 'catalog:remove-country-categories';

    protected $description = 'Remove country categories and their products';

    public function handle(): int
    {
        $names = [
            'Algeria', 'Argentina', 'Austria', 'Belgium', 'Bosnia and Herzegovina', 'Brazil',
            'Canada', 'Cape Verde', 'Colombia', 'Curacao', 'Egypt', 'England', 'France',
            'Germany', 'Iraq', 'Ivory Coast', 'Japan', 'Jordan', 'Malaysia', 'Mexico',
            'Morocco', 'Netherlands', 'Nigeria', 'Poland', 'Portugal', 'Qatar', 'Scotland',
            'Senegal', 'Slovenia', 'South Korea', 'Spain', 'Sweden', 'Switzerland', 'Tunisia', 'USA',
        ];

        $categories = Categoria::query()->whereIn('titulo', $names)->get();
        $deletedCategories = 0;
        $deletedProducts = 0;

        foreach ($categories as $categoria) {
            foreach ($categoria->productos as $producto) {
                $deletedProducts += 1;
                $producto->imagenes()->delete();
                $producto->delete();
            }

            $categoria->delete();
            $deletedCategories += 1;
        }

        $this->info('Categorias eliminadas: '.$deletedCategories);
        $this->info('Productos eliminados: '.$deletedProducts);

        return self::SUCCESS;
    }
}
