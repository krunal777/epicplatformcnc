<?php

namespace App\Http\Controllers\Epic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Epic\EpicUsers;
use Illuminate\Support\Collection;
use Futureecom\Foundation\Tenancy\StoreRepository;
use Futureecom\Utils\Tenancy\Organisation;
use Futureecom\Utils\SystemSettings\Facades\Settings;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class EpicUsersController extends Controller
{
    public function __construct()
    {
        
    }
    function getTaxExemption(Request $request){
        $rules = [
            'email' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }
        $email = $request->input('email');
        $user = EpicUsers::where('email', $email)->first();
        return response()->json($user);
    }
    function storeTaxExemption(Request $request){
        $rules = [
            'email' => 'required',
            'TaxExemptionID' => 'required',
            'TaxExemptionImageURL' => 'required',
            'TaxExemptionStatus' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            // Return JSON response with validation errors
            return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }
        $email = $request->input('email');
        $TaxExemptionID = $request->input('TaxExemptionID');
        $TaxExemptionImageURL = $request->input('TaxExemptionImageURL');
        $TaxExemptionStatus = $request->input('TaxExemptionStatus');
        $TaxExemptionNote = $request->input('TaxExemptionNote');
        $user = EpicUsers::where('email', $email)->first();

        // Check if user exists
        if ($user) {
            // Update the fields
            $user->TaxExemptionID = $TaxExemptionID;
            $user->TaxExemptionImageURL = $TaxExemptionImageURL;
            $user->TaxExemptionStatus = $TaxExemptionStatus;
            $user->TaxExemptionNote = $TaxExemptionNote;
            $user->save();
            return response()->json(['message' => 'User updated successfully'], 200);
        } else {
            // Return an error response if user not found
            return response()->json(['message' => 'User not found'], 404);
        }

    }
}