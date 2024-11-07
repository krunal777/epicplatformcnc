<?php
namespace App\Http\Controllers\Epic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Queries\ProductQuery;
use App\Search\CustomSearchStrategy;

class EpicSearchController extends Controller
{
    protected ProductQuery $productQuery;

    public function __construct(ProductQuery $productQuery)
    {
        $searchStrategy = new CustomSearchStrategy();
        $this->productQuery = new ProductQuery($searchStrategy);
    }

    public function searchProducts(Request $request): LengthAwarePaginator
    {
        return $this->productQuery->getFilteredSearch($request);
    } 
}
