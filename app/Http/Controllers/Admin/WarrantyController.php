<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Motorcycle;
use App\Models\Warranty;
use Illuminate\Http\Request;

class WarrantyController extends Controller
{
    public function index(Request $request)
    {
        $query = Warranty::query()->with(['motorcycle', 'creator'])->latest();

        if ($s = $request->input('search')) {
            $query->where(function ($q) use ($s) {
                $q->where('customer_name', 'like', "%{$s}%")
                  ->orWhere('customer_phone', 'like', "%{$s}%")
                  ->orWhere('frame_number', 'like', "%{$s}%")
                  ->orWhere('engine_number', 'like', "%{$s}%")
                  ->orWhere('motorcycle_name', 'like', "%{$s}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $warranties = $query->paginate(15)->withQueryString();

        return view('admin.warranties.index', compact('warranties'));
    }

    public function create()
    {
        $motorcycles = Motorcycle::query()->where('is_active', true)->orderBy('name')->get();
        return view('admin.warranties.create', compact('motorcycles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name'  => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'motorcycle_id'  => ['nullable', 'exists:motorcycles,id'],
            'motorcycle_name'=> ['nullable', 'string', 'max:255'],
            'frame_number'   => ['nullable', 'string', 'max:255'],
            'engine_number'  => ['nullable', 'string', 'max:255'],
            'purchase_date'  => ['required', 'date'],
            'warranty_start' => ['required', 'date'],
            'warranty_end'   => ['required', 'date', 'after:warranty_start'],
            'warranty_type'  => ['required', 'in:chinh_hang,mo_rong,cuahang'],
            'notes'          => ['nullable', 'string'],
            'status'         => ['required', 'in:active,expired,claimed,cancelled'],
        ]);

        if ($data['motorcycle_id'] ?? null) {
            $mc = Motorcycle::find($data['motorcycle_id']);
            $data['motorcycle_name'] = $data['motorcycle_name'] ?: $mc?->name;
            $data['frame_number']  = $data['frame_number']  ?: $mc?->frame_number;
            $data['engine_number'] = $data['engine_number'] ?: $mc?->engine_number;
        }

        $data['created_by'] = auth()->id();

        Warranty::create($data);

        return redirect()->route('admin.warranties.index')->with('success', 'Đã tạo phiếu bảo hành.');
    }

    public function show(Warranty $warranty)
    {
        $warranty->load(['motorcycle', 'creator']);
        return view('admin.warranties.show', compact('warranty'));
    }

    public function print(Warranty $warranty)
    {
        $warranty->load(['motorcycle', 'creator']);

        return view('admin.warranties.print', compact('warranty'));
    }

    public function edit(Warranty $warranty)
    {
        $motorcycles = Motorcycle::query()->where('is_active', true)->orderBy('name')->get();
        return view('admin.warranties.edit', compact('warranty', 'motorcycles'));
    }

    public function update(Request $request, Warranty $warranty)
    {
        $data = $request->validate([
            'customer_name'  => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'motorcycle_id'  => ['nullable', 'exists:motorcycles,id'],
            'motorcycle_name'=> ['nullable', 'string', 'max:255'],
            'frame_number'   => ['nullable', 'string', 'max:255'],
            'engine_number'  => ['nullable', 'string', 'max:255'],
            'purchase_date'  => ['required', 'date'],
            'warranty_start' => ['required', 'date'],
            'warranty_end'   => ['required', 'date', 'after:warranty_start'],
            'warranty_type'  => ['required', 'in:chinh_hang,mo_rong,cuahang'],
            'notes'          => ['nullable', 'string'],
            'status'         => ['required', 'in:active,expired,claimed,cancelled'],
        ]);

        $warranty->update($data);

        return redirect()->route('admin.warranties.index')->with('success', 'Đã cập nhật bảo hành.');
    }

    public function destroy(Warranty $warranty)
    {
        $warranty->delete();
        return redirect()->route('admin.warranties.index')->with('success', 'Đã xoá phiếu bảo hành.');
    }
}
