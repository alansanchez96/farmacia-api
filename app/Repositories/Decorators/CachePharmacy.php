<?php

namespace App\Repositories\Decorators;

use Illuminate\Http\Request;
use App\Repositories\Pharmacy;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\IPharmacy;
use Illuminate\Database\Eloquent\Collection;

class CachePharmacy implements IPharmacy
{
    private $repository;

    public function __construct()
    {
        $this->repository = new Pharmacy;
    }

    public function getCollection(?Request $request)
    {
        $longitude = $request->input('lon');
        $latitude = $request->input('lat');

        $key = "pharmacies_{$latitude}_{$longitude}";

        return Cache::rememberForever($key, fn () => $this->repository->getCollection($request));
    }

    public function getNearestCollection(?Request $request)
    {
        $longitude = $request->input('lon');
        $latitude = $request->input('lat');

        $key = "pharmacies_{$latitude}_{$longitude}_nearest";

        return Cache::rememberForever($key, fn () => $this->repository->getNearestCollection($request));
    }

    public function findById(int $id)
    {
        $key = "pharmacies_{$id}";

        return Cache::rememberForever($key, fn () => $this->repository->findById($id));
    }

    public function store(?Request $request)
    {
        // Con Redis => Cache::tag('key')->flush();
        Cache::flush();

        return $this->repository->store($request);
    }
}
