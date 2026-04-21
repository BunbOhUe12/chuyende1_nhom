<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PromotionController extends Controller
{
    public function index(): View
    {
        $promotions = Promotion::query()
            ->orderByDesc('is_active')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.promotions.index', compact('promotions'));
    }

    public function create(): View
    {
        return view('admin.promotions.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'slug'        => ['nullable', 'string', 'max:255', 'unique:promotions,slug'],
            'description' => ['nullable', 'string'],
            'type'        => ['required', 'in:percent,fixed'],
            'value'       => ['required', 'numeric', 'min:0'],
            'code'        => ['nullable', 'string', 'max:50', 'unique:promotions,code'],
            'starts_at'   => ['nullable', 'date'],
            'ends_at'     => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active'   => ['nullable', 'boolean'],
            'banner_image' => ['nullable', 'image', 'max:4096'],
            'thumbnail'    => ['nullable', 'image', 'max:2048'],
        ]);

        $slug = $data['slug'] ? Str::slug($data['slug']) : Str::slug($data['name']);
        if (empty($slug)) {
            $slug = Str::random(8);
        }

        $banner = null;
        if ($request->hasFile('banner_image')) {
            $banner = $request->file('banner_image')->store('promotions', 'public');
        }

        $thumb = null;
        if ($request->hasFile('thumbnail')) {
            $thumb = $request->file('thumbnail')->store('promotions', 'public');
        }

        Promotion::query()->create([
            'name'         => trim($data['name']),
            'slug'         => $slug,
            'description'  => $data['description'] ?? null,
            'type'         => $data['type'],
            'value'        => $data['value'],
            'code'         => $data['code'] ? strtoupper(trim($data['code'])) : null,
            'starts_at'    => $data['starts_at'] ?? null,
            'ends_at'      => $data['ends_at'] ?? null,
            'is_active'    => (bool) ($data['is_active'] ?? false),
            'banner_image' => $banner,
            'thumbnail'    => $thumb,
        ]);

        return redirect()->route('admin.promotions.index')->with('success', 'Đã tạo chương trình ưu đãi.');
    }

    public function edit(Promotion $promotion): View
    {
        return view('admin.promotions.edit', compact('promotion'));
    }

    public function update(Request $request, Promotion $promotion): RedirectResponse
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'slug'        => ['nullable', 'string', 'max:255', 'unique:promotions,slug,' . $promotion->id],
            'description' => ['nullable', 'string'],
            'type'        => ['required', 'in:percent,fixed'],
            'value'       => ['required', 'numeric', 'min:0'],
            'code'        => ['nullable', 'string', 'max:50', 'unique:promotions,code,' . $promotion->id],
            'starts_at'   => ['nullable', 'date'],
            'ends_at'     => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active'   => ['nullable', 'boolean'],
            'banner_image' => ['nullable', 'image', 'max:4096'],
            'thumbnail'    => ['nullable', 'image', 'max:2048'],
        ]);

        $slug = $data['slug'] ? Str::slug($data['slug']) : Str::slug($data['name']);
        if (empty($slug)) {
            $slug = $promotion->slug ?: Str::random(8);
        }

        $banner = $promotion->banner_image;
        if ($request->hasFile('banner_image')) {
            if ($banner) {
                Storage::disk('public')->delete($banner);
            }
            $banner = $request->file('banner_image')->store('promotions', 'public');
        }

        $thumb = $promotion->thumbnail;
        if ($request->hasFile('thumbnail')) {
            if ($thumb) {
                Storage::disk('public')->delete($thumb);
            }
            $thumb = $request->file('thumbnail')->store('promotions', 'public');
        }

        $promotion->update([
            'name'         => trim($data['name']),
            'slug'         => $slug,
            'description'  => $data['description'] ?? null,
            'type'         => $data['type'],
            'value'        => $data['value'],
            'code'         => $data['code'] ? strtoupper(trim($data['code'])) : null,
            'starts_at'    => $data['starts_at'] ?? null,
            'ends_at'      => $data['ends_at'] ?? null,
            'is_active'    => (bool) ($data['is_active'] ?? false),
            'banner_image' => $banner,
            'thumbnail'    => $thumb,
        ]);

        return redirect()->route('admin.promotions.index')->with('success', 'Đã cập nhật chương trình ưu đãi.');
    }

    public function destroy(Promotion $promotion): RedirectResponse
    {
        if ($promotion->banner_image) {
            Storage::disk('public')->delete($promotion->banner_image);
        }
        if ($promotion->thumbnail) {
            Storage::disk('public')->delete($promotion->thumbnail);
        }
        $promotion->delete();

        return redirect()->route('admin.promotions.index')->with('success', 'Đã xóa chương trình ưu đãi.');
    }
}
