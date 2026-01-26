<?php

$divisions = json_decode(file_get_contents('D:/Personal/Ecommerce appliance/Location json/bd-divisions.json'), true)['divisions'];
$districts = json_decode(file_get_contents('D:/Personal/Ecommerce appliance/Location json/bd-districts.json'), true)['districts'];
$upazilas = json_decode(file_get_contents('D:/Personal/Ecommerce appliance/Location json/bd-upazilas.json'), true)['upazilas'];

$dhakaCity = json_decode(file_get_contents('D:/Personal/Ecommerce appliance/Location json/dhaka-city.json'), true)['dhaka'];

$final = [];

// Index Districts by Division
$districtsByDiv = [];
foreach ($districts as $district) {
    if (!isset($districtsByDiv[$district['division_id']])) {
        $districtsByDiv[$district['division_id']] = [];
    }
    // Initialize Upazila array
    $district['upazilas'] = [];
    $districtsByDiv[$district['division_id']][] = $district;
}

// Index Upazilas by District
$upazilasByDist = [];
foreach ($upazilas as $upazila) {
    // Skip if conflict or duplicates might occur? No, just load them.
    $upazilasByDist[$upazila['district_id']][] = $upazila;
}

// Process Dhaka City Locations as Upazilas for District ID 1
// We need to generate IDs for them as they don't seem to have unique IDs in the JSON (or we can use index)
// Ensure uniformity in structure: { id, district_id, name, bn_name }
$dhakaUpazilas = [];
foreach ($dhakaCity as $index => $area) {
    $dhakaUpazilas[] = [
        'id' => 'dhaka_' . ($index + 1), // Generate a unique ID string
        'district_id' => '1',
        'name' => $area['name'] . ' (' . $area['city_corporation'] . ')', // Append Corporation for clarity?
        'bn_name' => $area['bn_name']
    ];
}
// OVERRIDE Dhaka (ID 1) Upazilas
$upazilasByDist['1'] = $dhakaUpazilas;


// Merge Upazilas into Districts
foreach ($districtsByDiv as $divId => &$distList) {
    foreach ($distList as &$dist) {
        if (isset($upazilasByDist[$dist['id']])) {
            $dist['upazilas'] = $upazilasByDist[$dist['id']];
        }
    }
}

// Merge Districts into Divisions
foreach ($divisions as $division) {
    if (isset($districtsByDiv[$division['id']])) {
        $division['districts'] = $districtsByDiv[$division['id']];
    } else {
        $division['districts'] = [];
    }
    $final[] = $division;
}

// Ensure output dir exists
if (!is_dir('D:/Personal/Ecommerce appliance/public/assets/data')) {
    mkdir('D:/Personal/Ecommerce appliance/public/assets/data', 0777, true);
}

file_put_contents('D:/Personal/Ecommerce appliance/public/assets/data/bd_locations.json', json_encode($final));

echo "Merged locations to public/assets/data/bd_locations.json\n";
