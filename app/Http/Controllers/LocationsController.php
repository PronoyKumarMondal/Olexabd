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
}
