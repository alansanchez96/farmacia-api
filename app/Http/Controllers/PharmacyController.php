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

class PharmacyController extends Controller
{
    /**
     * Obtenemos una collecion de Pharmacy
     * Le damos formato a la respuesta JSON con PharmacyCollection
     *
     * @return PharmacyCollection
     */
    public function index(): PharmacyCollection
    {
        $pharmacies = Pharmacy::select('id', 'name', 'address', 'latitude', 'longitude', 'created_at')->get();

        return PharmacyCollection::make($pharmacies);
    }

    /**
     * Obtenemos un registro atraves de su ID para luego retornarlo
     *
     * @param integer $id
     * @return PharmacyResource
     */
    public function show(int $id): PharmacyResource
    {
        $pharmacy = Pharmacy::findOrFail($id);

        return new PharmacyResource($pharmacy);
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
            Pharmacy::create($request->validated());
            return $response->jsonSuccess('creado');
        } catch (Exception $e) {
            return $response->jsonFailure($e->getMessage());
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

            return $response->jsonSuccess('actualizado');
        } catch (Exception $e) {
            return $response->jsonFailure($e->getMessage());
        }
    }

    /**
     * Elimina un registro en DB segun su ID
     *
     * @param PharmacyRequest $request
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
            return $response->jsonFailure($e->getMessage());
        }
    }

    /**
     * Calcula la Latitud y Longitud y retorna el modelo aproximado
     *
     * @param Request $request
     * @param JsonResponseService $response
     * @return JsonResponse
     */
    public function nearbyPharmacy(Request $request, JsonResponseService $response): JsonResponse|PharmacyResource
    {
        try {
            $latitude = $request->input('lat');
            $longitude = $request->input('lon');

            $pharmacy = Pharmacy::select('id', 'name', 'address', 'latitude', 'longitude', 'created_at')
                ->orderByRaw("ST_Distance_Sphere(POINT($longitude, $latitude), POINT(longitude, latitude)) ASC")
                ->first();

            return new PharmacyResource($pharmacy);
        } catch (Exception $e) {
            return $response->jsonFailure($e->getMessage());
        }
    }
}
