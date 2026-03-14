<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ProductsAttribute extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::saved(function ($attribute) {
            Cache::put('products_cache_version', time());
        });

        static::deleted(function ($attribute) {
            Cache::put('products_cache_version', time());
        });
    }

    protected $fillable = [
        'product_id',
        'user_id',
        'old_book_condition_id',
        'user_product_price',
        'stock',
        'sku',
        'status',
        'vendor_id',
        'admin_id',
        'admin_type',
        'product_discount',
        'admin_approved',

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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function condition()
    {
        return $this->belongsTo(OldBookCondition::class, 'old_book_condition_id');
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
    /**
     * Scope to only include the "Buy Box Winner" for each product.
     * The Buy Box algorithm picks the best vendor listing based on:
     * 1. Vendor Plan (Pro > Free > Individual)
     * 2. Distance from User (Closer is better)
     * 3. Price (Lower is better)
     * 4. Id (Fallback)
     */
    public function scopeBuyBox($query, $userLat = null, $userLng = null)
    {
        // Try to get coordinates from session if not provided
        $userLat = $userLat ?? session('user_latitude');
        $userLng = $userLng ?? session('user_longitude');

        // Distance formula fragment (in km)
        $distanceSql = "999999"; // default large distance if no user location is available
        if ($userLat && $userLng) {
            $userLat = (float)$userLat;
            $userLng = (float)$userLng;
            // Haversine formula
            $distanceSql = "(6371 * acos(
                cos(radians($userLat)) * cos(radians(SUBSTRING_INDEX(COALESCE(pa_v.location, '0,0'), ',', 1))) * 
                cos(radians(SUBSTRING_INDEX(COALESCE(pa_v.location, '0,0'), ',', -1)) - radians($userLng)) + 
                sin(radians($userLat)) * sin(radians(SUBSTRING_INDEX(COALESCE(pa_v.location, '0,0'), ',', 1)))
            ))";
        }

        return $query->whereIn('products_attributes.id', function ($sub) use ($distanceSql) {
            $sub->select('id')
                ->from(DB::raw("(SELECT pa.id, pa.product_id, 
                    ROW_NUMBER() OVER (PARTITION BY pa.product_id ORDER BY 
                        -- 1. Pro Plan Priority (1: pro, 2: free, 3: student/none)
                        (CASE WHEN pa_v.plan = 'pro' THEN 1 WHEN pa_v.plan = 'free' THEN 2 ELSE 3 END) ASC,
                        -- 2. Distance Priority
                        (CASE WHEN pa_v.location IS NOT NULL THEN $distanceSql ELSE 999999 END) ASC,
                        -- 3. Stock Priority (Higher stock is slightly better)
                        pa.stock DESC,
                        -- 4. Rating Priority (Higher average rating is better)
                        COALESCE(r.avg_rating, 0) DESC,
                        -- 5. Price Priority
                        (CASE 
                            WHEN pa.user_id IS NOT NULL THEN COALESCE(pa.user_product_price, 0)
                            ELSE p.product_price * (1 - COALESCE(pa.product_discount, 0) / 100)
                        END) ASC,
                        -- 6. ID Fallback
                        pa.id ASC
                    ) as rn
                    FROM products_attributes pa
                    JOIN products p ON pa.product_id = p.id
                    LEFT JOIN vendors pa_v ON pa.vendor_id = pa_v.id
                    LEFT JOIN (
                        SELECT product_attribute_id, AVG(rating) as avg_rating 
                        FROM ratings 
                        WHERE status = 1 
                        GROUP BY product_attribute_id
                    ) r ON pa.id = r.product_attribute_id
                    WHERE pa.status = 1 AND pa.stock > 0
                ) as ranked_attribs"))
                ->where('rn', 1);
        });
    }

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
