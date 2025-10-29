<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ManageADSController extends Controller
{
    public function index()
    {
        $ads = Ad::latest()->paginate(10);
        return view('admin.manage-ads', compact('ads'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
            'link' => 'nullable|url',
            'position' => 'required|string',
            'status' => 'required|in:active,inactive',
        ]);

        $path = $request->file('image')->store('ads', 'public');
        try {
            DB::beginTransaction();

            Ad::create([
                'title' => $request->title,
                'image_path' => $path,
                'link' => $request->link,
                'position' => $request->position,
                'status' => $request->status,
            ]);

            DB::commit();
            return response()->json([
                'message' => 'Ad created successfully!',
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Something went wrong. Please try again later.'
            ], 500);
        }
    }

    public function destroy(Ad $ad)
    {
        if ($ad->image_path) {
            Storage::disk('public')->delete($ad->image_path);
        }
        $ad->delete();

        return redirect()->back()->with('success', 'Ad deleted successfully!');
    }
}
