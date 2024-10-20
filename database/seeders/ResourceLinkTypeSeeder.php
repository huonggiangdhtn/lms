<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Resource\Models\ResourceLinkType;

class ResourceLinkTypeSeeder extends Seeder
{
    public function run()
    {
        $linkTypes = [
            ['title' => 'Public', 'code' => 'public'],
            ['title' => 'Private', 'code' => 'private'],
            ['title' => 'Internal', 'code' => 'internal']
        ];

        foreach ($linkTypes as $type) {
            ResourceLinkType::create($type);
        }
    }
}

