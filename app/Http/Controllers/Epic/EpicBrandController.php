<?php

namespace App\Http\Controllers\Epic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Epic\EpicBrands;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;
use Illuminate\Pagination\LengthAwarePaginator;

class EpicBrandController extends Controller
{
    public function getBrandDetail(Request $request, $brandName){
        if ($brandName == '') {
            return response()->json(['message' => 'No categories found for this brand'], 404);
        }
        $brand = EpicBrands::where('slug',$brandName)->first();
        if (!$brand) {
            return response()->json(['message' => 'Brand not found'], 404);
        }
        return response()->json([
            'message' => 'Brand details retrieved successfully',
            'data' => $brand
        ], 200);
    }
}