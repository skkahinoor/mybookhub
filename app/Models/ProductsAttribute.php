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
}
