<?php

namespace App\Http\Controllers\Api\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\Subject;
use App\Models\Cart;
use App\Models\Section;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\BookType;
use App\Models\Language;
use App\Models\InstitutionManagement;
use App\Models\InstitutionClass;
use App\Models\WalletTransaction;

class HomeController extends Controller
{
    public function home(Request $request)
    {
        // Check if user logged in (optional)
        $user = auth('sanctum')->user();

        if ($user) {
            if ($user->type !== 'student') {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }
        }

        $banners = Banner::where('status', 1)
            ->orderBy('id', 'asc')
            ->get();

        $subject = Subject::where('status', 1)
            ->orderBy('id', 'asc')
            ->get();

        $cartCount = 0;

        if ($user) {
            $cartCount = Cart::where('user_id', $user->id)->count();
        }

        return response()->json([
            'status' => true,
            'message' => 'Home data fetched successfully',

            'user' => $user ? $user : null,

            'cart_count' => $cartCount,

            'data' => [
                'banners' => $banners,
                'subjects' => $subject,
            ]
        ]);
    }

    public function getSections()
    {
        $sections = Section::where('status', 1)
            ->orderBy('name', 'asc')
            ->get(['id', 'name']);

        return response()->json([
            'status' => true,
            'data'   => $sections
        ]);
    }

    public function getInstitutions()
    {
        $institutions = InstitutionManagement::where('status', 1)
            ->orderBy('name', 'asc')
            ->get(['id', 'name']);

        return response()->json([
            'status' => true,
            'data'   => $institutions
        ]);
    }

    public function getInstitutionclass($institution_id)
    {
        $institutionClasses = InstitutionClass::with('subcategory')
            ->where('institution_id', $institution_id)
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $institutionClasses
        ]);
    }

    public function getcategories($section_id)
    {
        $categories = Category::where('status', 1)
            ->where('section_id', $section_id)
            ->orderBy('category_name', 'asc')
            ->get(['id', 'category_name', 'section_id']);

        return response()->json([
            'status' => true,
            'data'   => $categories
        ]);
    }

    public function getSubcategories()
    {
        $subcategory = Subcategory::where('status', 1)
            ->orderBy('subcategory_name', 'asc')
            ->get(['id', 'subcategory_name']);

        return response()->json([
            'status' => true,
            'data'   => $subcategory
        ]);
    }

    public function getBookTypes()
    {
        $booktype = BookType::where('status', 1)
            ->orderBy('book_type', 'asc')
            ->get(['id', 'book_type', 'book_type_icon']);

        return response()->json([
            'status' => true,
            'data'   => $booktype
        ]);
    }

    public function getLanguages()
    {
        $language = Language::where('status', 1)
            ->orderBy('name', 'asc')
            ->get(['id', 'name']);

        return response()->json([
            'status' => true,
            'data'   => $language
        ]);
    }

    public function getWalletTransactions(Request $request)
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not authenticated',
            ], 401);
        }

        $transactions = WalletTransaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Wallet transactions fetched successfully',
            'wallet_balance' => $user->wallet_balance,
            'data' => $transactions
        ]);
    }
}
