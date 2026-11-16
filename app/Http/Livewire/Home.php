<?php

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\Product;
use App\Services\AprioriRecommendationService;
use Illuminate\Http\Request;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class Home extends Component
{
    use WithPagination;

    protected $recommendationService;

    public function __construct()
    {
        parent::__construct();
        $this->recommendationService = app(AprioriRecommendationService::class);
    }

    private function getProducts($sort, $selectedCategory, $search, $priceFilter)
    {
        $query = Product::query();

        if ($selectedCategory) {
            $query->whereHas('categories', function ($query) use ($selectedCategory) {
                $query->where('categories.slug', $selectedCategory);
            });
        }

        if ($priceFilter) {
            preg_match('/\$(\d+)\s+-\s+\$(\d+)/', $priceFilter, $matches);
            if (count($matches) == 3) {
                $minPrice = $matches[1];
                $maxPrice = $matches[2];
                $query->whereBetween('price', [$minPrice, $maxPrice]);
            }
        }

        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                      ->orWhere('description', 'like', '%' . $search . '%')
                      ->orWhereHas('categories', function ($query) use ($search) {
                          $query->where('categories.name', 'like', '%' . $search . '%');
                      });
            });
        }

        if ($sort === 'latest') {
            $query->orderBy('created_at', 'DESC');
        } elseif ($sort === 'low-to-high') {
            $query->orderBy('price', 'ASC');
        } elseif ($sort === 'high-to-low') {
            $query->orderBy('price', 'DESC');
        }

        return $query->paginate(12)->appends([
            'sort' => $sort,
            'category' => $selectedCategory,
            'range' => $priceFilter,
            'search' => $search
        ]);
    }

    private function getPopularProducts($limit = 4)
    {
        return Cache::remember('popular_products', now()->addHours(24), function () use ($limit) {
            return Product::withCount(['orderItems' => function ($query) {
                $query->where('created_at', '>=', now()->subMonths(3));
            }])->orderBy('order_items_count', 'DESC')->take($limit)->get();
        });
    }

    public function render(Request $request)
    {
        $sort = $request->input('sort');
        $selectedCategory = $request->input('category');
        $search = $request->input('search');
        $priceFilter = $request->input('range');
        $products = $this->getProducts($sort, $selectedCategory, $search, $priceFilter);
        $categories = Category::all();

        $recommendedProducts = collect([]);

        if (auth()->check()) {
            try {
                $user = auth()->user();
                $recentProductIds = \App\Models\OrderItem::whereHas('order', function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                          ->where('created_at', '>=', now()->subMonths(3));
                })->latest()->take(3)->pluck('product_id')->unique()->toArray();

                Log::info("Recent product IDs for user {$user->id}: ", $recentProductIds);

                if (empty($recentProductIds)) {
                    Log::warning("No recent products for user {$user->id}, falling back to popular products.");
                    $recommendedProducts = $this->getPopularProducts(4);
                } else {
                    $recommendedProductIds = [];
                    foreach ($recentProductIds as $productId) {
                        if (Product::where('id', $productId)->exists()) {
                            $cachedRecommendations = Cache::remember(
                                "recommendations_user_{$user->id}_product_{$productId}",
                                now()->addHours(1),
                                function () use ($productId, $user) {
                                    $recommendations = $this->recommendationService->getRecommendations($productId, $user->id);
                                    Log::info("Apriori recommendations for user {$user->id}, product {$productId}: ", $recommendations);
                                    return $recommendations;
                                }
                            );
                            $recommendedProductIds = array_merge($recommendedProductIds, $cachedRecommendations);
                        }
                    }

                    $recommendedProductIds = array_unique($recommendedProductIds);
                    Log::info("Merged recommended product IDs for user {$user->id}: ", $recommendedProductIds);

                    $recommendedProducts = Product::whereIn('id', $recommendedProductIds)
                        ->with('categories')
                        ->take(4)
                        ->get();

                    if ($recommendedProducts->isEmpty()) {
                        Log::warning("No recommendations for user {$user->id}, falling back to popular products.");
                        $recommendedProducts = $this->getPopularProducts(4);
                    }
                }

                Log::info("Recommended products for user {$user->id}: ", $recommendedProducts->pluck('id')->toArray());

            } catch (\Exception $e) {
                Log::error("Failed to fetch recommendations for user {$user->id}: " . $e->getMessage() . ". Stack trace: " . $e->getTraceAsString());
                $recommendedProducts = $this->getPopularProducts(4);
            }
        } else {
            $recommendedProducts = $this->getPopularProducts(4);
        }

        return view('livewire.home', [
            'products' => $products,
            'sort' => $sort ?? 'default',
            'selectedCategory' => $selectedCategory,
            'categories' => $categories,
            'recommendedProducts' => $recommendedProducts,
        ]);
    }
}