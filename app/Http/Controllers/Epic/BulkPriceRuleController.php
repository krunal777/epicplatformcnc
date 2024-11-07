<?php
namespace App\Http\Controllers\Epic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Epic\BulkPriceRule; // Import the BulkPriceRule model
use Illuminate\Pagination\LengthAwarePaginator;

class BulkPriceRuleController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'priceStatus' => 'required|string',
            'price' => 'required|integer',
            'effective' => 'required|string',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Create a new bulk price rule
        $bulkPriceRule = BulkPriceRule::create([
            'title' => $request->input('title'),
            'priceStatus' => $request->input('priceStatus'),
            'author' => $request->input('author'),
            'price' => $request->input('price'),
            'brands' => $request->input('brands'),
            'categories' => $request->input('categories'),
            'ProductsSKU' => $request->input('ProductsSKU'),
            'effective' => $request->input('effective'),
            'startDate' => $request->input('startDate'),
            'endDate' => $request->input('endDate'),
            'authorModifies' => $request->input('authorModifies'),
        ]);

        return response()->json(['success' => true, 'data' => $bulkPriceRule]);
    }
    public function update(Request $request, $id)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'priceStatus' => 'required|string',
            'price' => 'required|integer',
            'effective' => 'required|string',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $bulkPriceRule = BulkPriceRule::find($id);
        if (!$bulkPriceRule) {
            return response()->json([
                'success' => false,
                'message' => 'Bulk price rule not found'
            ], 404);
        }
        // update a  bulk price rule
        $bulkPriceRule->update([
            'title' => $request->input('title'),
            'priceStatus' => $request->input('priceStatus'),
            'author' => $request->input('author'),
            'price' => $request->input('price'),
            'brands' => $request->input('brands'),
            'categories' => $request->input('categories'),
            'ProductsSKU' => $request->input('ProductsSKU'),
            'effective' => $request->input('effective'),
            'startDate' => $request->input('startDate'),
            'endDate' => $request->input('endDate'),
            'authorModifies' => $request->input('authorModifies'),
        ]);

        return response()->json(['success' => true, 'data' => $bulkPriceRule]);
    }

    public function destroy($id)
    {
        $bulkPriceRule = BulkPriceRule::find($id);

        // Check if the rule exists
        if (!$bulkPriceRule) {
            return response()->json([
                'success' => false,
                'message' => 'Bulk price rule not found',
            ], 404); // Return 404 if the rule doesn't exist
        }

        // Attempt to delete the rule
        $bulkPriceRule->delete();

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'Bulk price rule deleted successfully',
        ], 200); // Return 200 OK on success
    }

    public function getList(Request $request)
    {
        // Get the pagination parameters from the request, or use default values
        $perPage = $request->input('per_page', 10); // Items per page (default 10)
        $page = $request->input('page', 1); // Current page (default 1)
        
        // Fetch bulk price rules from the database (adjust the query as needed)
        $query = BulkPriceRule::query(); // Adjust this query as needed
        
        // Count total records for pagination
        $total = $query->count();
        
        // Apply pagination (skip and limit) to the query
        $bulkPriceRules = $query
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get(); // Fetch paginated results

        // Define the path for pagination
        $path = $request->url(); // Get the current URL as the path

        // Create a new LengthAwarePaginator instance
        $paginator = new LengthAwarePaginator(
            $bulkPriceRules, // Items for the current page
            $total,          // Total items
            $perPage,        // Items per page
            $page,           // Current page
            ['path' => $path] // Path for pagination links
        );

        // Return paginated response in JSON format
        return response()->json($paginator);
    }

    public function getDetail($id){
        $bulkPriceRule = BulkPriceRule::find($id);
        if (!$bulkPriceRule) {
            return response()->json([
                'success' => false,
                'message' => 'Bulk price rule not found',
            ], 404); // Return 404 if the rule doesn't exist
        }
        return response()->json([
            'success' => true,
            'data' => $bulkPriceRule,
        ], 200);

    }
}
