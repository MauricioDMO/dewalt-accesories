<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Accessory;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AccessoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Leer el archivo JSON
        $jsonPath = resource_path('accesories.json');
        $jsonData = json_decode(file_get_contents($jsonPath), true);

        $categories = [];
        $subcategories = [];

        // Primera pasada: recolectar categorías y subcategorías únicas
        foreach ($jsonData as $item) {
            if (!empty($item['category'])) {
                $categories[$item['category']] = true;
                
                if (!empty($item['subcategory'])) {
                    if (!isset($subcategories[$item['category']])) {
                        $subcategories[$item['category']] = [];
                    }
                    $subcategories[$item['category']][$item['subcategory']] = true;
                }
            }
        }

        // Crear categorías
        $categoryModels = [];
        foreach (array_keys($categories) as $categoryName) {
            $category = Category::firstOrCreate(
                ['name' => $categoryName],
                ['slug' => Str::slug($categoryName)]
            );
            $categoryModels[$categoryName] = $category;
        }

        // Crear subcategorías
        $subcategoryModels = [];
        foreach ($subcategories as $categoryName => $subs) {
            foreach (array_keys($subs) as $subName) {
                $subcategory = Subcategory::firstOrCreate(
                    [
                        'name' => $subName,
                        'category_id' => $categoryModels[$categoryName]->id
                    ],
                    ['slug' => Str::slug($subName)]
                );
                $subcategoryModels[$categoryName][$subName] = $subcategory;
            }
        }

        // Crear accesorios
        foreach ($jsonData as $item) {
            // Saltar si no tiene código
            if (empty($item['code'])) {
                continue;
            }

            $categoryId = null;
            $subcategoryId = null;

            if (!empty($item['category']) && isset($categoryModels[$item['category']])) {
                $categoryId = $categoryModels[$item['category']]->id;
                
                if (!empty($item['subcategory']) && isset($subcategoryModels[$item['category']][$item['subcategory']])) {
                    $subcategoryId = $subcategoryModels[$item['category']][$item['subcategory']]->id;
                }
            }

            Accessory::updateOrCreate(
                ['code' => $item['code']],
                [
                    'name' => $item['name'] ?? '',
                    'description' => $item['description'] ?? '',
                    'price' => $item['price'] ?? 0,
                    'offer' => $item['offer'] ?? null, // Campo opcional
                    'image' => $item['image'] ?? null,
                    'alt' => $item['alt'] ?? null,
                    'type' => $item['type'] ?? null,
                    'units' => $item['units'] ?? false,
                    'category_id' => $categoryId,
                    'subcategory_id' => $subcategoryId,
                ]
            );
        }

        // Crear usuario admin
        Admin::updateOrCreate(
            ['username' => 'admin'],
            [
                'password' => Hash::make('admin'),
                'name' => 'Administrador'
            ]
        );

        $this->command->info('Datos migrados exitosamente!');
        $this->command->info('Total categorías: ' . count($categoryModels));
        $this->command->info('Total accesorios: ' . Accessory::count());
        $this->command->info('Usuario admin creado: admin/admin');
    }
}
