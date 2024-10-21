<?php

namespace App\Modules\Recommend\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Modules\Recommend\Models\Product;
use App\Modules\Recommend\Models\UserProductRating;


class RecommendController extends Controller
{
    public function index($userId)
    {
        // return 'Hello user ' . $userId;
        $userRatings = UserProductRating::where('user_id', $userId)->pluck('product_id')->toArray();

        $recommendedProductIds = UserProductRating::whereNotIn('product_id', $userRatings)
            ->select('product_id')
            ->groupBy('product_id')
            ->orderByRaw('COUNT(*) DESC')
            ->take(5)
            ->pluck('product_id');
        print('Sản phẩm gợi ý (Recommand) của user '. $userId . ' là: ');
        return Product::whereIn('id', $recommendedProductIds)->get();

    }
//     public function show($id)
// {
//     $product = Product::findOrFail($id); // Hoặc cách khác để lấy dữ liệu
//     return view('Recommend::show', compact('product'));
// }
}