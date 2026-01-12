<?php

namespace Database\Seeders\Therapists;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Tags\Tag;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            'dicipline' => [
                [
                    'es' => 'Deportivo',
                    'en' => 'Sport',
                    'image' => 'tags/diciplines/deportivo.jpg',
                ],
                [
                    'es' => 'Terapéutico',
                    'en' => 'Therapeutic',
                    'image' => 'tags/diciplines/terapeutico.jpg',
                ],
                [
                    'es' => 'Antiestrés',
                    'en' => 'Anti-stress',
                    'image' => 'tags/diciplines/antiestres.jpg',
                ],
                [
                    'es' => 'Descontracturante',
                    'en' => 'Decontracting',
                    'image' => 'tags/diciplines/descontracturante.jpg',
                ],
                [
                    'es' => 'Reflexología',
                    'en' => 'Reflexology',
                    'image' => 'tags/diciplines/reflexologia.jpg',
                ],
            ],
        ];


        foreach ($tags as $type => $items) {
            foreach ($items as $tag) {
                Tag::create([
                    'type' => $type,
                    'name' => [
                        'es' => $tag['es'],
                        'en' => $tag['en'],
                    ],
                    'slug' => [
                        'es' => Str::slug($tag['es']),
                        'en' => Str::slug($tag['en']),
                    ],
                    'image' => $tag['image'] ?? null,
                ]);
            }
        }
    }
}
