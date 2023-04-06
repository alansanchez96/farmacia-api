<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Pharmacy;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\JsonResponseService;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\PharmacyRequest;
use App\Http\Resources\PharmacyResource;
use App\Http\Resources\PharmacyCollection;
use App\Http\Requests\NearbyPharmacyRequest;

class PharmacyController extends Controller
{
    /**
     * Calcula la Latitud y Longitud y retorna una collection segun su ubicacion
     *
     * @param NearbyPharmacyRequest $request
     * @param JsonResponseService $response
     * @return JsonResponse|PharmacyCollection
     */
    public function index(NearbyPharmacyRequest $request, JsonResponseService $response): JsonResponse|PharmacyCollection
    {
        try {
            return $this->getPharmacies($request);
        } catch (Exception $e) {
            Log::debug($e->getMessage());
            return $response->jsonFailure();
        }
    }

    /**
     * Calcula la Latitud y Longitud y retorna una collection segun su ubicacion a 10 metros de distancia
     *
     * @param NearbyPharmacyRequest $request
     * @param JsonResponseService $response
     * @return JsonResponse|PharmacyCollection
     */
    public function getNearestPharmacy(NearbyPharmacyRequest $request, JsonResponseService $response): JsonResponse|PharmacyCollection
    {
        try {
            return $this->getPharmacies($request, true);
        } catch (Exception $e) {
            Log::debug($e->getMessage());
            return $response->jsonFailure();
        }
    }

    /**
     * Obtenemos un registro atraves de su ID para luego retornarlo
     *
     * @param integer $id
     * @param JsonResponseService $response
     * @return PharmacyResource|JsonResponse
     */
    public function show(int $id, JsonResponseService $response): PharmacyResource|JsonResponse
    {
        try {
            $pharmacy = Pharmacy::findOrFail($id);

            return new PharmacyResource($pharmacy);
        } catch (Exception $e) {
            Log::debug($e->getMessage());
            return $response->jsonFailure();
        }
    }

    /**
     * Crea un nuevo registro en DB
     *
     * @param PharmacyRequest $request
     * @param JsonResponseService $response
     * @return JsonResponse
     */
    public function store(PharmacyRequest $request, JsonResponseService $response): JsonResponse
    {
        try {
            $pharmacy = Pharmacy::create($request->validated());
            return $response->jsonSuccess(
                'creado',
                'pharmacy',
                new PharmacyResource($pharmacy)
            );
        } catch (Exception $e) {
            Log::debug($e->getMessage());
            return $response->jsonFailure();
        }
    }

    /**
     * Actualiza un registro en DB segun su ID
     *
     * @param PharmacyRequest $request
     * @param integer $id
     * @param JsonResponseService $response
     * @return JsonResponse
     */
    public function update(PharmacyRequest $request, int $id, JsonResponseService $response): JsonResponse
    {
        try {
            $pharmacy = Pharmacy::findOrFail($id);
            $pharmacy->update($request->validated());

            return $response->jsonSuccess(
                'actualizado',
                'pharmacy',
                new PharmacyResource($pharmacy)
            );
        } catch (Exception $e) {
            Log::debug($e->getMessage());
            return $response->jsonFailure();
        }
    }

    /**
     * Elimina un registro en DB segun su ID
     *
     * @param integer $id
     * @param JsonResponseService $response
     * @return JsonResponse
     */
    public function delete(int $id, JsonResponseService $response): JsonResponse
    {
        try {
            $pharmacy = Pharmacy::findOrFail($id);
            $pharmacy->delete();

            return $response->jsonSuccess('eliminado');
        } catch (Exception $e) {
            Log::debug($e->getMessage());
            return $response->jsonFailure();
        }
    }

    /**
     * obtiene la coleccion de Pharmacies
     *
     * @param NearbyPharmacyRequest $request
     * @param boolean|null $nearest
     * @return PharmacyCollection
     */
    private function getPharmacies(NearbyPharmacyRequest $request, ?bool $nearest = null): PharmacyCollection
    {
        $latitude = $request->input('lat');
        $longitude = $request->input('lon');

        $cacheKey = "pharmacies_{$latitude}_{$longitude}_{$nearest}";

        $pharmacies = Cache::remember($cacheKey, 60, function () use ($latitude, $longitude, $nearest) {
            return $nearest ?
                Pharmacy::select('id', 'name', 'address', 'latitude', 'longitude', 'created_at')
                ->whereRaw("ST_Distance_Sphere(POINT($longitude, $latitude), POINT(longitude, latitude)) <= 10")
                ->orderByRaw("ST_Distance_Sphere(POINT($longitude, $latitude), POINT(longitude, latitude)) ASC")
                ->get() :
                Pharmacy::select('id', 'name', 'address', 'latitude', 'longitude', 'created_at')
                ->orderByRaw("ST_Distance_Sphere(POINT($longitude, $latitude), POINT(longitude, latitude)) ASC")
                ->get();
        });

        return PharmacyCollection::make($pharmacies);
    }
}
