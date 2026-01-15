<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellBookRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_title',
        'author_name',
        'request_message',
        'request_status',
        'admin_notes',
        'isbn',
        'publisher',
        'edition',
        'year_published',
        'book_condition',
        'book_description',
        'expected_price',
        'book_image',
        'book_status',
        'final_admin_notes',
    ];

    protected $casts = [
        'expected_price' => 'decimal:2',
        'year_published' => 'integer',
    ];

    /**
     * Get the user that owns the sell book request.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if request is approved
     */
    public function isApproved()
    {
        return $this->request_status === 'approved';
    }

    /**
     * Check if book details can be filled
     */
    public function canFillBookDetails()
    {
        return $this->request_status === 'approved' && empty($this->isbn);
    }

    /**
     * Check if book details are submitted
     */
    public function hasBookDetails()
    {
        return !empty($this->isbn);
    }
}

