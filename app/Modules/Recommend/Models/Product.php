<?php

namespace App\Models;
namespace App\Modules\Recommend\Models;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //
    public function ratings()
    {
        return $this->hasMany(UserProductRating::class);
    }
}
