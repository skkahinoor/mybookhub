<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthorProduct extends Model
{
    use HasFactory;

    protected $table = 'author_product';

    protected $fillable = [
        'author_id',
        'product_id',
    ];

    public function author()
    {
        return $this->belongsTo(Author::class, 'author_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
