<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $services = [
            'Facials',
            'Chemical Peels',
            'Microdermabrasion',
            'Waxing',
            'Eyebrow Shaping and Tinting',
            'Eyelash Services (Extensions, Tinting)',
            'Body Treatments (Scrubs, Wraps)',
            'Acne Treatments',
            'Anti-Aging Treatments',
            'Makeup Application',
            'Nutritional Counseling (related to skin health)',
            'Dermal Fillers',
            'Botox',
            'Skin Rejuvenation (Laser, IPL, Radiofrequency)',
            'Hair Removal (Laser Hair Removal)',
        ];

        foreach ($services as $service) {
            Category::create([
                'name' => $service,
                'image_url' => '',
            ]);
        }
    }
}
