<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use App\Models\Pharmacy as PharmacyModel;
use App\Repositories\Contracts\IPharmacy;

class Pharmacy implements IPharmacy
{
    public function getCollection(?Request $request)
    {
        if ($this->validateRequest($request))
            return PharmacyModel::select('id', 'name', 'address', 'latitude', 'longitude', 'created_at')->get();

        $longitude = $request->input('lon');
        $latitude = $request->input('lat');

        return PharmacyModel::select('id', 'name', 'address', 'latitude', 'longitude', 'created_at')
            ->orderByRaw("ST_Distance_Sphere(POINT($longitude, $latitude), POINT(longitude, latitude)) ASC")
            ->get();
    }

    public function getNearestCollection(?Request $request)
    {
        if ($this->validateRequest($request))
            return PharmacyModel::select('id', 'name', 'address', 'latitude', 'longitude', 'created_at')->get();

        $longitude = $request->input('lon');
        $latitude = $request->input('lat');

        return PharmacyModel::select('id', 'name', 'address', 'latitude', 'longitude', 'created_at')
            ->whereRaw("ST_Distance_Sphere(POINT($longitude, $latitude), POINT(longitude, latitude)) <= 10")
            ->orderByRaw("ST_Distance_Sphere(POINT($longitude, $latitude), POINT(longitude, latitude)) ASC")
            ->get();
    }

    public function findById(int $id)
    {
        return PharmacyModel::findOrFail($id);
    }

    public function store(?Request $request)
    {
        return PharmacyModel::create($request->validated());
    }

    private function validateRequest(Request $request)
    {
        if (!$request->has('lon') || !$request->has('lat')) {
            return true;
        }

        return false;
    }
}
