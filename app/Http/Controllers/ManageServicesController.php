<?php

namespace App\Http\Controllers;

use App\Models\ServiceType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ManageServicesController extends Controller
{
    public function index(Request $request)
    {
        $query = ServiceType::query();

        // Handle search
        if ($search = $request->input('search')) {
            $query->where('item_code_id', 'like', "%{$search}%")
                ->orWhere('short_description', 'like', "%{$search}%")
                ->orWhere('item_category', 'like', "%{$search}%");
        }

        // Fetch with pagination
        $services = $query->latest()->paginate(10);

        return view('admin.manage-services', compact('services'));
    }

    public function create(Request $request)
    {
        $lastService = ServiceType::latest('id')->first();
        $nextNumber = $lastService ? ((int) substr($lastService->item_code_id, 3)) + 1 : 1;

        $itemCode = 'LAB' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        $barcode = $itemCode;

        if ($request->ajax()) {
            return response()->json([
                'itemCode' => $itemCode,
                'barcode' => $barcode
            ]);
        }

        return view('admin.create-services', compact('itemCode', 'barcode'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'short_description' => 'required|string|max:255',
            'standard_description' => 'nullable|string',
            'generic_name' => 'nullable|string|max:255',
            'specifications' => 'nullable|string',
            'item_category' => 'required|string|max:100',
            'examination_type' => 'nullable|string|max:100',
        ]);

        DB::beginTransaction();

        try {
            // Generate item code inside store
            $lastService = ServiceType::latest('id')->first();
            $nextNumber = $lastService ? ((int) substr($lastService->item_code_id, 3)) + 1 : 1;
            $validated['item_code_id'] = 'LAB' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            // Barcode same as item code (optional)
            $validated['standard_barcode_id'] = $validated['item_code_id'];

            ServiceType::create($validated);
            DB::commit();

            // 👇 Generate *next* values for the next form entry
            $nextNumber++;
            $nextItemCode = 'LAB' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            $nextBarcode = $nextItemCode;

            return response()->json([
                'message' => 'Service Type created successfully!',
                'next_item_code' => $nextItemCode,
                'next_barcode' => $nextBarcode,
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('ServiceType creation failed: ' . $e->getMessage());
            return response()->json(['message' => 'Something went wrong. Please try again later.'], 500);
        }
    }

    public function edit(ServiceType $service)
    {
        return view('admin.edit-services', compact('service'));
    }

    public function update(Request $request, ServiceType $service)
    {
        $validated = $request->validate([
            'short_description' => 'required|string|max:255',
            'standard_description' => 'nullable|string',
            'generic_name' => 'nullable|string|max:255',
            'specifications' => 'nullable|string',
            'item_category' => 'required|string|max:100',
            'examination_type' => 'nullable|string|max:100',
        ]);

        try {
            $service->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Service Type updated successfully!',
            ]);
        } catch (Exception $e) {
            Log::error('Failed to update service: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update service. Please try again later.',
            ], 500);
        }
    }

    public function destroy(ServiceType $service)
    {
        try {
            $service->delete();

            return response()->json([
                'success' => true,
                'message' => 'Service deleted successfully!'
            ]);
        } catch (Exception $e) {
            Log::error('Failed to delete service: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete service.'
            ], 500);
        }
    }
}
