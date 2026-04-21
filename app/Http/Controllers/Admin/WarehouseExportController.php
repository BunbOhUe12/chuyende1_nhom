<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Motorcycle;
use App\Models\WarehouseExport;
use Illuminate\Http\Request;

class WarehouseExportController extends Controller
{
    public function index(Request $request)
    {
        // Kho mặc định xem tab "Chờ xử lý" để nhanh thấy việc cần làm
        $defaultStatus = (auth()->user()->isKho() && ! auth()->user()->isAdmin()) ? 'pending' : '';
        $status = $request->query('status', $defaultStatus);

        $query = WarehouseExport::with(['motorcycle', 'requester', 'handler'])->latest();

        if ($status !== '') {
            $query->where('status', $status);
        }

        $exports = $query->paginate(20)->withQueryString();
        $counts  = WarehouseExport::selectRaw('status, count(*) as c')->groupBy('status')->pluck('c', 'status')->all();

        return view('admin.warehouse-exports.index', compact('exports', 'counts', 'status'));
    }

    public function create()
    {
        abort_unless(auth()->user()->isAdmin(), 403, 'Chỉ quản trị viên mới có thể tạo phiếu xuất kho.');

        $motorcycles = Motorcycle::where('is_active', true)->orderBy('name')->get();
        return view('admin.warehouse-exports.create', compact('motorcycles'));
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->isAdmin(), 403, 'Chỉ quản trị viên mới có thể tạo phiếu xuất kho.');
        $data = $request->validate([
            'motorcycle_id' => ['required', 'exists:motorcycles,id'],
            'quantity'      => ['required', 'integer', 'min:1'],
            'reason'        => ['required', 'string', 'max:500'],
            'notes'         => ['nullable', 'string', 'max:1000'],
        ], [
            'motorcycle_id.required' => 'Vui lòng chọn sản phẩm.',
            'quantity.required'      => 'Vui lòng nhập số lượng.',
            'quantity.min'           => 'Số lượng phải ít nhất là 1.',
            'reason.required'        => 'Vui lòng nhập lý do xuất kho.',
        ]);

        $data['requested_by'] = auth()->id();
        $data['status']       = 'pending';

        WarehouseExport::create($data);

        return redirect()->route('admin.warehouse-exports.index')
            ->with('success', 'Đã tạo phiếu xuất kho. Bộ phận kho sẽ xử lý sớm.');
    }

    public function show(WarehouseExport $warehouseExport)
    {
        $warehouseExport->load(['motorcycle', 'requester', 'handler']);
        return view('admin.warehouse-exports.show', ['export' => $warehouseExport]);
    }

    /**
     * Bộ phận kho cập nhật trạng thái phiếu xuất kho.
     */
    public function updateStatus(Request $request, WarehouseExport $warehouseExport)
    {
        $data = $request->validate([
            'status'          => ['required', 'in:confirmed,completed,rejected'],
            'warehouse_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $update = [
            'status'          => $data['status'],
            'warehouse_notes' => $data['warehouse_notes'] ?? $warehouseExport->warehouse_notes,
            'handled_by'      => auth()->id(),
        ];

        if ($data['status'] === 'confirmed') {
            $update['confirmed_at'] = now();
        } elseif ($data['status'] === 'completed') {
            if (! $warehouseExport->confirmed_at) {
                $update['confirmed_at'] = now();
            }
            $update['completed_at'] = now();

            // Trừ tồn kho
            $motorcycle = $warehouseExport->motorcycle;
            $newStock   = max(0, $motorcycle->stock - $warehouseExport->quantity);
            $motorcycle->update(['stock' => $newStock]);
        }

        $warehouseExport->update($update);

        return redirect()->route('admin.warehouse-exports.show', $warehouseExport)
            ->with('success', 'Đã cập nhật trạng thái phiếu xuất kho.');
    }
}
