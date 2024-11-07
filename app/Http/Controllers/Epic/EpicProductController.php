<?php

namespace App\Http\Controllers\Epic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Epic\EpicProduct;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;
use Illuminate\Pagination\LengthAwarePaginator;

class EpicProductController extends Controller
{
    public function __construct()
    {
    }
    public function get_simple_products_list(Request $request){
        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 25);
        $filter = $request->input('filter', '');
        // Query to filter products
        $query = EpicProduct::where('classification', 'common')
        ->whereNull('parent_id')->whereNull('children');
        if (!empty($filter['name'])) {
            $query->where('name', 'like', '%' . $filter['name'] . '%');
        }
            $total = $query->count();

            // Get the products for the current page
            $products = $query->forPage($page, $perPage)->get();
        
            // Get the current path
            $path = $request->url();
        
            // Create the pagination links
            $paginator = new LengthAwarePaginator(
                $products,
                $total,
                $perPage,
                $page,
                ['path' => $path]
            );
        
            // Format the response
            $response = [
                'data' => $paginator->items(),
                'links' => [
                    'first' => $paginator->url(1),
                    'last' => $paginator->url($paginator->lastPage()),
                    'prev' => $paginator->previousPageUrl(),
                    'next' => $paginator->nextPageUrl(),
                ],
                'meta' => [
                    'current_page' => $paginator->currentPage(),
                    'from' => $paginator->firstItem(),
                    'last_page' => $paginator->lastPage(),
                    'path' => $path,
                    'per_page' => $paginator->perPage(),
                    'to' => $paginator->lastItem(),
                    'total' => $total,
                ],
            ];
        
