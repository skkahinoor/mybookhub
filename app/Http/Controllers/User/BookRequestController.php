<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BookRequest;
use App\Models\BookRequestReply;
use App\Models\HeaderLogo;
use App\Models\Notification;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use App\Models\Language;
use App\Models\Product;
use App\Models\Section;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Helpers\RoleHelper;

class BookRequestController extends Controller
{
    private WhatsAppService $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    private function getUserDefaultDistrictId($user)
    {
        $defaultAddress = $user->addresses()
            ->where('is_default', 1)
            ->first();

        if ($defaultAddress && !empty($defaultAddress->district_id)) {
            return $defaultAddress->district_id;
        }

        return null;
    }

    private function getMatchingVendorsForDistrict($districtId)
    {
        if (empty($districtId)) {
            return collect();
        }

        // Return users who have the 'vendor' role and match the district
        // We bypass the Vendor model/Table temporarily as requested
        return User::where('role_id', RoleHelper::vendorId())
            ->where('district_id', $districtId)
            ->where('status', 1)
            ->get();
    }

    private function getUserLocationText($user, $districtId): string
    {
        $address = $user->addresses()
            ->with(['country', 'state', 'district'])
            ->where('is_default', 1)
            ->first();

        if (!$address) {
            $address = $user->addresses()
                ->with(['country', 'state', 'district'])
                ->latest()
                ->first();
        }

        if ($address) {
            $parts = array_filter([
                optional($address->country)->name,
                optional($address->state)->name,
                optional($address->district)->name,
            ]);

            $location = implode(', ', $parts);
            if (!empty($address->pincode)) {
                $location .= ($location ? ' - ' : '') . $address->pincode;
            }

            if (!empty($location)) {
                return $location;
            }
        }

        return optional($user->district)->name ?: ('District #' . $districtId);
    }

