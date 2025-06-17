<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use Illuminate\Support\Arr;

class ChairsTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        $chairImages = [
            'chaise-bureau-ergonomique.jpg',
            'chaise-design-moderne.jpg',
            'chaise-gamer-professionnelle.jpg',
            'chaise-en-cuir-luxe.jpg',
            'chaise-en-bois-massif.jpg',
            'chaise-pliable.jpg',
            'chaise-de-conference.jpg',
            'chaise-de-bar.jpg',
            'chaise-scandinave.jpg',
            'chaise-accent.jpg'
        ];

        $chairModels = [
            'ErgoComfort 3000' => 'Chaise de bureau ergonomique',
            'Executive Elite' => 'Chaise de direction en cuir',
            'ModernFlex Pro' => 'Chaise design contemporaine',
            'Classic Leather Plus' => 'Chaise en cuir véritable',
            'Minimalist Mesh' => 'Chaise de bureau en mesh',
            'TaskMaster Deluxe' => 'Chaise de travail haut de gamme',
            'HomeOffice Supreme' => 'Chaise de télétravail confortable',
            'Gaming Throne X' => 'Chaise gamer professionnelle',
            'Conference Master' => 'Chaise de salle de réunion',
            'Student Essential' => 'Chaise étudiante économique'
        ];

        $i = 0;
        foreach ($chairModels as $model => $desc) {
            DB::table('products')->insert([
                'name' => $model,
                'code' => 'CHAIR-' . (1000 + $i),
                'price' => $this->generateRealisticPrice($model),
                'description' => $this->generateDetailedDescription($model, $desc),
                'image_path' => 'chairs/' . $chairImages[$i % count($chairImages)],
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $i++;
            if ($i >= 10) break;
        }
    }

    protected function generateRealisticPrice($modelName)
    {
        $basePrices = [
            'gamer' => 299.00,
            'ergo' => 349.00,
            'cuir' => 449.00,
            'design' => 399.00,
            'bureau' => 199.00,
            'etudiant' => 89.00,
            'conference' => 179.00,
            'pliable' => 129.00
        ];

        foreach ($basePrices as $key => $price) {
            if (stripos($modelName, $key) !== false) {
                return $price + mt_rand(-20, 50);
            }
        }

        return mt_rand(80, 500) - 0.01;
    }

    protected function generateDetailedDescription($model, $type)
    {
        $materials = [
            'cuir' => 'en cuir véritable italien',
            'bois' => 'en bois massif de hêtre',
            'metal' => 'en acier inoxydable brossé',
            'mesh' => 'avec dossier en mesh respirant',
            'plastique' => 'en polypropylène renforcé',
            'bambou' => 'en bambou durable certifié FSC'
        ];

        $features = [
            'réglable en hauteur',
            'pivote à 360 degrés',
            'assise rembourrée',
            'accoudoirs réglables',
            'tête et lombaires intégrées',
            'mécanisme inclinable',
            'roulettes silencieuses',
            'base chromée 5 branches'
        ];

        $randomMaterial = $materials[array_rand($materials)];
        $randomFeatures = implode(', ', Arr::random($features, min(3, count($features))));

        return "La $model est une $type $randomMaterial. Caractéristiques: $randomFeatures. Conçue pour un confort optimal et une durabilité à toute épreuve.";
    }
}
