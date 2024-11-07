<?php
namespace App\Http\Controllers\Epic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Epic\DdiTax;
use Illuminate\Support\Facades\Http;

class DdiTaxController extends Controller
{
    public function getDDITaxrateZipcode(Request $request){
        //validate request
        $validator = Validator::make($request->all(), [
            'zipcode' => 'required',
            'city' => 'required'
        ]);
    
        if ($validator->fails()) {
            // Custom error response for validation failure
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        $zipcode = $request->input("zipcode");
        $city = $request->input("city");
        $result = DdiTax::where('ZipCode',(int)$zipcode)->where('City', 'regex', new \MongoDB\BSON\Regex('^' . $city . '$', 'i'))->first();
        if($result){
            return response()->json(['success' => true, 'data' => $result]) ;
        }else{
            return response()->json(['success' => false, 'message' => 'Zipcode not found'], 404);
        }
    }

    public function ddiValidateUser(Request $request){
        $posts = $request->all();
        $apiUrl = 'https://api.qualifiedhardware.com:4443/restapi.svc/datasync/EcommPro_ValidateUser';

   
        // Make the POST request using Laravel's Http client
        $response = Http::post($apiUrl, $posts);

        // Check if the request was successful
        if ($response->successful()) {
            // Handle successful response
            return response()->json([
                'success' => true,
                'data' => $response->json(),
            ]);
        } else {
            // Handle error response
            return response()->json([
                'success' => false,
                'message' => 'Failed to call external API',
                'error' => $response->body(),
            ], $response->status());
        }
    }
    public function ddiSubmitOrder(Request $request){
        $posts = $request->all();
        $apiUrl = 'https://api.qualifiedhardware.com:4443/restapi.svc/datasync/EcommPro_SubmitOrder';

   
        // Make the POST request using Laravel's Http client
        $response = Http::post($apiUrl, $posts);

        // Check if the request was successful
        if ($response->successful()) {
            // Handle successful response
            return response()->json([
                'success' => true,
                'data' => $response->json(),
            ]);
        } else {
            // Handle error response
            return response()->json([
                'success' => false,
                'message' => 'Failed to call external API',
                'error' => $response->body(),
            ], $response->status());
        }
    }
    public function ddiFetchPrice(Request $request){
        $posts = $request->all();
        $apiUrl = 'https://api.qualifiedhardware.com:4443/restapi.svc/datasync/EcommPro_PriceStock';

   
        // Make the POST request using Laravel's Http client
        $response = Http::post($apiUrl, $posts);

        // Check if the request was successful
        if ($response->successful()) {
            // Handle successful response
            return response()->json([
                'success' => true,
                'data' => $response->json(),
            ]);
        } else {
            // Handle error response
            return response()->json([
                'success' => false,
                'message' => 'Failed to call external API',
                'error' => $response->body(),
            ], $response->status());
        }
    }
    public function shippoGetMethod(Request $request){
        $url = $request->input('url');
        $token = $request->input('token');
        $response = Http::withHeaders([
            'Authorization' => $token,  // Assuming it's a Bearer token
        ])->get($url);
    
        // Check the response and return
        if ($response->successful()) {
            return response()->json($response->json());
        } else {
            return response()->json(['error' => 'Request failed'], 500);
        }
    }
}
