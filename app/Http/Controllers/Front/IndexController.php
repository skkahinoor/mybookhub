<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\HeaderLogo;
use App\Models\Language;
use App\Models\Product;
use App\Models\Section;
use App\Models\User;
use App\Models\ProductsAttribute;
use App\Models\FilterClassSubject;
use App\Models\Subcategory;
use App\Models\Subject;
use App\Models\BookType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IndexController extends Controller
{
    public function index(Request $request)
    {
        $condition = session('condition', 'all');

        $sessionSectionId = $request->cookie('bg_section_id') ?? session('bg_section_id');
        $sessionCategoryId = $request->cookie('bg_category_id') ?? session('bg_category_id');
        $sessionSubcategoryId = $request->cookie('bg_subcategory_id') ?? session('bg_subcategory_id');
        $sessionSubjectId = $request->cookie('bg_subject_id') ?? session('bg_subject_id');

        // Priority 1: Direct Request Params (Manually selected filters)
        $currentSectionId = $request->filled('section_id') ? $request->section_id : null;
        $currentCategoryId = $request->filled('category_id') ? $request->category_id : null;
        $currentSubcategoryId = $request->filled('subcategory_id') ? $request->subcategory_id : null;
        $currentSubjectId = $request->filled('subject_id') ? $request->subject_id : null;

        // If no direct request params, check Profile or Session (only on initial/normal page load)
        if (!$request->has('filter_update')) {
            if (!$currentSectionId) {
                if (Auth::check()) {
                    // Priority 2: Academic Profile for Students
                    $profile = \App\Models\AcademicProfile::where('user_id', Auth::id())->first();
                    if ($profile && $profile->education_level_id) {
                        if (!$currentSectionId) $currentSectionId = $profile->education_level_id;
                        if (!$currentCategoryId) $currentCategoryId = $profile->board_id;
                        if (!$currentSubcategoryId) $currentSubcategoryId = $profile->class_id;
                    } else {
                        // Priority 3: Fallback to Session/Cookie for logged in users without profile
                        if (!$currentSectionId) $currentSectionId = $sessionSectionId;
                        if (!$currentCategoryId) $currentCategoryId = $sessionCategoryId;
                        if (!$currentSubcategoryId) $currentSubcategoryId = $sessionSubcategoryId;
                        if (!$currentSubjectId) $currentSubjectId = $sessionSubjectId;
                    }
                } else {
                    // Priority 3: Session/Cookie for Guest Users
                    if (!$currentSectionId) $currentSectionId = $sessionSectionId;
                    if (!$currentCategoryId) $currentCategoryId = $sessionCategoryId;
                    if (!$currentSubcategoryId) $currentSubcategoryId = $sessionSubcategoryId;
                    if (!$currentSubjectId) $currentSubjectId = $sessionSubjectId;
                }
            }
        }

        // Verify that category and subcategory belong to the selected section to avoid session pollution
        if ($currentSectionId && $currentCategoryId) {
            $catExists = Category::where('id', $currentCategoryId)->where('section_id', $currentSectionId)->exists();
            if (!$catExists) {
                $currentCategoryId = null;
                $currentSubcategoryId = null;
                $currentSubjectId = null;
            }
        }

        if ($request->filled('distance')) {
            session(['distance' => (int) $request->distance]);
        }
        $currentDistance = session('distance', 20);
        $userLat = session('user_latitude');
        $userLng = session('user_longitude');

        if ($request->ajax() && $request->has('filter_update')) {
            $sliderProductsQuery = $this->homeSliderProductsBaseQuery();
            $this->applyHomeSliderFilters(
                $sliderProductsQuery,
                $request,
                $condition,
                $currentSectionId,
                $currentCategoryId,
                $currentSubcategoryId,
                $currentSubjectId,
                $currentDistance,
                $userLat,
                $userLng
            );
            $sliderProducts = $sliderProductsQuery->orderBy('id', 'desc')->paginate(12);
            $homeSubjects = $this->loadHomeSubjects($currentSectionId, $currentCategoryId, $currentSubcategoryId);
            $sliderProductDiscountPrices = Product::getDiscountPricesForProductIds(
                $sliderProducts->pluck('product_id')->all()
            );

            return response()->json([
                'html' => view('front.partials.home_product_grid', compact('sliderProducts', 'sliderProductDiscountPrices'))->render(),
                'subjects_html' => view('front.partials.home_subjects', compact('homeSubjects'))->render(),
                'has_more' => $sliderProducts->hasMorePages(),
                'info' => $request->input('info', ''),
            ]);
        }

        if ($request->ajax()) {
            $newProducts = $this->homeNewProductsQuery($condition)->get();
            $newProductDiscountPrices = Product::getDiscountPricesForProductIds($newProducts->pluck('id')->all());

            return view('front.partials.new_products', compact('newProducts', 'newProductDiscountPrices'))->render();
        }

        $sliderProductsQuery = $this->homeSliderProductsBaseQuery();
        $this->applyHomeSliderFilters(
            $sliderProductsQuery,
            $request,
            $condition,
            $currentSectionId,
            $currentCategoryId,
            $currentSubcategoryId,
            $currentSubjectId,
            $currentDistance,
            $userLat,
            $userLng
        );
        $sliderProducts = $sliderProductsQuery->orderBy('id', 'desc')->paginate(12);
        $sliderProductDiscountPrices = Product::getDiscountPricesForProductIds(
            $sliderProducts->pluck('product_id')->all()
        );

        $homeSubjects = $this->loadHomeSubjects($currentSectionId, $currentCategoryId, $currentSubcategoryId);

        $logos = HeaderLogo::first();
        $sections = Section::all();

        $meta_title = 'BookHub - The Only Hub For Students';
        $meta_description = 'The cross platform where students meets their career through books.';
        $meta_keywords = 'eshop website, online shopping, multi vendor e-commerce';

        $displayCategoryName = $currentCategoryId
            ? Category::where('id', $currentCategoryId)->value('category_name')
            : null;
        $displaySubcategoryName = $currentSubcategoryId
            ? Subcategory::where('id', $currentSubcategoryId)->value('subcategory_name')
            : null;

        return view('front.index3', [
            'languages' => Language::where('status', 1)->get(),
            'bookTypes' => BookType::where('status', 1)->get(),
        ])->with(compact(
            'meta_title',
            'meta_description',
            'meta_keywords',
            'sections',
            'logos',
            'sliderProducts',
            'sliderProductDiscountPrices',
            'homeSubjects',
            'currentSectionId',
            'currentCategoryId',
            'currentSubcategoryId',
            'displayCategoryName',
            'displaySubcategoryName'
        ));
    }

    private function homeSliderProductsBaseQuery(): Builder
    {
        return ProductsAttribute::with(['product.authors', 'product.publisher', 'condition', 'vendor.vendorbusinessdetails'])
            ->whereHas('product', function ($q) {
                $q->where('status', 1);
            })
            ->where('status', 1)
            ->where('stock', '>', 0);
    }

    private function homeNewProductsQuery(string $condition): Builder
    {
        return Product::with(['authors', 'publisher'])
            ->whereHas('attributes', function ($q) {
                $q->where('status', 1);
            })
            ->when($condition !== 'all', function ($query) use ($condition) {
                $query->where('condition', $condition);
            })
            ->when(session('language') && session('language') !== 'all', function ($query) {
                $query->where('language_id', session('language'));
            })
            ->where('status', 1)
            ->orderBy('id', 'desc');
    }

    private function applyHomeSliderFilters(
        Builder $sliderProductsQuery,
        Request $request,
        string $condition,
        $currentSectionId,
        $currentCategoryId,
        $currentSubcategoryId,
        $currentSubjectId,
        $currentDistance,
        $userLat,
        $userLng
    ): void {
        $sliderProductsQuery->whereHas('product', function ($q) use ($request, $condition, $currentSectionId, $currentCategoryId, $currentSubcategoryId, $currentSubjectId) {
            if ($currentSectionId) {
                $q->where('section_id', $currentSectionId);
            }
            if ($currentCategoryId) {
                $q->where('category_id', $currentCategoryId);
            }
            if ($currentSubcategoryId) {
                $q->where('subcategory_id', $currentSubcategoryId);
            }
            if ($request->filled('condition')) {
                if ($request->condition !== 'all') {
                    $q->where('condition', $request->condition);
                }
            } elseif ($condition !== 'all') {
                $q->where('condition', $condition);
            }
            if ($currentSubjectId) {
                $q->where('subject_id', $currentSubjectId);
            }
            if ($request->filled('languages')) {
                $langIds = is_array($request->input('languages'))
                    ? $request->input('languages')
                    : explode(',', (string) $request->input('languages'));
                $q->whereIn('language_id', $langIds);
            }
        });

        if ($userLat && $userLng && $currentDistance < 100) {
            $distance = $currentDistance;
            $sliderProductsQuery->where(function ($query) use ($userLat, $userLng, $distance) {
                $query->whereHas('vendor.vendorbusinessdetails', function ($q) use ($userLat, $userLng, $distance) {
                    $q->whereNotNull('latitude')
                      ->whereNotNull('longitude')
                      ->whereRaw("(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) <= ?", [$userLat, $userLng, $userLat, $distance]);
                })
                ->orWhereHas('vendor', function ($q) use ($userLat, $userLng, $distance) {
                    $q->whereDoesntHave('vendorbusinessdetails')
                      ->whereNotNull('location')
                      ->whereRaw("(6371 * acos(cos(radians(?)) * cos(radians(SUBSTRING_INDEX(location, ',', 1))) * cos(radians(SUBSTRING_INDEX(location, ',', -1)) - radians(?)) + sin(radians(?)) * sin(radians(SUBSTRING_INDEX(location, ',', 1))))) <= ?", [$userLat, $userLng, $userLat, $distance]);
                });
            });
        }
    }

    private function loadHomeSubjects($currentSectionId, $currentCategoryId, $currentSubcategoryId)
    {
        if ($currentSectionId || $currentCategoryId || $currentSubcategoryId) {
            $subjectIdsQuery = FilterClassSubject::query();
            if ($currentSectionId) {
                $subjectIdsQuery->where('section_id', $currentSectionId);
            }
            if ($currentCategoryId) {
                $subjectIdsQuery->where('category_id', $currentCategoryId);
            }
            if ($currentSubcategoryId) {
                $subjectIdsQuery->where('sub_category_id', $currentSubcategoryId);
            }
            $subjectIds = $subjectIdsQuery->distinct()->pluck('subject_id');

            return Subject::whereIn('id', $subjectIds)->where('status', 1)->get();
        }

        return Subject::where('status', 1)->limit(20)->get();
    }

    public function setLanguage(Request $request)
    {
        session(['language' => $request->language]);
        return response()->json(['success' => true]);
    }

    public function setCondition(Request $request)
    {
        session(['condition' => $request->condition]);
        return response()->json(['success' => true]);
    }

    public function setBookgenieSession(Request $request)
    {
        $minutes = 525600 * 5; // 5 years

        if ($request->has('bookgenie_shown')) {
            session(['bookgenie_shown' => true]);
            \Illuminate\Support\Facades\Cookie::queue('bookgenie_shown', 'true', $minutes);
        }

        if ($request->has('section_id')) {
            $sectionId = $request->input('section_id');
            if (empty($sectionId)) {
                session()->forget(['bg_section_id', 'bg_category_id', 'bg_subcategory_id', 'bg_subject_id']);
                \Illuminate\Support\Facades\Cookie::queue(\Illuminate\Support\Facades\Cookie::forget('bg_section_id'));
                \Illuminate\Support\Facades\Cookie::queue(\Illuminate\Support\Facades\Cookie::forget('bg_category_id'));
                \Illuminate\Support\Facades\Cookie::queue(\Illuminate\Support\Facades\Cookie::forget('bg_subcategory_id'));
                \Illuminate\Support\Facades\Cookie::queue(\Illuminate\Support\Facades\Cookie::forget('bg_subject_id'));
            } else {
                $oldSectionId = session('bg_section_id') ?? \Illuminate\Support\Facades\Cookie::get('bg_section_id');
                if ($oldSectionId != $sectionId) {
                    session()->forget(['bg_category_id', 'bg_subcategory_id', 'bg_subject_id']);
                    \Illuminate\Support\Facades\Cookie::queue(\Illuminate\Support\Facades\Cookie::forget('bg_category_id'));
                    \Illuminate\Support\Facades\Cookie::queue(\Illuminate\Support\Facades\Cookie::forget('bg_subcategory_id'));
                    \Illuminate\Support\Facades\Cookie::queue(\Illuminate\Support\Facades\Cookie::forget('bg_subject_id'));
                }
                session(['bg_section_id' => $sectionId]);
                \Illuminate\Support\Facades\Cookie::queue('bg_section_id', (string)$sectionId, $minutes);
            }
        }

        if ($request->has('category_id')) {
            $categoryId = $request->input('category_id');
            if (empty($categoryId)) {
                session()->forget(['bg_category_id', 'bg_subcategory_id', 'bg_subject_id']);
                \Illuminate\Support\Facades\Cookie::queue(\Illuminate\Support\Facades\Cookie::forget('bg_category_id'));
                \Illuminate\Support\Facades\Cookie::queue(\Illuminate\Support\Facades\Cookie::forget('bg_subcategory_id'));
                \Illuminate\Support\Facades\Cookie::queue(\Illuminate\Support\Facades\Cookie::forget('bg_subject_id'));
            } else {
                $oldCategoryId = session('bg_category_id') ?? \Illuminate\Support\Facades\Cookie::get('bg_category_id');
                if ($oldCategoryId != $categoryId) {
                    session()->forget(['bg_subcategory_id', 'bg_subject_id']);
                    \Illuminate\Support\Facades\Cookie::queue(\Illuminate\Support\Facades\Cookie::forget('bg_subcategory_id'));
                    \Illuminate\Support\Facades\Cookie::queue(\Illuminate\Support\Facades\Cookie::forget('bg_subject_id'));
                }
                session(['bg_category_id' => $categoryId]);
                \Illuminate\Support\Facades\Cookie::queue('bg_category_id', (string)$categoryId, $minutes);
            }
        }

        if ($request->has('subcategory_id')) {
            $subcategoryId = $request->input('subcategory_id');
            if (empty($subcategoryId)) {
                session()->forget(['bg_subcategory_id', 'bg_subject_id']);
                \Illuminate\Support\Facades\Cookie::queue(\Illuminate\Support\Facades\Cookie::forget('bg_subcategory_id'));
                \Illuminate\Support\Facades\Cookie::queue(\Illuminate\Support\Facades\Cookie::forget('bg_subject_id'));
            } else {
                session(['bg_subcategory_id' => $subcategoryId]);
                \Illuminate\Support\Facades\Cookie::queue('bg_subcategory_id', (string)$subcategoryId, $minutes);
            }
        }

        if ($request->has('subject_id')) {
            $subjectId = $request->input('subject_id');
            if (empty($subjectId)) {
                session()->forget('bg_subject_id');
                \Illuminate\Support\Facades\Cookie::queue(\Illuminate\Support\Facades\Cookie::forget('bg_subject_id'));
            } else {
                session(['bg_subject_id' => $subjectId]);
                \Illuminate\Support\Facades\Cookie::queue('bg_subject_id', (string)$subjectId, $minutes);
            }
        }

        // Sync with AcademicProfile if logged in
        if (Auth::check() && ($request->filled('section_id') || $request->filled('category_id') || $request->filled('subcategory_id'))) {
            \App\Models\AcademicProfile::updateOrCreate(
                ['user_id' => Auth::id()],
                [
                    'education_level_id' => $request->section_id ?? session('bg_section_id'),
                    'board_id' => $request->category_id ?? session('bg_category_id'),
                    'class_id' => $request->subcategory_id ?? session('bg_subcategory_id'),
                ]
            );
        }

        return response()->json(['success' => true]);
    }

    public function setWelcomeSession(Request $request)
    {
        if ($request->has('welcome_shown')) {
            session(['welcome_shown' => true]);
        }
        return response()->json(['success' => true]);
    }

    public function bookgenieSearch(Request $request)
    {
        $query = trim($request->get('q', ''));
        $sectionId = $request->get('section_id');
        $categoryId = $request->get('category_id');
        $subcategoryId = $request->get('subcategory_id');
        $subjectId = $request->get('subject_id');

        $botResponseText = "";
        $refinedSearchQuery = $query;

        // Relax length requirement if filters are present
        $hasFilters = $request->filled('section_id') || $request->filled('category_id') || $request->filled('subcategory_id') || $request->filled('subject_id') || !empty($sectionId) || !empty($categoryId) || !empty($subcategoryId) || !empty($subjectId);

        if (strlen($query) < 2 && !$hasFilters) {
            return response()->json(['results' => [], 'message' => 'Please type at least 2 characters.']);
        }

        if (!empty($query)) {
            // Check if Gemini API key is configured in env
            $geminiApiKey = env('GEMINI_API_KEY');
            if (!empty($geminiApiKey)) {
                try {
                    // Fetch current catalog metadata for grounding/mapping (cached for performance)
                    $metadata = \Illuminate\Support\Facades\Cache::remember('bg_search_metadata', 3600, function() {
                        return [
                            'sections' => Section::where('status', 1)->get(['id', 'name'])->toArray(),
                            'categories' => Category::where('status', 1)->get(['id', 'category_name'])->toArray(),
                            'subcategories' => Subcategory::where('status', 1)->get(['id', 'subcategory_name'])->toArray(),
                            'subjects' => Subject::where('status', 1)->get(['id', 'name'])->toArray(),
                        ];
                    });

                    // Prepare Prompt
                    $prompt = "You are 'BookGenie', a helpful book assistant for BookHub. 
User's message: \"{$query}\"

We have the following catalog entities in our database:
Sections (Education Levels): " . json_encode($metadata['sections']) . "
Categories (Boards): " . json_encode($metadata['categories']) . "
Subcategories (Classes/Streams): " . json_encode($metadata['subcategories']) . "
Subjects: " . json_encode($metadata['subjects']) . "

Task:
1. Parse the user's natural query and map it to any matching Section, Category, Subcategory, or Subject.
2. Formulate a friendly, warm, human-like chat response (maximum 2-3 sentences) explaining what you found (e.g. \"I found some CBSE Board books for Class 10. Here they are:\").
3. Extract any specific keyword for text search (e.g. if query is \"cbse ncert math\", mapping CBSE is category, math is subject, and \"ncert\" is the remaining search query keyword).
4. Reply ONLY in raw JSON format with no markdown blocks (do not enclose in ```json ... ```), using this exact schema:
{
  \"response_text\": \"Your friendly human response here.\",
  \"section_id\": matched_section_id_or_null,
  \"category_id\": matched_category_id_or_null,
  \"subcategory_id\": matched_subcategory_id_or_null,
  \"subject_id\": matched_subject_id_or_null,
  \"search_query\": \"extracted_remaining_search_keyword_or_null\"
}";

                    $response = \Illuminate\Support\Facades\Http::withHeaders([
                        'Content-Type' => 'application/json'
                    ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$geminiApiKey}", [
                        'contents' => [
                            ['parts' => [['text' => $prompt]]]
                        ]
                    ]);

                    if ($response->successful()) {
                        $resData = $response->json();
                        $text = $resData['candidates'][0]['content']['parts'][0]['text'] ?? '';
                        $text = trim($text);
                        if (str_starts_with($text, '```')) {
                            $text = preg_replace('/^```(?:json)?|```$/', '', $text);
                            $text = trim($text);
                        }
                        $json = json_decode($text, true);
                        if (is_array($json)) {
                            if (!empty($json['section_id'])) $sectionId = $json['section_id'];
                            if (!empty($json['category_id'])) $categoryId = $json['category_id'];
                            if (!empty($json['subcategory_id'])) $subcategoryId = $json['subcategory_id'];
                            if (!empty($json['subject_id'])) $subjectId = $json['subject_id'];
                            if (!empty($json['search_query']) && strtolower($json['search_query']) !== 'null' && strtolower($json['search_query']) !== 'none') {
                                $refinedSearchQuery = $json['search_query'];
                            } else {
                                $refinedSearchQuery = "";
                            }
                            if (!empty($refinedSearchQuery)) {
                                $refinedSearchQuery = str_replace(['>', '-', '/', '|', '+', ':', ','], ' ', $refinedSearchQuery);
                                $refinedSearchQuery = trim(preg_replace('/\s+/', ' ', $refinedSearchQuery));
                                if (strlen($refinedSearchQuery) < 2) {
                                    $refinedSearchQuery = "";
                                }
                            }
                            $botResponseText = $json['response_text'] ?? "";
                        }
                    }
                } catch (\Exception $e) {
                    // Fall back to local parsing silently
                }
            }

            // If we didn't use Gemini or it failed, use the Local NLP Rule-based Matcher
            if (empty($botResponseText)) {
                $matchedSection = null;
                $matchedCategory = null;
                $matchedSubcategory = null;
                $matchedSubject = null;

                // Simple normalization
                $normQuery = strtolower($query);

                // Fetch sections, categories, subcategories, subjects
                $sectionsList = Section::where('status', 1)->get();
                $categoriesList = Category::where('status', 1)->get();
                $subcategoriesList = Subcategory::where('status', 1)->get();
                $subjectsList = Subject::where('status', 1)->get();

                $matchWord = function($q, $e) {
                    if (empty($q) || empty($e)) return false;
                    return preg_match('/\b' . preg_quote($e, '/') . '\b/i', $q) || preg_match('/\b' . preg_quote($q, '/') . '\b/i', $e);
                };

                // Match Category (Board)
                foreach ($categoriesList as $cat) {
                    $catName = strtolower($cat->category_name);
                    if ($matchWord($normQuery, $catName)) {
                        $matchedCategory = $cat;
                        break;
                    }
                    if ($catName == 'cbse' && (str_contains($normQuery, 'cbse') || str_contains($normQuery, 'central board'))) {
                        $matchedCategory = $cat;
                        break;
                    }
                    if ($catName == 'icse' && (str_contains($normQuery, 'icse') || str_contains($normQuery, 'indian certificate'))) {
                        $matchedCategory = $cat;
                        break;
                    }
                    if (($catName == 'chse odisha' || $catName == 'chse') && (str_contains($normQuery, 'chse') || str_contains($normQuery, 'odisha'))) {
                        $matchedCategory = $cat;
                        break;
                    }
                    if ($catName == 'bse' && str_contains($normQuery, 'bse')) {
                        $matchedCategory = $cat;
                        break;
                    }
                }

                // Match Subcategory (Class)
                foreach ($subcategoriesList as $subcat) {
                    $subName = strtolower($subcat->subcategory_name);
                    if ($matchWord($normQuery, $subName)) {
                        $matchedSubcategory = $subcat;
                        break;
                    }
                    if (preg_match('/\b(10|10th|x|tenth)\b/', $normQuery) && preg_match('/\b(x|10)\b/i', $subName)) {
                        $matchedSubcategory = $subcat;
                        break;
                    }
                    if (preg_match('/\b(12|12th|xii|twelfth)\b/', $normQuery) && preg_match('/\b(xii|12)\b/i', $subName)) {
                        $matchedSubcategory = $subcat;
                        break;
                    }
                    if (preg_match('/\b(9|9th|ix|nineth)\b/', $normQuery) && preg_match('/\b(ix|9)\b/i', $subName)) {
                        $matchedSubcategory = $subcat;
                        break;
                    }
                    if (preg_match('/\b(11|11th|xi|eleventh)\b/', $normQuery) && preg_match('/\b(xi|11)\b/i', $subName)) {
                        $matchedSubcategory = $subcat;
                        break;
                    }
                    if (preg_match('/\b(8|8th|viii|eighth)\b/', $normQuery) && preg_match('/\b(viii|8)\b/i', $subName)) {
                        $matchedSubcategory = $subcat;
                        break;
                    }
                }

                // Match Subject
                foreach ($subjectsList as $subj) {
                    $subjName = strtolower($subj->name);
                    if ($matchWord($normQuery, $subjName)) {
                        $matchedSubject = $subj;
                        break;
                    }
                    if ($subjName == 'mathematics' && (str_contains($normQuery, 'math') || str_contains($normQuery, 'maths'))) {
                        $matchedSubject = $subj;
                        break;
                    }
                }

                // Match Section
                foreach ($sectionsList as $sec) {
                    $secName = strtolower($sec->name);
                    if ($matchWord($normQuery, $secName)) {
                        $matchedSection = $sec;
                        break;
                    }
                }

                // If matched, apply to filters
                if ($matchedSection) $sectionId = $matchedSection->id;
                if ($matchedCategory) $categoryId = $matchedCategory->id;
                if ($matchedSubcategory) $subcategoryId = $matchedSubcategory->id;
                if ($matchedSubject) $subjectId = $matchedSubject->id;

                // Formulate Response
                $matchedNames = [];
                if ($matchedCategory) $matchedNames[] = $matchedCategory->category_name;
                if ($matchedSubcategory) $matchedNames[] = $matchedSubcategory->subcategory_name;
                if ($matchedSubject) $matchedNames[] = $matchedSubject->name;

                // Remove the matched keywords from the query to refine search
                $cleanQuery = $query;
                if ($matchedSection) {
                    $cleanQuery = preg_replace('/\b' . preg_quote($matchedSection->name, '/') . '\b/i', '', $cleanQuery);
                }
                if ($matchedCategory) {
                    $cleanQuery = preg_replace('/\b' . preg_quote($matchedCategory->category_name, '/') . '\b/i', '', $cleanQuery);
                }
                $cleanQuery = preg_replace('/\b(cbse|icse|bse|chse)\b/i', '', $cleanQuery);
                if ($matchedSubcategory) {
                    $cleanQuery = preg_replace('/\b' . preg_quote($matchedSubcategory->subcategory_name, '/') . '\b/i', '', $cleanQuery);
                    $cleanQuery = preg_replace('/\b(class\s+)?(10|12|9|8|11)(th)?\b/i', '', $cleanQuery);
                    $cleanQuery = preg_replace('/\b(x|xii|ix|xi|viii)\b/i', '', $cleanQuery);
                }
                if ($matchedSubject) {
                    $cleanQuery = preg_replace('/\b' . preg_quote($matchedSubject->name, '/') . '\b/i', '', $cleanQuery);
                    $cleanQuery = preg_replace('/\b(math|maths|physics|chemistry|biology|science)\b/i', '', $cleanQuery);
                }
                
                // Replace separators and symbols
                $cleanQuery = str_replace(['>', '-', '/', '|', '+', ':', ','], ' ', $cleanQuery);
                
                $refinedSearchQuery = trim(preg_replace('/\s+/', ' ', $cleanQuery));
                if (strlen($refinedSearchQuery) < 2) {
                    $refinedSearchQuery = "";
                }

                if (!empty($matchedNames)) {
                    $filterDesc = implode(' > ', array_filter($matchedNames));
                    if (!empty($refinedSearchQuery)) {
                        $botResponseText = "🔍 I've filtered the catalog for **{$filterDesc}** and searched for \"{$refinedSearchQuery}\". Here are the matching books:";
                    } else {
                        $botResponseText = "📚 I've filtered the catalog for **{$filterDesc}** books. Check out the options available below:";
                    }
                } else {
                    if (in_array($normQuery, ['hi', 'hello', 'hey', 'greetings'])) {
                        $botResponseText = "👋 Hello! I'm BookGenie, your virtual helper. I can help you search, filter, and discover books easily. Ask me anything, like *\"CBSE Class 10 Science\"*!";
                        $refinedSearchQuery = "";
                    } else {
                        $botResponseText = "Here are the books matching your search query \"{$query}\":";
                        $refinedSearchQuery = $query;
                    }
                }
            }
        } else {
            // No q param but filters are present (e.g. from selecting chips/dropdowns directly)
            $matchedNames = [];
            if ($sectionId) $matchedNames[] = Section::where('id', $sectionId)->value('name');
            if ($categoryId) $matchedNames[] = Category::where('id', $categoryId)->value('category_name');
            if ($subcategoryId) $matchedNames[] = Subcategory::where('id', $subcategoryId)->value('subcategory_name');
            if ($subjectId) $matchedNames[] = Subject::where('id', $subjectId)->value('name');

            if (!empty($matchedNames)) {
                $botResponseText = "📚 Showing results for: **" . implode(' > ', array_filter($matchedNames)) . "**";
            } else {
                $botResponseText = "Here are some books I found in our database:";
            }
        }

        $userLat = session('user_latitude');
        $userLng = session('user_longitude');

        \Illuminate\Support\Facades\Log::info('BookGenie Search Debug', [
            'q' => $request->get('q'),
            'section_id' => $request->get('section_id'),
            'category_id' => $request->get('category_id'),
            'subcategory_id' => $request->get('subcategory_id'),
            'subject_id' => $request->get('subject_id'),
            'matched_section' => $sectionId,
            'matched_category' => $categoryId,
            'matched_subcategory' => $subcategoryId,
            'matched_subject' => $subjectId,
            'refined_query' => $refinedSearchQuery,
        ]);

        $results = Product::with([
                'publisher',
                'authors',
                'category',
                'attributes.vendor.vendorbusinessdetails',
            ])
            ->where('status', 1)
            ->whereHas('attributes', function($q) {
                $q->where('status', 1);
            })
            ->where(function ($q) use ($refinedSearchQuery, $sectionId, $categoryId, $subcategoryId, $subjectId) {
                if ($sectionId) $q->where('section_id', $sectionId);
                if ($categoryId) $q->where('category_id', $categoryId);
                if ($subcategoryId) $q->where('subcategory_id', $subcategoryId);
                if ($subjectId) $q->where('subject_id', $subjectId);

                if (!empty($refinedSearchQuery)) {
                    $q->where(function ($q2) use ($refinedSearchQuery) {
                        $q2->where('product_name', 'like', "%{$refinedSearchQuery}%")
                            ->orWhere('product_isbn', 'like', "%{$refinedSearchQuery}%")
                            ->orWhere('description', 'like', "%{$refinedSearchQuery}%")
                            ->orWhereHas('category', function($q3) use ($refinedSearchQuery) {
                                $q3->where('category_name', 'like', "%{$refinedSearchQuery}%");
                            })
                            ->orWhereHas('subcategory', function($q3) use ($refinedSearchQuery) {
                                $q3->where('subcategory_name', 'like', "%{$refinedSearchQuery}%");
                            })
                            ->orWhereHas('subject', function($q3) use ($refinedSearchQuery) {
                                $q3->where('name', 'like', "%{$refinedSearchQuery}%");
                            });
                    });
                }
            })
            ->limit(10)
            ->get();

        $formatted = $results->map(function ($product) use ($userLat, $userLng) {
            // Get best seller (winner) for this product
            $bestAttr = ProductsAttribute::where('product_id', $product->id)
                ->where('status', 1)
                ->where('stock', '>', 0)
                ->buyBox()
                ->first();

            if (!$bestAttr) {
                $bestAttr = ProductsAttribute::where('product_id', $product->id)
                    ->where('status', 1)
                    ->first();
            }

            $vendor = $bestAttr ? $bestAttr->vendor : null;

            // Calculate distance
            $distance = null;
            $vLat = null;
            $vLng = null;
            if ($vendor) {
                if ($vendor->vendorbusinessdetails && !empty($vendor->vendorbusinessdetails->latitude) && !empty($vendor->vendorbusinessdetails->longitude)) {
                    $vLat = $vendor->vendorbusinessdetails->latitude;
                    $vLng = $vendor->vendorbusinessdetails->longitude;
                } elseif ($vendor->location) {
                    [$vLat, $vLng] = array_pad(explode(',', $vendor->location), 2, null);
                }
            }

            if ($userLat && $userLng && is_numeric($vLat) && is_numeric($vLng)) {
                $R  = 6371;
                $dL = deg2rad((float)$vLat - (float)$userLat);
                $dN = deg2rad((float)$vLng - (float)$userLng);
                $a  = sin($dL / 2) ** 2 + cos(deg2rad((float)$userLat)) * cos(deg2rad((float)$vLat)) * sin($dN / 2) ** 2;
                $distance = round($R * 2 * atan2(sqrt($a), sqrt(1 - $a)), 1);
            }

            $finalPrice = Product::getDiscountPrice($product->id);
            $shopName   = optional($vendor?->vendorbusinessdetails)->shop_name ?? 'Individual Seller';
            $address    = optional($vendor?->vendorbusinessdetails)->shop_address ?? '';

            return [
                'id'          => $bestAttr ? $bestAttr->id : null,
                'product_id'  => $product->id,
                'name'        => $product->product_name,
                'isbn'        => $product->product_isbn,
                'image'       => $product->product_image
                    ? getBookCoverUrl($product->product_image)
                    : null,
                'price'       => '₹' . number_format($finalPrice, 0),
                'shop'        => $shopName,
                'address'     => $address,
                'distance'    => $distance !== null ? $distance . ' km away' : null,
                'url'         => route('front.products.detail', $product->id),
            ];
        });

        if ($results->isEmpty() && empty($botResponseText)) {
            $botResponseText = "Sorry, I couldn't find any books matching \"" . e($query) . "\". Try another search or browse by category.";
        }

        return response()->json([
            'results' => $formatted,
            'message' => $botResponseText,
            'active_filters' => [
                'section_id' => $sectionId,
                'category_id' => $categoryId,
                'subcategory_id' => $subcategoryId,
                'subject_id' => $subjectId,
            ]
        ]);
    }

    public function getFilterCategories(Request $request)
    {
        $categoryIds = FilterClassSubject::where('section_id', $request->section_id)
            ->distinct()
            ->pluck('category_id');

        if ($categoryIds->isEmpty()) {
            $categories = Category::where('section_id', $request->section_id)
                ->where('status', 1)
                ->get(['id', 'category_name']);
        } else {
            $categories = Category::whereIn('id', $categoryIds)
                ->where('status', 1)
                ->get(['id', 'category_name']);
        }

        return response()->json($categories);
    }

    public function getFilterSubcategories(Request $request)
    {
        $subcategoryIds = FilterClassSubject::where('section_id', $request->section_id)
            ->where('category_id', $request->category_id)
            ->distinct()
            ->pluck('sub_category_id');

        if ($subcategoryIds->isEmpty()) {
            $subcategories = collect();
        } else {
            $subcategories = Subcategory::whereIn('id', $subcategoryIds)
                ->where('status', 1)
                ->get(['id', 'subcategory_name as category_name']); // using aliased name for frontend compatibility
        }

        return response()->json($subcategories);
    }

    public function getFilterSubjects(Request $request)
    {
        $subjectIds = FilterClassSubject::where('section_id', $request->section_id)
            ->where('category_id', $request->category_id)
            ->where('sub_category_id', $request->subcategory_id)
            ->distinct()
            ->pluck('subject_id');

        $subjects = Subject::whereIn('id', $subjectIds)
            ->where('status', 1)
            ->get(['id', 'name as category_name']); // using aliased name for frontend compatibility

        return response()->json($subjects);
    }

    public function searchProducts(Request $request)
    {
        $condition = session('condition', 'new');

        $query = Product::with([
                'publisher',
                'authors',
                'category',
                'attributes.vendor.vendorbusinessdetails',
            ])
            ->whereHas('attributes', function($q) {
                $q->where('status', 1);
            })
            ->where('status', 1);

        /* CONDITION */
        if ($request->filled('condition')) {
            if ($request->condition !== 'all') {
                $query->where('condition', $request->condition);
            }
        } elseif ($condition !== 'all') {
            $query->where('condition', $condition);
        }

        /* LANGUAGE */
        if ($request->filled('language_id') && $request->language_id !== 'all') {
            $query->where('language_id', $request->language_id);
        } elseif (session('language') && session('language') !== 'all') {
            $query->where('language_id', session('language'));
        }

        /* SEARCH */
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('product_isbn', 'like', "%{$search}%")
                    ->orWhereHas(
                        'category',
                        fn($c) =>
                        $c->where('category_name', 'like', "%{$search}%")
                    );
            });
        }

        /* SECTION */
        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        /* CATEGORY */
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        /* SUBCATEGORY (CLASS) */
        if ($request->filled('subcategory_id')) {
            $query->where('subcategory_id', $request->subcategory_id);
        }

        /* SUBJECT */
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        /* DISTANCE RANGE */
        $userLat = session('user_latitude');
        $userLng = session('user_longitude');
        $distance = session('distance', 10);
        if ($userLat && $userLng && $distance < 100) {
            $query->where(function ($subQuery) use ($userLat, $userLng, $distance) {
                $subQuery->whereHas('attributes.vendor.vendorbusinessdetails', function ($q) use ($userLat, $userLng, $distance) {
                    $q->whereNotNull('latitude')
                      ->whereNotNull('longitude')
                      ->whereRaw("(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) <= ?", [$userLat, $userLng, $userLat, $distance]);
                })
                ->orWhereHas('attributes.vendor', function ($q) use ($userLat, $userLng, $distance) {
                    $q->whereDoesntHave('vendorbusinessdetails')
                      ->whereNotNull('location')
                      ->whereRaw("(6371 * acos(cos(radians(?)) * cos(radians(SUBSTRING_INDEX(location, ',', 1))) * cos(radians(SUBSTRING_INDEX(location, ',', -1)) - radians(?)) + sin(radians(?)) * sin(radians(SUBSTRING_INDEX(location, ',', 1))))) <= ?", [$userLat, $userLng, $userLat, $distance]);
                });
            });
        }

        /* FETCH */
        $products = $query->get();

        /* PRICE FILTER (VENDOR DISCOUNT SAFE) */
        if ($request->filled('min_price') || $request->filled('max_price')) {
            $minPriceVal = (float) ($request->min_price ?? 0);
            $maxPriceVal = (float) ($request->max_price ?? PHP_FLOAT_MAX);

            $products = $products->filter(function ($product) use ($minPriceVal, $maxPriceVal) {
                $price = Product::getDiscountPrice($product->id);
                return $price >= $minPriceVal && $price <= $maxPriceVal;
            });
        }

        /* PAGINATION */
        $perPage = 12;
        $page    = request('page', 1);

        $products = new \Illuminate\Pagination\LengthAwarePaginator(
            $products->forPage($page, $perPage),
            $products->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $sections = Section::all();
        $category = Category::limit(10)->get();
        $language = Language::all();
        $logos    = HeaderLogo::first();

        $footerProducts = Product::where('status', 1)
            ->orderByDesc('id')
            ->take(3)
            ->get()
            ->toArray();

        /* LOG SEARCH QUERY */
        if ($request->filled('search')) {
            \App\Models\SearchQuery::create([
                'keyword'       => $request->search,
                'user_id'       => Auth::id(),
                'ip_address'    => $request->ip(),
                'latitude'      => session('user_latitude'),
                'longitude'     => session('user_longitude'),
                'results_count' => $products->total(),
            ]);
        }

        return view(
            'front.products.search',
            compact(
                'products',
                'condition',
                'sections',
                'footerProducts',
                'category',
                'language',
                'logos'
            ),
            [
                'languages'        => Language::all(),
                'selectedLanguage' => Language::find(session('language')),
            ]
        );
    }
}
