<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CacheTest extends TestCase
{
    public function testCacheHit()
    {
        // Preparación de la información de prueba
        $latitude = 10.123;
        $longitude = -80.456;
        $nearest = true;

        // Envía la solicitud a la API
        $response = $this->get("api/pharmacies?lat={$latitude}&lon={$longitude}");

        // Creación de la clave de caché
        $cacheKey = "pharmacies_{$latitude}_{$longitude}_{$nearest}";

        // Comprueba si la clave de caché existe antes de agregarla a la caché
        $this->assertFalse(Cache::has($cacheKey));

        // Agrega la respuesta de la API a la caché con una duración de 30 minutos
        Cache::put($cacheKey, $response, 30);

        // Comprueba si la clave de caché existe después de agregarla a la caché
        $this->assertTrue(Cache::has($cacheKey));

        // Obtiene la respuesta de la caché
        Cache::get($cacheKey);

        // Elimina la clave de caché
        Cache::forget($cacheKey);

        // Comprueba si la clave de caché existe después de eliminarla de la caché
        $this->assertFalse(Cache::has($cacheKey));
    }


    public function testCacheMiss()
    {
        // Informacion erronea
        $latitude = null;
        $longitude = null;
        $nearest = false;

        // Envía la solicitud a la API
        $response = $this->get("api/pharmacies?lat={$latitude}&lon={$longitude}");

        // Creación de la clave de caché
        $cacheKey = "pharmacies_{$latitude}_{$longitude}_{$nearest}";

        // Obtener las farmacias de la cache
        Cache::get($cacheKey);

        // Verificar que la key no existe en la cache
        $this->assertFalse(Cache::has($cacheKey));

        // Realizar las comprobaciones necesarias
        Cache::put($cacheKey, $response, 30);

        // Verificar que la key existe en la cache
        $this->assertTrue(Cache::has($cacheKey));

        // Realizar la respuesta de la cache
        Cache::forget($cacheKey);

        // Verificar que la key no existe en la cache
        $this->assertFalse(Cache::has($cacheKey));
    }
}
