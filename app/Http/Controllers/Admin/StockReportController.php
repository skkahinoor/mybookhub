<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class StockReportController extends Controller
{
    public function index(Request $request)
    {
        Session::put('page', 'stock_report');

        // Fetch all categories and sections for filters
        $categories = \App\Models\Category::where('status', 1)->get();
        $sections = \App\Models\Section::where('status', 1)->get();

        $query = Product::with(['attributes' => function ($query) {
            $query->with(['vendor.vendorbusinessdetails', 'admin']);
        }]);

        // Apply filters
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        $products = $query->get();

        $stockReport = [];
        foreach ($products as $product) {
            $totalStock = collect($product->attributes)->sum('stock');

            // Apply stock status filter
            if ($request->filled('stock_status')) {
                if ($request->stock_status == 'in_stock' && $totalStock <= 0) {
                    continue;
                }
                if ($request->stock_status == 'out_of_stock' && $totalStock > 0) {
                    continue;
                }
            }

            $vendorStocks = [];
            foreach (collect($product->attributes) as $attribute) {
                $name = 'Admin';
                if ($attribute->vendor) {
                    $name = $attribute->vendor->vendorbusinessdetails->shop_name ?? 'Unknown Vendor';
                }

                if (!isset($vendorStocks[$name])) {
                    $vendorStocks[$name] = 0;
                }
                $vendorStocks[$name] += $attribute->stock;
            }

            $vendors = [];
            foreach ($vendorStocks as $name => $stock) {
                $vendors[] = [
                    'name' => $name,
                    'stock' => $stock
                ];
            }

            $stockReport[] = [
                'id' => $product->id,
                'name' => $product->product_name,
                'total_stock' => $totalStock,
                'vendors' => $vendors,
                'category' => $product->category->category_name ?? 'N/A',
                'section' => $product->section->name ?? 'N/A'
            ];
        }

        return view('admin.reports.stock_report', compact('stockReport', 'categories', 'sections'));
    }
}
