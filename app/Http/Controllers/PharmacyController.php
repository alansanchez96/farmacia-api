<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Pharmacy;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\JsonResponseService;
use App\Http\Requests\PharmacyRequest;
use App\Http\Resources\PharmacyResource;
use App\Http\Resources\PharmacyCollection;
use Illuminate\Support\Facades\Log;

class PharmacyController extends Controller
{
    /**
     * Obtenemos una collecion de Pharmacy
     * Le damos formato a la respuesta JSON con PharmacyCollection
     *
     * @param JsonResponseService $response
     * @return PharmacyCollection|JsonResponse
     */
    public function index(JsonResponseService $response): PharmacyCollection|JsonResponse
    {
        try {
            $pharmacies = Pharmacy::select('id', 'name', 'address', 'latitude', 'longitude', 'created_at')->get();

            return PharmacyCollection::make($pharmacies);
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
     * Calcula la Latitud y Longitud y una collection segun su ubicacion
     *
     * @param Request $request
     * @param JsonResponseService $response
     * @return JsonResponse|PharmacyCollection
     */
    public function nearbyPharmacy(Request $request, JsonResponseService $response): JsonResponse|PharmacyCollection
    {
        try {
            $latitude = $request->input('lat');
            $longitude = $request->input('lon');

            $pharmacies = Pharmacy::select('id', 'name', 'address', 'latitude', 'longitude', 'created_at')
                ->orderByRaw("ST_Distance_Sphere(POINT($longitude, $latitude), POINT(longitude, latitude)) ASC")
                ->get();

            return PharmacyCollection::make($pharmacies);
        } catch (Exception $e) {
            Log::debug($e->getMessage());
            return $response->jsonFailure();
        }
    }
}
