<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Section;
use App\Models\Admin;

class CatalogueController extends Controller
{
    private function checkAccess($request)
    {
        $admin = $request->user();

        if (!$admin instanceof Admin) {
            return response()->json([
                'status' => false,
                'message' => 'Only Admin or Vendor can access this.'
            ], 403);
        }

        if (!in_array($admin->type, ['superadmin', 'vendor'])) {
            return response()->json([
                'status' => false,
                'message' => 'Only Admin or Vendor can access this.'
            ], 403);
        }

        if ($admin->status != 1) {
            return response()->json([
                'status' => false,
                'message' => 'Your account is inactive.'
            ], 403);
        }

        return null;
    }

    public function getSection(Request $request)
    {
        if ($resp = $this->checkAccess($request)) return $resp;

        $sections = Section::orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => true,
            'message' => 'Sections fetched successfully',
            'data' => $sections
        ]);
    }

    public function storeSection(Request $request)
    {
        if ($resp = $this->checkAccess($request)) return $resp;

        $request->validate([
            'name' => 'required|string|max:255|unique:sections,name',
        ]);

        $section = Section::create([
            'name' => $request->name,
            'status' => '1',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Section created successfully',
            'data' => $section
        ]);
    }

    public function updateSection(Request $request, $id)
    {
        if ($resp = $this->checkAccess($request)) return $resp;

        $section = Section::find($id);

        if (!$section) {
            return response()->json([
                'status' => false,
                'message' => 'Section not found'
            ], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:sections,name,' . $section->id,
        ]);

        $section->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Section updated successfully',
            'data' => $section
        ]);
    }

    public function destroySection(Request $request, $id)
    {
        if ($resp = $this->checkAccess($request)) return $resp;

        $section = Section::find($id);

        if (!$section) {
            return response()->json([
                'status' => false,
                'message' => 'Section not found'
            ], 404);
        }

        $section->delete();

        return response()->json([
            'status' => true,
            'message' => 'Section deleted successfully'
        ]);
    }
}