    /**
     * User-side search page with "request a book" form.
     */
    public function index(Request $request)
    {
        $condition = session('condition', 'new');
        $sections  = Section::all();
        $logos     = HeaderLogo::all();
        $headerLogo = HeaderLogo::first();
        $language  = Language::get();
        $query = Product::with(['publisher', 'authors'])
            ->where('status', 1);
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhere('product_isbn', 'like', '%' . $search . '%');
            });
        }
        $products = $query->paginate(12)->appends($request->query());
        return view('user.book.indexbookrequest', compact('products', 'logos', 'sections', 'language', 'condition', 'headerLogo'));
    }

    /**
     * Store a new book request from the user.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'book_title'     => 'required|string|max:255',
            'author_name'    => 'nullable|string|max:255',
            'publisher_name' => 'nullable|string|max:255',
            'message'        => 'nullable|string|max:1000',
            'user_location' => 'nullable|string|max:100',
            'user_location_name' => 'nullable|string|max:255',
        ]);

        $districtId = $this->getUserDefaultDistrictId($user);
        if (empty($districtId)) {
            return redirect()->back()->with('error', 'Please set a default address with district before requesting a book.');
        }

        $matchingVendors = $this->getMatchingVendorsForDistrict($districtId);
        if ($matchingVendors->isEmpty()) {
            return redirect()->back()->with('error', 'No vendors are available for your district right now.');
        }

        $userLocationName = $request->user_location_name;
        $userLocation = $request->user_location;

        // If location name is missing but coordinates are present, fetch from Google Maps
        if (empty($userLocationName) && !empty($userLocation)) {
            $apiKey = config('services.google.maps_key');
            if ($apiKey) {
                try {
                    $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                        'latlng' => $userLocation,
                        'key' => $apiKey
                    ]);

                    if ($response->successful()) {
                        $data = $response->json();
                        if (!empty($data['results'][0]['formatted_address'])) {
                            $userLocationName = $data['results'][0]['formatted_address'];
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Google Maps Reverse Geocoding failed: ' . $e->getMessage());
                }
            }
        }

        $bookRequest = BookRequest::create([
            'book_title'        => $request->book_title,
            'author_name'       => $request->author_name,
            'publisher_name'    => $request->publisher_name,
            'message'           => $request->message,
            'requested_by_user' => Auth::id(),
            'district_id'       => $districtId,
            'vendor_id'         => null,
            'user_location' => $userLocation,
            'user_location_name' => $userLocationName,
        ]);

        Notification::create([
            'type' => 'book_request_submitted',
            'title' => 'Book request submitted',
            'message' => 'Your request for "' . $request->book_title . '" has been submitted and shared with vendors in your district.',
            'related_id' => (int) Auth::id(),
            'related_type' => User::class,
            'is_read' => false,
        ]);

        // Send WhatsApp template to opted-in vendors in same district.
        $vendorsForWhatsapp = Vendor::with(['user', 'vendorbusinessdetails'])
            ->where('whatsapp_opt_in', true)
            ->whereNotNull('whatsapp_phone')
            ->whereHas('user', function ($q) use ($districtId) {
                $q->where('district_id', $districtId)->where('status', 1);
            })
            ->get();

        $template = config('services.whatsapp.template', 'book_request_alert_v2');
        $locationText = $request->user_location_name ?: $this->getUserLocationText($user, $districtId);
        foreach ($vendorsForWhatsapp as $vendor) {
            $vendorDisplayName = $vendor->vendorbusinessdetails->shop_name
                ?? $vendor->user->name
                ?? 'Vendor';
            $customerMobile = $user->phone ?? $user->mobile ?? '-';

            $params = [
                $vendorDisplayName,
                $request->book_title ?: '-',
                $request->author_name ?: '-',
                $request->publisher_name ?: '-',
                $locationText,
                $user->name ?: 'BookHub User',
                $customerMobile,
            ];

            $sent = $this->whatsAppService->sendTemplate(
                $vendor->whatsapp_phone,
                $template,
                $params
            );

            if (!$sent) {
                Log::warning('WhatsApp not sent for book request (web).', [
                    'vendor_id' => $vendor->id,
                    'book_request_id' => $bookRequest->id,
                ]);
            }
        }

        return redirect()->route('student.query.index', ['query_id' => $bookRequest->id])
            ->with('success', 'Your book request has been submitted!');
    }

    /**
     * List requests in table with inline accordion.
     */
    public function indexbookrequest()
    {
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        $requestedBooks = BookRequest::where('requested_by_user', Auth::id())
            ->with(['replies.vendor.user', 'vendor.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.book.indexbookrequest', compact('requestedBooks', 'logos', 'headerLogo'));
    }

    public function replyToQuery(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
        ], [
            'message.required' => 'Reply message is required',
        ]);
        $query = BookRequest::find($id);
        if (!$query) {
            return redirect()->back()->with('error', 'Query not found.');
        }

        BookRequestReply::create([
            'book_request_id' => $query->id,
            'reply_by'        => 'user',
            'vendor_id'       => $request->vendor_id ?: null,
            'message'         => $request->message,
        ]);

        return redirect()->back()->with('success', 'Reply sent successfully!');
    }

    public function endConversation(Request $request, $id)
    {
        $query = BookRequest::find($id);
        if (!$query) {
            return redirect()->back()->with('error', 'Query not found.');
        }

        $vendorId = $request->vendor_id ?: null;

        // Mark conversation as ended for this vendor
        BookRequestReply::create([
            'book_request_id' => $query->id,
            'reply_by'        => 'user',
            'vendor_id'       => $vendorId,
            'message'         => 'Student has ended this conversation.',
            'is_ended'        => true,
        ]);

        return redirect()->back()->with('success', 'Conversation with vendor ended successfully!');
    }

    public function indexqueries(Request $request)
    {
        $queries = BookRequest::where('requested_by_user', Auth::id())
            ->with(['replies.vendor.user', 'vendor.user', 'vendor.vendorbusinessdetails'])
            ->orderBy('created_at', 'desc')
            ->get();
        $selectedQueryId = (int) $request->query('query_id', 0);
        $selectedQuery = null;
        if ($queries->isNotEmpty()) {
            $selectedQuery = $selectedQueryId > 0
                ? $queries->firstWhere('id', $selectedQueryId)
                : $queries->first();
        }

        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        return view('user.book.myqueries', compact('queries', 'logos', 'headerLogo', 'selectedQueryId', 'selectedQuery'));
    }

    public function raiseQueryPage()
    {
        $user = Auth::user();
        $districtId = $this->getUserDefaultDistrictId($user);
        $matchingVendors = $this->getMatchingVendorsForDistrict($districtId);
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();

        return view('user.book.raisequery', compact('matchingVendors', 'logos', 'headerLogo', 'districtId'));
    }
}
