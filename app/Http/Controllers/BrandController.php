<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;

class BrandController extends Controller
{
    public function show(Brand $brand)
    {
        $brand->load(['motorcycles' => function ($q) {
            $q->where('is_active', true)->with(['category'])->latest();
        }]);

        return view('shop', [
            'motorcycles' => $brand->motorcycles()->paginate(12),
            'brands' => Brand::query()->where('is_active', true)->orderBy('name')->get(),
            'categories' => Category::query()->where('is_active', true)->orderBy('name')->get(),
            'filters' => ['brand_id' => $brand->id],
        ]);
    }
}
