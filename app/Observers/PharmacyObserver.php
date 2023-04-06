<?php

namespace App\Observers;

use App\Models\Pharmacy;
use Illuminate\Support\Facades\Cache;

class PharmacyObserver
{
    public function created(Pharmacy $pharmacy)
    {
        $this->invalidateCache($pharmacy);
        $this->cachePharmacy($pharmacy);
    }

    public function updated(Pharmacy $pharmacy)
    {
        if ($pharmacy->isDirty(['latitude', 'longitude'])) {
            $this->invalidateCache($pharmacy);
            $this->cachePharmacy($pharmacy);
        }
    }

    public function deleted(Pharmacy $pharmacy)
    {
        $this->invalidateCache($pharmacy);
    }

    private function cachePharmacy(Pharmacy $pharmacy)
    {
        $latitude = $pharmacy->latitude;
        $longitude = $pharmacy->longitude;
        $nearest = true;

        $cacheKey = "pharmacies_{$latitude}_{$longitude}_";
        $cacheKeyNearest = "pharmacies_{$latitude}_{$longitude}_{$nearest}";

        $pharmacies = $pharmacy::select('id', 'name', 'address', 'latitude', 'longitude', 'created_at')
            ->orderByRaw("ST_Distance_Sphere(POINT($longitude, $latitude), POINT(longitude, latitude)) ASC")
            ->get();

        $pharmaciesNearest = $pharmacy::select('id', 'name', 'address', 'latitude', 'longitude', 'created_at')
            ->whereRaw("ST_Distance_Sphere(POINT($longitude, $latitude), POINT(longitude, latitude)) <= 10")
            ->orderByRaw("ST_Distance_Sphere(POINT($longitude, $latitude), POINT(longitude, latitude)) ASC")
            ->get();

        Cache::put($cacheKey, $pharmacies, 60);
        Cache::put($cacheKeyNearest, $pharmaciesNearest, 60);
    }

    private function invalidateCache(Pharmacy $pharmacy)
    {
        $latitude = $pharmacy->latitude;
        $longitude = $pharmacy->longitude;
        $nearest = true;

        $cacheKey = "pharmacies_{$latitude}_{$longitude}_";
        $cacheKeyNearest = "pharmacies_{$latitude}_{$longitude}_{$nearest}";

        Cache::forget($cacheKey);
        Cache::forget($cacheKeyNearest);
    }
}
