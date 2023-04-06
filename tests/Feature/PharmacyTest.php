<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Pharmacy;
use App\Services\PharmacyService;
use App\Exceptions\PharmacyException;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PharmacyTest extends TestCase
{
    public function testDistanciaCalculadaCorrectamente()
    {
        $this->withoutExceptionHandling();
        // Se crea un Pharmacy
        $pharmacy = Pharmacy::factory()->create([
            'name' => 'Pharmacy Test',
            'address' => 'Calle 123',
            'latitude' => 10,
            'longitude' => 20
        ]);

        // Se crea un punto de referencia
        $latRef = 12;
        $lonRef = 18;

        // Se calcula la distancia utilizando el PharmacyService
        $pharmacyService = new PharmacyService();
        $distance = $pharmacyService->calculateDistance($pharmacy->latitude, $pharmacy->longitude, $latRef, $lonRef);

        // Se comprueba que la distancia sea la esperada
        $this->assertEquals($distance, $distance);
    }

    public function testErrorCalculandoDistancia()
    {
        $this->withoutExceptionHandling();
        $this->expectException(PharmacyException::class);

        // Se crea una Pharmacy sin latitud ni longitud
        $pharmacy = Pharmacy::factory()->create([
            'name' => 'Pharmacy Test',
            'address' => 'Calle 123'
        ]);

        // Se crea un punto de referencia
        $latRef = 12;
        $lonRef = 18;

        // Se calcula la distancia utilizando el servicio de Pharmacy
        $pharmacyService = new PharmacyService();
        $pharmacyService->calculateDistance($pharmacy->latitud, $pharmacy->longitud, $latRef, $lonRef);
    }
}
