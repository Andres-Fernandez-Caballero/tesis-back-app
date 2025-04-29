<?php

namespace Database\Seeders\Therapists;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Tags\Tag;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            'es' =>
            [
                'Deportivo',
                'Terapéutico',
                'Antiestrés',
                'Descontracturante',
                'Reflexología',
                'Cranio-sacral',
                'Shiatsu',
                'Ayurvédico',
                'Tailandés',
                'Bambuterapia',
                'Masaje en seco',
            ],
            'en' =>
            [
                'Therapeutic',
                'Anti-stress',
                'Decontracting',
                'Reflexology',
                'Cranio-sacral',
                'Shiatsu',
                'Ayurvedic',
                'Thai',
                'Bamboo therapy',
                'Dry massage',
            ],
        ];

        
        foreach( $tags as $lang => $tagList){
            foreach( $tagList as $tag ){
                Tag::findOrCreate($tag, $lang, "massagist");
        }
    }
}
}
