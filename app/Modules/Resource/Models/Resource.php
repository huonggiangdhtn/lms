<?php

namespace App\Modules\Resource\Models;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    protected $fillable = [
        'title', 'slug', 'file_name', 'file_type','youtube_url','document_url','image_url' ,'file_size','description', 'path', 'url', 'resource_type_id', 'resource_link_type_id','tags'
    ];

    public function resourceType()
    {
        return $this->belongsTo(ResourceType::class);
    }

    public function resourceLinkType()
    {
        return $this->belongsTo(ResourceLinkType::class);
    }
}
