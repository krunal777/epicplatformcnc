<?php

namespace App\Queries;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Search\CustomSearchStrategy;
use App\Epic\EpicProduct;
use Illuminate\Support\Collection;

class ProductQuery
{
    protected array $allowedFilters = [
        'brand_name',
        'price_amount',
        'sale_price_amount',
        'price_currency',
        'tags_name',
        'inventory_quantity',
        'inventory_is_available',
        'variants',
        'real_price_amount',
        'category_ids',
        // Add more filters as needed
    ];

    protected array $defaultSorts = [
        'created_at' => 'desc',
    ];

    protected CustomSearchStrategy $searchStrategy;

    public function __construct(CustomSearchStrategy $searchStrategy)
    {
        $this->searchStrategy = $searchStrategy;
    }

    public function filtersQuery(Request $request): Collection
    {
        $searchPhrase = $request->input('phrase'); // Get search phrase from the request
        $includeChildren = strpos($request->input('include', ''), 'children') !== false;
        $yieldAll = $request->input('yield') === 'all';


        if ($searchPhrase) {
            // Construct the aggregation pipeline
            $aggregate = [
                [
                    '$search' => [
                        'index' => 'default',
                        'compound' => [
                            'should' => [
                                [
                                    'autocomplete' => [
                                        'query' => $searchPhrase,
                                        'path' => 'name',
                                    ]
                                ],
                                [
                                    'autocomplete' => [
                                        'query' => $searchPhrase,
                                        'path' => 'sku',
                                    ]
                                ],
                                [
                                    'autocomplete' => [
                                        'query' => $searchPhrase,
                                        'path' => 'brand_name',
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    '$addFields' => [
                        'priority' => [
                            '$switch' => [
                                'branches' => [
                                    [
                                        'case' => ['$gt' => [['$indexOfCP' => ['$name', $searchPhrase]], -1]],
                                        'then' => 1  // Highest priority if found in 'name'
                                    ],
                                    [
                                        'case' => ['$gt' => [['$indexOfCP' => ['$sku', $searchPhrase]], -1]],
                                        'then' => 2  // Medium priority if found in 'sku'
                                    ],
                                    [
                                        'case' => ['$gt' => [['$indexOfCP' => ['$brand_name', $searchPhrase]], -1]],
                                        'then' => 3  // Lowest priority if found in 'brand_name'
                                    ]
                                ],
                                'default' => 4 // Default priority if not found in any
                            ]
                        ]
                    ]
                ]
            ];
        }
        if (!$includeChildren) {
            $aggregate[] = ['$match' => ['parent_id' => ['$exists' => false]]];
        }
        $fields = $request->input('fields');
        if ($fields) {
            $fieldsArray = explode(',', $fields);
            $projectFields = array_fill_keys($fieldsArray, 1); // Project specified fields only
            $projectFields['_id'] = 0; // Optionally exclude _id from the response
            $projectFields['created_at'] = 1;
            // Add the $project stage to the pipeline
            $aggregate[] = ['$project' => $projectFields];
        }
        if ($yieldAll) {
            // Aggregation pipeline for getting unique brands, prices, tags, and variants
            $aggregate = array_merge($aggregate, [
                [
                    '$facet' => [
                        'brands' => [
                            [
                                '$group' => [
                                    '_id' => '$brand_name',
                                    'count' => ['$sum' => 1]
                                ]
                            ],
                            [
                                '$project' => [
                                    '_id' => 0,
                                    'name' => '$_id',
                                    'count' => '$count'
                                ]
                            ]
                        ],
                        'prices' => [
                            [
                                '$bucket' => [
                                    'groupBy' => '$real_price.amount',
                                    'boundaries' => [0, 10000, 25000, 40000, 50000, 63000, 125000, PHP_INT_MAX],  // Define price ranges
                                    'default' => 'Other',
                                    'output' => [
                                        'count' => ['$sum' => 1],
                                        'min' => ['$min' => '$real_price.amount'],
                                        'max' => ['$max' => '$real_price.amount'],
                                    ],
                                ],
                            ]
                        ],
                        'tags' => [
                            [
                                '$unwind' => '$tags'
                            ],
                            [
                                '$group' => [
                                    '_id' => '$tags',
                                    'count' => ['$sum' => 1]
                                ]
                            ],
                            [
                                '$project' => [
                                    '_id' => 0,
                                    'name' => '$_id',
                                    'count' => '$count'
                                ]
                            ]
                        ],
                        'variants' => [
                            [
                                '$addFields' => [
                                    'parsed_variants' => [
                                        '$function' => [
                                            'body' => '
                                                function(variants) {
                                                    try {
                                                        return JSON.parse(variants);
                                                    } catch (e) {
                                                        return null; // Return null for invalid JSON
                                                    }
                                                }',
                                            'args' => ['$ca__product_group__varians_option'],
                                            'lang' => 'js'
                                        ]
                                    ]
                                ]
                            ],
                            [
                                '$unwind' => '$parsed_variants'
                            ],
                            [
                                // Match only valid variants (i.e., where the JSON was parsed successfully)
                                '$match' => [
                                    'parsed_variants' => ['$ne' => null]
                                ]
                            ],
                            [
                                '$group' => [
                                    '_id' => '$parsed_variants.name',
                                    'options' => ['$addToSet' => '$parsed_variants.options'],
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
            $results = EpicProduct::raw(function ($collection) use ($aggregate) {
                return $collection->aggregate($aggregate);
            });

            // Format the results to match the desired response structure
            $data = json_decode(json_encode($results->first() ?: []), true);
            return collect([
                'brands' => $this->formatBrands($data['brands'] ?? []),
                'prices' => $this->formatPrices($data['prices'] ?? []),
                'tags' => $this->formatTags($data['tags'] ?? []),
                'variants' => $this->formatVariants($data['variants'] ?? []),
            ]);
        }
        // Process filter parameters
        $filters = $request->input('filter', []);

        // First, handle the price filter separately
        foreach ($filters as $field => $conditions) {
            $matchConditions = [];
            if (is_array($conditions)) {
                foreach ($conditions as $operator => $value) {
                    $mongoOperator = match ($operator) {
                        'eq' => '$eq',
                        'neq' => '$ne',
                        'gt' => '$gt',
                        'lt' => '$lt',
                        'gte' => '$gte',
                        'lte' => '$lte',
                        'in' => '$in',
                        'nin' => '$nin',
                        default => null
                    };

                    // Handle price range filtering
                    if ($field == 'real_price.amount') {
                        $aggregate[] = [
                            '$match' => [
                                'real_price.amount' => [
                                    '$gte' => (float) ($conditions['gte'] ?? 0),
                                    '$lte' => (float) ($conditions['lte'] ?? PHP_INT_MAX)
                                ]
                            ]
                        ];
                    }
                }
            }
        }
        if (isset($filters['brand.name'])) {
            $brands = explode(',', $filters['brand.name']);
            $aggregate[] = [
                '$match' => [
                    'brand_name' => ['$in' => $brands]
                ]
            ];
        }
        if (isset($filters['product_type'])) {
            $productType = $filters['product_type'];
        
            if ($productType == 'variation') {
                // For 'variation', check if 'children' exists and is not empty
                $aggregate[] = [
                    '$match' => [
                        'children_ids' => [
                            '$exists' => true,
                            '$ne' => [] // Ensure 'children' is not empty
                        ]
                    ]
                ];
            } elseif ($productType == 'bundle') {
                // For 'bundle', check if classification is 'bundle'
                $aggregate[] = [
                    '$match' => [
                        'classification' => 'bundle' // Ensure classification is 'bundle'
                    ]
                ];
            } else {
                // Default case: simple product, no 'children' and classification not 'bundle'
                $aggregate[] = [
                    '$match' => [
                        'children_ids' => [
                            '$exists' => false // No 'children' key for simple products
                        ],
                        'classification' => [
                            '$ne' => 'bundle' // Not classified as 'bundle'
                        ]
                    ]
                ];
            }
        }
        if (isset($filters['name'])) {
            $name = $filters['name'];
            $aggregate[] = [
                '$match' => [
                    'name' => [
                        '$regex' => '.*' . $name . '.*',  // Use regex for partial matching
                        '$options' => 'i'    // Case-insensitive matching
                    ]
                ]
            ];
        }
        if (isset($filters['sku'])) {
            $sku = $filters['sku'];
            $aggregate[] = [
                '$match' => [
                    'sku' => [
                        '$regex' => '.*' . $sku . '.*',  // Use regex for partial matching
                        '$options' => 'i'    // Case-insensitive matching
                    ]
                ]
            ];
        }
        if (isset($filters['brand'])) {
            $brand = $filters['brand'];
            $aggregate[] = [
                '$match' => [
                    'brand_name' => [
                        '$regex' => '.*' . $brand . '.*',  // Use regex for partial matching
                        '$options' => 'i'    // Case-insensitive matching
                    ]
                ]
            ];
        }
        // Next, handle variant filtering (parsing JSON stored in ca__product_group__varians_option)
        if (isset($filters['variants'])) {
            $variantFilters = $filters['variants'];

            $aggregate[] = [
                '$addFields' => [
                    'parsed_variants' => [
                        '$function' => [
                            'body' => 'function(variants) { return JSON.parse(variants); }',
                            'args' => ['$ca__product_group__varians_option'],
                            'lang' => 'js'
                        ]
                    ]
                ]
            ];
            $orConditions = [];
            foreach ($variantFilters as $variantKey => $variantValue) {
                $brand_ary = explode(',', $variantValue);
                $orConditions[] = [
                    'parsed_variants' => [
                        '$elemMatch' => [
                            'name' => $variantKey,  // e.g., Weight, Finish, Storage
                            'options.ca__product_group__variant_label' => ['$in' => $brand_ary]
                        ]
                    ]
                ];
            }
            $aggregate[] = [
                '$match' => [
                    '$or' => $orConditions
                ]
            ];
        }

        // Add sorting by textScore and then by the priority field
        $sortField = $request->input('sort');

        // Default sorting
        $aggregate[] = [
            '$sort' => [
                'created_at' => -1  // Include the field to verify its existence
            ]
        ];
        if ($searchPhrase) {
            $defaultSort = [
                'priority' => 1,  // First by priority (lowest number is higher priority)
                'score' => ['$meta' => 'textScore'],  // Then by textScore (for relevance)
                'name_priority' => 1,  // Sort by presence in name
                'sku_priority' => 1,  // Sort by presence in SKU
            ];
            $aggregate[] = [
                '$sort' => $defaultSort
            ];
        }
        
        // Check if 'sort' is present in the request
        // if ($sortField) {
        //     // Determine if sorting is ascending or descending
        //     $sortDirection = 1; // Default to ascending
        //     if (strpos($sortField, '-') === 0) {
        //         // If '-' is present, set sort direction to descending
        //         $sortField = ltrim($sortField, '-'); // Remove '-' from field name
        //         $sortDirection = -1; // Descending order
        //     }

        //     // Add custom sort field to the aggregate pipeline
        //     $aggregate[] = [
        //         '$sort' => array_merge($defaultSort, [$sortField => $sortDirection])
        //     ];
        // }

        // Handle pagination: perPage and page parameters
        $perPage = (int) $request->input('perPage', 20); // Default to 20 per page
        $page = (int) $request->input('page', 1); // Default to page 1

        $skip = ($page - 1) * $perPage;

        // $aggregate[] = ['$skip' => $skip]; // Skip records for pagination
        // $aggregate[] = ['$limit' => $perPage]; // Limit the number of records per page
        // Execute the aggregation pipeline
        $results = EpicProduct::raw(value: function ($collection) use ($aggregate) {
            return $collection->aggregate($aggregate);
        });

        return collect($results);
    }
    private function formatBrands(array $brands): array
    {
        return array_values(array_filter(array_map(function ($brand) {
            // Ensure 'name' is directly accessible and 'name' is not empty
            if (!empty($brand) && array_key_exists('name', $brand) && $brand['name'] != '') {
                return [
                    'name' => $brand['name'] ?? '', // Ensure 'name' is directly accessible
                    'count' => $brand['count'] ?? 0
                ];
            }
            return null; // Return null for entries to be filtered out
        }, $brands)));
    }

    private function formatPrices(array $prices): array
    {
        // Sort prices by start value
        usort($prices, function ($a, $b) {
            return $a['min'] <=> $b['min'];
        });

        // Adjust the start of each bucket to close the gap
        $formattedPrices = [];
        $previousEnd = null;

        foreach ($prices as $price) {
            $start = $previousEnd !== null ? $previousEnd + 100 : $price['min'];
            $end = $price['max'];
            $count = $price['count'];

            $formattedPrices[] = [
                'start' => $start,
                'end' => $end,
                'count' => $count,
            ];

            $previousEnd = $end;
        }

        return $formattedPrices;
    }

    // private function formatPrices(array $prices): array
    // {
    //     return array_map(function ($price) {
    //         return [
    //             'start' => $price['min'],
    //             'end' => $price['max'],
    //             'count' => $price['count'],
    //         ];
    //     }, $prices);
    // }

    private function formatTags(array $tags): array
    {
        return array_map(function ($tag) {
            return [
                'name' => $tag,
                'count' => 0 // Placeholder for actual count logic
            ];
        }, $tags);
    }

    function formatVariants($responses)
    {
        $formattedVariants = [];

        // Extract the variants from the response
        if (!empty($responses)) {
            foreach ($responses as $response) {
                $variantName = $response['_id'] ?? '';  // Variant name, like "Color", "Finish", etc.
                $options = [];

                if (isset($response['options']) && is_array($response['options'])) {
                    // Loop through all options for this variant
                    foreach ($response['options'] as $optionGroup) {
                        foreach ($optionGroup as $option) {
                            if (isset($option['ca__product_group__variant_label'])) {
                                $optionLabel = $option['ca__product_group__variant_label'];

                                // Check if the option label already exists, if so increment its count
                                if (!isset($options[$optionLabel])) {
                                    $options[$optionLabel] = 0;
                                }
                                $options[$optionLabel]++;
                            }
                        }
                    }
                }

                // Prepare the result with counts
                $formattedOptions = [];
                foreach ($options as $label => $count) {
                    $formattedOptions[] = [
                        'value' => $label,
                        'count' => $count
                    ];
                }

                // Add the formatted variant group to the result array
                $formattedVariants[] = [
                    'name' => $variantName,
                    'options' => $formattedOptions
                ];
            }
        }

        return $formattedVariants;
    }
    public function getSearchableFields(): array
    {
        return [
            'name', // Example searchable fields
            'description',
            // Add more fields as necessary
        ];
    }

    public function getFilteredSearch(Request $request): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = $this->filtersQuery($request);

        if ($query instanceof Collection) {
            if (!$request->has('sort')) {
                // You might need to write a different sorting mechanism for collections
                $query = $query->sortByDesc('score'); // Example sorting, adjust as needed
            }

            return new LengthAwarePaginator(
                $query->forPage($request->input('page', 1), $request->input('perPage', 15)),
                $query->count(),
                $request->input('perPage', 15),
                $request->input('page', 1)
            );
        }

        // If it's a builder, apply sort and return the paginated results
        if ($query instanceof Builder) {
            if (!$request->has('sort')) {
                $this->searchStrategy->applyScoreSort($query);
            }

            return $query->paginate($request->input('perPage', 15));
        }

        throw new RuntimeException('Unexpected query result type');
    }
}


