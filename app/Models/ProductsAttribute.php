<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductsAttribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'size',
        'price',
        'stock',
        'sku',
        'status',
        'vendor_id',
        'admin_id',
        'admin_type',
        'product_discount',

        // Flags
        'is_featured',
        'is_bestseller'
    ];

    /**
     * Relationship: Product Attribute belongs to a Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relationship: Product Attribute belongs to a Vendor (if vendor_id is not null)
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    /**
     * Relationship: Product Attribute belongs to an Admin (superadmin/admin/subadmin)
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    /**
     * Get total stock for product (if size not used)
     */
    public static function getProductStock($product_id, $size = null)
    {
        return (int) self::where('product_id', $product_id)->sum('stock');
    }

    /**
     * Get attribute status (enabled/disabled)
     */
    public static function getAttributeStatus($product_id, $size = null)
    {
        $getAttributeStatus = self::select('status')->where([
            'product_id' => $product_id,
        ])->first();

        return $getAttributeStatus->status ?? null;
    }

    /**
     * Get discount price details for a product
     * Returns product_price, final_price, and discount amount
     * Always uses product_price from Product table, not the price from ProductsAttribute
     */
    public static function getDiscountPriceDetails($product_id)
    {
        // Get product with product_price from Product table
        $product = Product::select('id', 'product_price', 'category_id')
            ->where('id', $product_id)
            ->first();
            
        if (!$product) {
            return [
                'product_price' => 0,
                'final_price'   => 0,
                'discount'      => 0,
            ];
        }
        
        // CRITICAL: Always use product_price from Product table, NEVER use price from ProductsAttribute
        $original_price = (float) $product->product_price;
        
        // Get product discount from ProductsAttribute table (get first active attribute)
        // We only use this for the discount percentage, NOT for the base price
        $productAttribute = self::where('product_id', $product_id)
            ->where('status', 1)
            ->first();
        $product_discount = $productAttribute ? (float) ($productAttribute->product_discount ?? 0) : 0;
        
        // Get category discount
        $category = Category::select('category_discount')
            ->where('id', $product->category_id)
            ->first();
        $category_discount = $category ? (float) ($category->category_discount ?? 0) : 0;

        // Calculate final price based on discounts
        // Base price is ALWAYS from Product table's product_price
        if ($product_discount > 0) {
            $final_price = $original_price - ($original_price * $product_discount / 100);
            $discount = $original_price - $final_price;
        } elseif ($category_discount > 0) {
            $final_price = $original_price - ($original_price * $category_discount / 100);
            $discount = $original_price - $final_price;
        } else {
            // No discount - final price equals original price from Product table
            $final_price = $original_price;
            $discount = 0;
        }

        return [
            'product_price' => round($original_price, 2),
            'final_price'   => round($final_price, 2),
            'discount'      => round($discount, 2),
        ];
    }
}
