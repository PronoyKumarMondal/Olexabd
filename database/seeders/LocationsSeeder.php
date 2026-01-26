<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class LocationsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Divisions
        $divisions = json_decode(File::get('D:/Personal/Ecommerce appliance/Location json/bd-divisions.json'), true)['divisions'];
        foreach ($divisions as $div) {
            DB::table('divisions')->updateOrInsert(
                ['id' => $div['id']],
                [
                    'name' => $div['name'],
                    'bn_name' => $div['bn_name'],
                    'url' => $div['url'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
        $this->command->info('Divisions seeded!');

        // 2. Districts
        $districts = json_decode(File::get('D:/Personal/Ecommerce appliance/Location json/bd-districts.json'), true)['districts'];
        foreach ($districts as $dist) {
            DB::table('districts')->updateOrInsert(
                ['id' => $dist['id']],
                [
                    'division_id' => $dist['division_id'],
                    'name' => $dist['name'],
                    'bn_name' => $dist['bn_name'],
                    'lat' => $dist['lat'] ?? null,
                    'long' => $dist['long'] ?? null,
                    'url' => $dist['url'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
        $this->command->info('Districts seeded!');

        // 3. Upazilas (Standard)
        $upazilas = json_decode(File::get('D:/Personal/Ecommerce appliance/Location json/bd-upazilas.json'), true)['upazilas'];
        foreach ($upazilas as $up) {
            DB::table('upazilas')->updateOrInsert(
                ['id' => $up['id']],
                [
                    'district_id' => $up['district_id'],
                    'name' => $up['name'],
                    'bn_name' => $up['bn_name'],
                    'url' => $up['url'] ?? null,
                    'is_inside_dhaka' => false, // Default standard upazilas are NOT "Inside City" for charge purposes usually (e.g. Savar)
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
        $this->command->info('Upazilas seeded!');

        // 4. Dhaka City Locations (City Corporations) -> Inside Dhaka Rate
        $dhakaCity = json_decode(File::get('D:/Personal/Ecommerce appliance/Location json/dhaka-city.json'), true)['dhaka'];
        $dhakaStartId = 5000;
        foreach ($dhakaCity as $area) {
            DB::table('upazilas')->insertOrIgnore([
                'id' => $dhakaStartId++, // ID starting from 5000
                'district_id' => $area['district_id'], // 1
                'name' => $area['name'] . ' (' . $area['city_corporation'] . ')',
                'bn_name' => $area['bn_name'],
                'is_inside_dhaka' => true, // THESE are the ones getting 60tk rate
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->command->info('Dhaka City seeded!');


        // 5. Postcodes
        $postcodes = json_decode(File::get('D:/Personal/Ecommerce appliance/Location json/bd-postcodes.json'), true)['postcodes'];
        foreach ($postcodes as $pc) {
            DB::table('postcodes')->insertOrIgnore([
                'division_id' => $pc['division_id'],
                'district_id' => $pc['district_id'],
                'upazila' => $pc['upazila'], // Storing Name
                'postOffice' => $pc['postOffice'],
                'postCode' => $pc['postCode'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->command->info('Postcodes seeded!');
    }
}
