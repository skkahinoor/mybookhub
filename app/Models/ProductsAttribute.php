<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductsAttribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
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
        return $this->belongsTo(Product::class, 'product_id');
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
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Get total stock for product (if size not used)
     */
    public static function getProductStock($product_id, $size = null)
    {
        return (int) self::where('product_id', $product_id)->sum('stock');
    }

    public function ratings()
    {
        return $this->hasMany(\App\Models\Rating::class, 'product_attribute_id');
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
    $product = Product::select('id', 'product_price')
        ->where('id', $product_id)
        ->first();

    if (!$product || $product->product_price <= 0) {
        return [
            'product_price' => 0,
            'final_price'   => 0,
            'discount'      => 0,
            'discount_percent' => 0,
        ];
    }

    // Base price always from Product table
    $original_price = (float) $product->product_price;

    /**
     * Get highest discount from active product attributes
     */
    $discount_percent = self::where('product_id', $product_id)
        ->where('status', 1)
        ->max('product_discount');

    // Safety checks
    $discount_percent = (float) ($discount_percent ?? 0);
    $discount_percent = min(max($discount_percent, 0), 100);

    // Calculate discount
    $discount_amount = ($original_price * $discount_percent) / 100;
    $final_price = $original_price - $discount_amount;

    return [
        'product_price'    => round($original_price, 2),
        'final_price'      => round($final_price, 2),
        'discount'         => round($discount_amount, 2),
        'discount_percent' => round($discount_percent, 2),
    ];
}

}
