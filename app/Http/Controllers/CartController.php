<?php

namespace App\Http\Controllers;

use App\Models\Motorcycle;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = $request->session()->get('cart', []);

        $raw = collect($cart['items'] ?? []);
        $motorcycleIds = $raw->pluck('motorcycle_id')->unique()->filter()->values();
        $motorcycles = Motorcycle::query()
            ->whereIn('id', $motorcycleIds)
            ->with('approvedCoverImage')
            ->get()
            ->keyBy('id');

        $items = $raw->map(function (array $item) use ($motorcycles) {
            $item['color_options'] = $this->extractColorOptions($item['color'] ?? null);
            $mid = (int) ($item['motorcycle_id'] ?? 0);
            $item['motorcycle'] = $motorcycles->get($mid);

            return $item;
        })->values();

        $summary = [
            'quantity' => (int) ($cart['quantity'] ?? $items->sum('quantity')),
            'subtotal' => (float) ($cart['subtotal'] ?? $items->sum('line_total')),
        ];

        return view('cart', [
            'items' => $items,
            'summary' => $summary,
        ]);
    }

    public function add(Request $request, Motorcycle $motorcycle)
    {
        $data = $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1', 'max:99'],
            'selected_color' => ['nullable', 'string', 'max:100'],
        ]);

        $quantityToAdd = (int) ($data['quantity'] ?? 1);

        if (! $motorcycle->is_active || $motorcycle->stock <= 0) {
            return back()->with('error', 'Sản phẩm hiện không khả dụng.');
        }

        $cart = $request->session()->get('cart', ['items' => []]);
        $items = $cart['items'] ?? [];

        $key = (string) $motorcycle->id;
        $existingQty = (int) ($items[$key]['quantity'] ?? 0);
        $newQty = min($existingQty + $quantityToAdd, (int) $motorcycle->stock);

        $unitPrice = (float) $motorcycle->price;
        $availableColors = $this->extractColorOptions($motorcycle->color);
        $defaultColor = $availableColors[0] ?? $motorcycle->color;
        $selectedColor = $data['selected_color'] ?? ($items[$key]['selected_color'] ?? $defaultColor);
        if (! empty($availableColors) && $selectedColor && ! in_array($selectedColor, $availableColors, true)) {
            $selectedColor = $defaultColor;
        }
        $items[$key] = [
            'motorcycle_id' => $motorcycle->id,
            'name' => $motorcycle->name,
            'slug' => $motorcycle->slug,
            'price' => $unitPrice,
            'color' => $motorcycle->color,
            'selected_color' => $selectedColor,
            'quantity' => $newQty,
            'line_total' => round($unitPrice * $newQty, 2),
        ];

        $cart['items'] = $items;
        $cart['quantity'] = collect($items)->sum('quantity');
        $cart['subtotal'] = round(collect($items)->sum('line_total'), 2);

        $request->session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Đã thêm vào giỏ hàng.');
    }

    public function update(Request $request, Motorcycle $motorcycle)
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:99'],
            'selected_color' => ['nullable', 'string', 'max:100'],
        ]);

        $quantity = (int) $data['quantity'];

        $cart = $request->session()->get('cart', ['items' => []]);
        $items = $cart['items'] ?? [];
        $key = (string) $motorcycle->id;

        if (! array_key_exists($key, $items)) {
            return back();
        }

        if ($quantity <= 0) {
            unset($items[$key]);
        } else {
            $quantity = min($quantity, (int) $motorcycle->stock);
            $unitPrice = (float) $motorcycle->price;
            $selectedColor = $data['selected_color'] ?? ($items[$key]['selected_color'] ?? null);
            $availableColors = $this->extractColorOptions($motorcycle->color);
            if (! empty($availableColors) && $selectedColor && ! in_array($selectedColor, $availableColors, true)) {
                $selectedColor = $availableColors[0];
            }
            $items[$key]['quantity'] = $quantity;
            $items[$key]['price'] = $unitPrice;
            $items[$key]['color'] = $motorcycle->color;
            $items[$key]['selected_color'] = $selectedColor;
            $items[$key]['line_total'] = round($unitPrice * $quantity, 2);
        }

        $cart['items'] = $items;
        $cart['quantity'] = collect($items)->sum('quantity');
        $cart['subtotal'] = round(collect($items)->sum('line_total'), 2);

        $request->session()->put('cart', $cart);

        return redirect()->route('cart.index');
    }

    public function remove(Request $request, Motorcycle $motorcycle)
    {
        $cart = $request->session()->get('cart', ['items' => []]);
        $items = $cart['items'] ?? [];
        unset($items[(string) $motorcycle->id]);

        $cart['items'] = $items;
        $cart['quantity'] = collect($items)->sum('quantity');
        $cart['subtotal'] = round(collect($items)->sum('line_total'), 2);

        $request->session()->put('cart', $cart);

        return redirect()->route('cart.index');
    }

    public function clear(Request $request)
    {
        $request->session()->forget('cart');

        return redirect()->route('cart.index');
    }

    private function extractColorOptions(?string $color): array
    {
        if (! $color) {
            return [];
        }

        $normalized = str_replace(['|', ';', '/'], ',', $color);

        return collect(explode(',', $normalized))
            ->map(fn ($c) => trim($c))
            ->filter(fn ($c) => $c !== '')
            ->unique()
            ->values()
            ->all();
    }
}
