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

        if ($request->ajax()) {
            $query = Product::with(['attributes' => function ($query) {
                $query->with(['vendor.vendorbusinessdetails', 'admin']);
            }, 'category', 'section']);

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

                $vendorHtml = '
                    <div class="child-row-content">
                        <div class="d-flex align-items-center mb-3">
                            <div class="mr-3 text-primary"><i class="fas fa-store fa-lg"></i></div>
                            <h6 class="m-0 font-weight-bold text-dark">Vendor Stock Distribution</h6>
                        </div>
                        <table class="table vendor-table table-sm">
                            <thead>
                                <tr>
                                    <th width="50%">Vendor Name</th>
                                    <th width="25%" class="text-center">Stock Available</th>
                                    <th width="25%" class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>';

                foreach ($vendors as $vendor) {
                    $statusHtml = $vendor['stock'] > 0 ? '<span class="badge badge-success">In Stock</span>' : '<span class="badge badge-danger">Out of Stock</span>';
                    $vendorHtml .= '
                                <tr>
                                    <td class="font-weight-medium">' . $vendor['name'] . '</td>
                                    <td class="text-center font-weight-bold">' . $vendor['stock'] . '</td>
                                    <td class="text-center">' . $statusHtml . '</td>
                                </tr>';
                }

                if (count($vendors) == 0) {
                    $vendorHtml .= '
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3">No vendors assigned</td>
                                </tr>';
                }

                $vendorHtml .= '
                            </tbody>
                        </table>
                    </div>';

                $stockReport[] = [
                    'id' => $product->id,
                    'name' => $product->product_name,
                    'total_stock' => $totalStock,
                    'category' => $product->category->category_name ?? 'N/A',
                    'section' => $product->section->name ?? 'N/A',
                    'vendors_html' => $vendorHtml
                ];
            }

            return \Yajra\DataTables\Facades\DataTables::of(collect($stockReport))
                ->addIndexColumn()
                ->addColumn('expand', function ($row) {
                    return '<i class="fas fa-plus-circle expand-icon"></i>';
                })
                ->addColumn('id_formatted', function ($row) {
                    return '<span class="font-weight-bold text-muted">#' . $row['id'] . '</span>';
                })
                ->addColumn('book_details', function ($row) {
                    return '<div class="d-flex align-items-center">
                                <div class="book-icon">
                                    <i class="fas fa-book-open"></i>
                                </div>
                                <div>
                                    <div class="font-weight-bold text-dark" style="font-size: 1rem;">' . $row['name'] . '</div>
                                </div>
                            </div>';
                })
                ->addColumn('category_section', function ($row) {
                    return '<span class="category-badge mb-1 d-inline-block">' . $row['category'] . '</span>
                            <div class="small text-muted mt-1 pl-1">' . $row['section'] . '</div>';
                })
                ->addColumn('stock_badge', function ($row) {
                    $class = $row['total_stock'] > 10 ? 'stock-high' : 'stock-low';
                    return '<div class="stock-circle ' . $class . '">' . $row['total_stock'] . '</div>';
                })
                ->addColumn('actions', function ($row) {
                    return '<button type="button" class="btn-view-sellers view-details-btn">View Sellers</button>';
                })
                ->addColumn('hidden_details', function ($row) {
                    return $row['vendors_html'];
                })
                ->rawColumns(['expand', 'id_formatted', 'book_details', 'category_section', 'stock_badge', 'actions', 'hidden_details'])
                ->setRowClass('product-row')
                ->make(true);
        }

        return view('admin.reports.stock_report', compact('categories', 'sections'));
    }
}
