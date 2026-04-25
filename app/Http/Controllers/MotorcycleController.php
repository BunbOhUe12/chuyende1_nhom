<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Motorcycle;
use Illuminate\Http\Request;

class MotorcycleController extends Controller
{
    public function index(Request $request)
    {
        $partsCategory = Category::query()
            ->where('slug', 'phu-tung')
            ->orWhere('slug', 'phu-tung-xe-may')
            ->orWhere('name', 'Phu tung')
            ->orWhere('name', 'Phụ tùng')
            ->first();

        $type = (string) $request->query('type', 'xe');
        if (! in_array($type, ['xe', 'phu-tung'], true)) {
            $type = 'xe';
        }

        $query = Motorcycle::query()
            ->where('is_active', true)
            ->with(['brand', 'category', 'promotion', 'approvedCoverImage']);

        if ($search = trim((string) $request->query('q'))) {
            $searchNoSpace = str_replace(' ', '', mb_strtolower($search));
            $query->where(function ($q) use ($search, $searchNoSpace) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('brand', fn ($b) => $b->where('name', 'like', "%{$search}%"))
                    ->orWhereRaw('REPLACE(LOWER(name), " ", "") like ?', ["%{$searchNoSpace}%"]);
            });
        }

        if ($brandId = $request->query('brand_id')) {
            $query->where('brand_id', $brandId);
        }

        if ($categoryId = $request->query('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($minPrice = $request->query('min_price')) {
            $query->where('price', '>=', $minPrice);
        }

        if ($maxPrice = $request->query('max_price')) {
            $query->where('price', '<=', $maxPrice);
        }

        if ($partsCategory) {
            if ($type === 'phu-tung') {
                $query->where('category_id', $partsCategory->id);
            } else {
                $query->where('category_id', '!=', $partsCategory->id);
            }
        }

        $motorcycles = $query->orderByDesc('created_at')->paginate(12)->withQueryString();

        $partsCount = $partsCategory
            ? Motorcycle::query()->where('is_active', true)->where('category_id', $partsCategory->id)->count()
            : 0;
        $vehicleCount = Motorcycle::query()->where('is_active', true)
            ->when($partsCategory, fn ($q) => $q->where('category_id', '!=', $partsCategory->id))
            ->count();

        return view('shop', [
            'motorcycles' => $motorcycles,
            'brands' => Brand::query()->where('is_active', true)->orderBy('name')->get(),
            'categories' => Category::query()->where('is_active', true)->orderBy('name')->get(),
            'filters' => $request->only(['q', 'brand_id', 'category_id', 'min_price', 'max_price', 'type']),
            'productType' => $type,
            'vehicleCount' => $vehicleCount,
            'partsCount' => $partsCount,
        ]);
    }

    public function show(Motorcycle $motorcycle)
    {
        $motorcycle->load(['brand', 'category', 'approvedImages' => function ($q) {
            $q->latest();
        }, 'reviews' => function ($q) {
            $q->where('is_approved', true)->latest();
        }]);

        return view('product-detail', [
            'motorcycle' => $motorcycle,
        ]);
    }
}
