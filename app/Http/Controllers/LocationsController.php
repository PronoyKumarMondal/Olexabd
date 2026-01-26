<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocationsController extends Controller
{
    public function getDivisions()
    {
        return response()->json(DB::table('divisions')->select('id', 'name', 'bn_name')->get());
    }

    public function getDistricts($division_id)
    {
        return response()->json(DB::table('districts')->where('division_id', $division_id)->select('id', 'name', 'bn_name')->get());
    }

    public function getUpazilas($district_id)
    {
        return response()->json(DB::table('upazilas')->where('district_id', $district_id)->select('id', 'name', 'bn_name', 'is_inside_dhaka')->get());
    }

    public function getPostcode($upazila_id)
    {
        // 1. Get Upazila Name from ID
        $upazila = DB::table('upazilas')->where('id', $upazila_id)->first();
        
        if (!$upazila) return response()->json(['postCode' => '']);

        // 2. Search Postcodes table matching the name
        // Note: Postcodes table has 'upazila' as string.
        // We try to match exact name first.
        $postcode = DB::table('postcodes')
            ->where('upazila', $upazila->name)
            ->first();

        // Fallback: If no match by name, maybe try to match 'district_id' and get first?
        // But the JSON data structure was loose.
        
        if ($postcode) {
             return response()->json(['postCode' => $postcode->postCode]);
        }
        
        return response()->json(['postCode' => '']); 
    }
}
