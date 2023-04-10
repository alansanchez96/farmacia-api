<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\PharmacyRequest;
use App\Http\Resources\PharmacyResource;
use App\Repositories\Contracts\IPharmacy;
use App\Http\Resources\PharmacyCollection;
use App\Http\Requests\NearbyPharmacyRequest;

class PharmacyController extends Controller
{
    private $pharmacy;

    public function __construct(IPharmacy $pharmacy)
    {
        parent::__construct();
        $this->pharmacy = $pharmacy;
    }

    public function index(NearbyPharmacyRequest $request)
    {
        try {
            $pharmacies = $this->pharmacy->getCollection($request);

            return PharmacyCollection::make($pharmacies);
        } catch (\Exception $e) {
            return $this->response->jsonFailure($e->getMessage());
        }
    }

    public function nearestPharmacies(NearbyPharmacyRequest $request)
    {
        try {
            $pharmacies = $this->pharmacy->getNearestCollection($request);

            return PharmacyCollection::make($pharmacies);
        } catch (\Exception $e) {
            return $this->response->jsonFailure($e->getMessage());
        }
    }

    public function show(int $id)
    {
        try {
            $pharmacy = $this->pharmacy->findById($id);

            return new PharmacyResource($pharmacy);
        } catch (\Exception $e) {
            return $this->response->jsonFailure($e->getMessage());
        }
    }

    public function store(PharmacyRequest $request)
    {
        try {
            $pharmacy = $this->pharmacy->store($request);

            $pharmacy = new PharmacyResource($pharmacy);

            return $this->response->jsonSuccess('creado', $pharmacy);
        } catch (\Exception $e) {
            return $this->response->jsonFailure($e->getMessage());
        }
    }
}
