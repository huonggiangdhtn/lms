<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Resource\Models\ResourceType;

class ResourceTypeSeeder extends Seeder
{
    public function run()
    {
        $resourceTypes = [
            ['title' => 'Image', 'code' => 'image'],
            ['title' => 'Document', 'code' => 'document'],
            ['title' => 'Video', 'code' => 'video'],
            ['title' => 'Audio', 'code' => 'audio']
        ];

        foreach ($resourceTypes as $type) {
            ResourceType::create($type);
        }
    }
}

