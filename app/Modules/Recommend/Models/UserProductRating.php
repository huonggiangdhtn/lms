<?php

namespace App\Models;
namespace App\Modules\Recommend\Models;
use Illuminate\Database\Eloquent\Model;

class UserProductRating extends Model
{
    //
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
