<?php
namespace App\Http\Controllers\Epic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Futureecom\Cart\App\Queries\CartQuery;
use App\Epic\EpicCarts;
class EpicCartController extends Controller
{
    public function __construct(CartQuery $cartQuery)
    {
        $this->cartQuery = $cartQuery;
    }
    public function getCartData(){
        $cartId = session('cart_id'); // Assuming 'cart_id' is the key where the cart ID is stored
    
        if (!$cartId) {
            // Handle the case where the cart ID is not found
            return response()->json(['error' => 'Cart ID not found'], 404);
        }
    
        $cartData = $this->cartQuery->get($cartId);
    
        echo '<pre>';
        print_r($cartData);
    }
    public function updateCartSummary(Request $request){
        $rules = [
            'cart_id' => 'required',
            'summary' => 'required|array',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }
        $cart_id = $request->input('cart_id');
        $summary = $request->input('summary');
        $shipping = $request->input('shipping');
        $cart = EpicCarts::where('id', $cart_id)->first();
        if ($cart) {
            $cart->summary = $summary;
            if(is_array($shipping)){
                $cart->shipping = $shipping;
            }
            $cart->save();
            return response()->json(['message' => 'Cart updated successfully'], 200);
        }
        else{
            return response()->json(['message' => 'Cart not found'], 404);
        }
    }
}