            return response()->json($response);
    }

    public function getSaleProducts(){
        $products = EpicProduct::whereNotNull('sale_price')
            ->orderBy('updated_at', 'desc')  // Order by the latest update
            ->limit(5)
            ->get();  // Select specific fields for the response

        return response()->json($products);
    }
    public function update(Request $request, $productId){
        $rules = [
            'slug' => 'required|unique:products,slug,' . $productId,
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            // Return JSON response with validation errors
            return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }
        $product = EpicProduct::where('id', $productId)->first(); 
        $slug = $request->input('slug');
        if ($product && $slug) {
            $product->slug = $slug; // Update the slug to the desired value
            $product->save(); // Save the changes to the database
            return response()->json(['success' => true, 'product' => $product], 200);
        }
        return response()->json(['success' => false, 'message' => 'Product or slug not provided or not found'], 404);
    }
    public function update_variants(Request $request, $productId){
        $request->validate([
            'variants' => 'required|array',
            'variants.*.key' => 'required|string',
            'variants.*.name' => 'required|string',
            'variants.*.options' => 'required|array',
            // Add more validation rules as needed
        ]);
        $variants = $request->input('variants');
        
        $translations = $request->input('translations');
        $product = EpicProduct::where('id', $productId)->first(); 
        EpicProduct::where('parent_id', $productId)->delete();
        if(!empty($product['children_ids'])){
            $product->children_ids = [];
            $product->save();
        }
        if(!empty($product['children'])){
            $product->children = [];
            $product->save();
        }
        $combinations = $this->generateCombinations($variants);
        $childProducts = [];
        $main_price = $product->price['amount']/100; 
        
        $savedchildren=[];
        $currency = $product->price['currency'];
        foreach ($combinations as $combination) {
            $childProduct = $product->replicate();
            $childProduct->id = (string) Uuid::uuid4();
            $childProduct->name = $product->name . ' ' . implode(' ', array_column($combination, 'label'));
            $childProduct->sku = $product->sku . '-' . implode('-', array_column($combination, 'label'));
            $childProduct->slug = Str::slug($childProduct->name);
            $combinationPrice = $main_price + collect($combination)->sum('price');
            $childProduct->price = ['amount' => $combinationPrice * 100,'currency'=>$currency];
            $childProduct->gross_price = ['amount' => $combinationPrice * 100,'currency'=>$currency];
            $childProduct->real_price = ['amount' => $combinationPrice * 100,'currency'=>$currency];
            if($product->sale_price){
                $sale_price = $product->sale_price['amount'];
                if($sale_price > 0){
                    $child_sale_price = $product->sale_price['amount']/100; 
                    $child_combinationPrice = $child_sale_price + collect($combination)->sum('price');
                    $childProduct->sale_price = ['amount' => $child_combinationPrice * 100,'currency'=>$currency];
                }
                else{
                    $childProduct->sale_price = null;
                }
            }
            
            $childProduct->gross_sale_price = null;
            // $childProduct->properties = $combination;
            $childProduct->ca__product_group__product_property = $combination;
            $childProduct->attributes = $combination; // Store attributes for reference
            $childProduct->parent_id = $product->id; // Store attributes for reference
            $childProduct->save();
            if ($childProduct) {
                $childProductArray = $childProduct->toArray();
            } else {
                $childProductArray = []; // Handle the case where no product is found
            } 
            $childrenIds[] = $childProduct->id;
            $childProducts[] = $childProductArray;
        }
        $product->children = $childProducts;
        $product->children_ids = $childrenIds;
        // Save parent product with updated children information
        $product->save();
        return response()->json(['success' => true, 'data' => $product], 200);
    }
    private function generateCombinations($variants)
    {
        $combinations = [[]];
        foreach ($variants as $variant) {
            $options = $variant['options'];
            $newCombinations = [];
            foreach ($combinations as $combination) {
                foreach ($options as $option) {
                    $newCombination = array_merge($combination, [[
                        'name' => $variant['name'],
                        'label' => $option['ca__product_group__variant_label'],
                        'price' => $option['ca__product_group__variant_price'],
                    ]]);
                    $newCombinations[] = $newCombination;
                }
            }
            $combinations = $newCombinations;
        }
        return $combinations;
    }
    public function getBrandCategoriesList(Request $request, $brandId)
    {
        $categories = EpicProduct::withoutGlobalScope(EpicOrganisationStoreScope::class)
            ->raw(function($collection) use ($brandId) {
                return $collection->aggregate([
                    [
                        '$match' => ['brand_id' => $brandId] // Match by brand_id
                    ],
                    [
                        '$unwind' => '$category_ids' // Unwind category_ids array
                    ],
                    [
                        '$group' => [
                            '_id' => '$category_ids' // Group by category_ids (UUIDs as strings)
                        ]
                    ],
                    [
                        '$lookup' => [
                            'from' => 'categories', // Assuming categories collection
                            'localField' => '_id',
                            'foreignField' => 'id', // Match with UUIDs in the 'id' field of categories
                            'as' => 'category_info'
                        ]
                    ],
                    [
                        '$unwind' => '$category_info' // Unwind the category_info array
                    ],
                    [
                        '$project' => [
                            'category_id' => '$_id',
                            'category_name' => '$category_info.name' // Return only category name
                        ]
                    ]
                ]);
            });

        // Return categories or an appropriate response
        if (empty($categories)) {
            return response()->json(['message' => 'No categories found for this brand'], 404);
        }

        return response()->json($categories);
    }
   
    public function getProductsByBrandAndCategories(Request $request, $brandId)
    {
        $categoryIds = $request->input('category_ids', ''); // Default to an empty string if not provided

        // If category_ids are provided, split the comma-separated values into an array
        if (!empty($categoryIds)) {
            $categoryIds = explode(',', $categoryIds); // Split by comma to create an array
        } else {
            $categoryIds = []; // Empty array if no category_ids are provided
        }

        $perPage = $request->input('per_page', 9); // Number of products per page (default 10)
        $currentPage = $request->input('page', 1);  // Current page (default 1)

        // Build the MongoDB aggregation pipeline
        $products = EpicProduct::withoutGlobalScope(EpicOrganisationStoreScope::class)
            ->raw(function ($collection) use ($brandId, $categoryIds) {

                // Start the aggregation pipeline
                $pipeline = [];

                // Always match by brand_id
                $pipeline[] = [
                    '$match' => ['brand_id' => $brandId]
                ];

                // If category_ids are provided, filter by those category_ids
                if (!empty($categoryIds)) {
                    $pipeline[] = [
                        '$match' => ['category_ids' => ['$in' => $categoryIds]]
                    ];
                }

                // Unwind and group products by category_ids
                $pipeline[] = [
                    '$unwind' => '$category_ids'
                ];

                // Filter out categories that do not match the given category_ids
                if (!empty($categoryIds)) {
                    $pipeline[] = [
                        '$match' => ['category_ids' => ['$in' => $categoryIds]]
                    ];
                }

                $pipeline[] = [
                    '$group' => [
                        '_id' => '$category_ids',
                        'products' => [
                            '$push' => [
                                'id' => '$id',
                                'name' => '$name',
                                'sku' => '$sku',
                                'slug' => '$slug',
                                'price' => '$price',
                                'image_url' => '$image_url',
                                'ca__group_descriptions__question_answers' => ['$ifNull' => ['$ca__group_descriptions__question_answers', null]]
                            ]
                        ]
                    ]
                ];

                // Lookup category details
                $pipeline[] = [
                    '$lookup' => [
                        'from' => 'categories',
                        'localField' => '_id',
                        'foreignField' => 'id',
                        'as' => 'category_info'
                    ]
                ];

                $pipeline[] = [
                    '$unwind' => '$category_info'
                ];

                $pipeline[] = [
                    '$project' => [
                        'category_id' => '$_id',
                        'category_name' => '$category_info.name',
                        'products' => 1
                    ]
                ];

                // Execute aggregation pipeline
                return $collection->aggregate($pipeline);
            });

        // Prepare paginated response
        $paginatedCategories = $products->map(function($category) use ($currentPage, $perPage) {
            // Extract products for this category
            $products = collect($category['products']);

            // Paginate products
            $paginatedProducts = new LengthAwarePaginator(
                $products->forPage($currentPage, $perPage)->values(),  // Products for the current page
                $products->count(),                                   // Total number of products
                $perPage,                                             // Number of products per page
                $currentPage                                          // Current page
            );

            // Return the paginated category
            return [
                'category_id' => $category['category_id'],
                'category_name' => $category['category_name'],
                'products' => $paginatedProducts->items(),  // Products on the current page
                'pagination' => [
                    'total' => $paginatedProducts->total(),
                    'per_page' => $paginatedProducts->perPage(),
                    'current_page' => $paginatedProducts->currentPage(),
                    'last_page' => $paginatedProducts->lastPage(),
                    'from' => $paginatedProducts->firstItem(),
                    'to' => $paginatedProducts->lastItem()
                ]
            ];
        });

        // Return the paginated categories with products
        return response()->json($paginatedCategories->toArray());
    }


}




