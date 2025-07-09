<?php

namespace App\Services;

use App\Contracts\FipeServiceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Serviço para consumir a API da FIPE
 *
 * Este serviço implementa a interface FipeServiceInterface e fornece
 * métodos para obter informações sobre veículos da API da FIPE.
 */
class FipeService implements FipeServiceInterface
{
    /**
     * URL base da API da FIPE
     */
    private const BASE_URL = 'https://fipe.parallelum.com.br/api/v2';

    /**
     * Timeout para requisições HTTP em segundos
     */
    private const REQUEST_TIMEOUT = 30;

    /**
     * Tempo de cache em minutos para dados que mudam raramente
     */
    private const CACHE_TTL = 1440; // 24 horas

    /**
     * Mapeamento dos tipos de veículos para a API
     */
    private const VEHICLE_TYPES = [
        'cars' => 'Carros',
        'motorcycles' => 'Motos',
        'trucks' => 'Caminhões'
    ];

    /**
     * Obtém os tipos de veículos disponíveis
     *
     * @return array Array com os tipos de veículos
     */
    public function getVehicleTypes(): array
    {
        return collect(self::VEHICLE_TYPES)->map(function ($label, $value) {
            return [
                'id' => $value,
                'name' => $label
            ];
        })->values()->toArray();
    }

    /**
     * Obtém as marcas disponíveis para um tipo de veículo específico
     *
     * @param string $vehicleType Tipo do veículo
     * @return array Array com as marcas disponíveis
     */
    public function getBrands(string $vehicleType): array
    {
        $cacheKey = "fipe_brands_{$vehicleType}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($vehicleType) {
            try {
                $response = Http::timeout(self::REQUEST_TIMEOUT)
                    ->get(self::BASE_URL . "/{$vehicleType}/brands");

                if ($response->successful()) {
                    $brands = $response->json();

                    // Formata os dados para o formato esperado pelo Filament
                    return collect($brands)->map(function ($brand) {
                        return [
                            'id' => $brand['code'],
                            'name' => $brand['name']
                        ];
                    })->toArray();
                }

                Log::error("Erro ao obter marcas da API FIPE", [
                    'vehicle_type' => $vehicleType,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return [];
            } catch (\Exception $e) {
                Log::error("Exceção ao obter marcas da API FIPE", [
                    'vehicle_type' => $vehicleType,
                    'error' => $e->getMessage()
                ]);

                return [];
            }
        });
    }

    /**
     * Obtém os modelos disponíveis para uma marca específica
     *
     * @param string $vehicleType Tipo do veículo
     * @param int $brandId ID da marca
     * @return array Array com os modelos disponíveis
     */
    public function getModels(string $vehicleType, int $brandId): array
    {
        $cacheKey = "fipe_models_{$vehicleType}_{$brandId}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($vehicleType, $brandId) {
            try {
                $response = Http::timeout(self::REQUEST_TIMEOUT)
                    ->get(self::BASE_URL . "/{$vehicleType}/brands/{$brandId}/models");

                if ($response->successful()) {
                    $models = $response->json();

                    // Formata os dados para o formato esperado pelo Filament
                    return collect($models)->map(function ($model) {
                        return [
                            'id' => $model['code'],
                            'name' => $model['name']
                        ];
                    })->toArray();
                }

                Log::error("Erro ao obter modelos da API FIPE", [
                    'vehicle_type' => $vehicleType,
                    'brand_id' => $brandId,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return [];
            } catch (\Exception $e) {
                Log::error("Exceção ao obter modelos da API FIPE", [
                    'vehicle_type' => $vehicleType,
                    'brand_id' => $brandId,
                    'error' => $e->getMessage()
                ]);

                return [];
            }
        });
    }

    /**
     * Obtém os anos disponíveis para um modelo específico
     *
     * @param string $vehicleType Tipo do veículo
     * @param int $brandId ID da marca
     * @param int $modelId ID do modelo
     * @return array Array com os anos disponíveis
     */
    public function getYears(string $vehicleType, int $brandId, int $modelId): array
    {
        $cacheKey = "fipe_years_{$vehicleType}_{$brandId}_{$modelId}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($vehicleType, $brandId, $modelId) {
            try {
                $response = Http::timeout(self::REQUEST_TIMEOUT)
                    ->get(self::BASE_URL . "/{$vehicleType}/brands/{$brandId}/models/{$modelId}/years");

                if ($response->successful()) {
                    $years = $response->json();

                    // Formata os dados para o formato esperado pelo Filament
                    return collect($years)->map(function ($year) {
                        return [
                            'id' => $year['code'],
                            'name' => $year['name']
                        ];
                    })->toArray();
                }

                Log::error("Erro ao obter anos da API FIPE", [
                    'vehicle_type' => $vehicleType,
                    'brand_id' => $brandId,
                    'model_id' => $modelId,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return [];
            } catch (\Exception $e) {
                Log::error("Exceção ao obter anos da API FIPE", [
                    'vehicle_type' => $vehicleType,
                    'brand_id' => $brandId,
                    'model_id' => $modelId,
                    'error' => $e->getMessage()
                ]);

                return [];
            }
        });
    }

    /**
     * Obtém as informações detalhadas do veículo incluindo preço FIPE
     *
     * @param string $vehicleType Tipo do veículo
     * @param int $brandId ID da marca
     * @param int $modelId ID do modelo
     * @param string $yearId ID do ano
     * @return array Array com as informações detalhadas do veículo
     */
    public function getVehicleInfo(string $vehicleType, int $brandId, int $modelId, string $yearId): array
    {
        // Cache por menos tempo pois o preço pode mudar mais frequentemente
        $cacheKey = "fipe_vehicle_info_{$vehicleType}_{$brandId}_{$modelId}_{$yearId}";

        return Cache::remember($cacheKey, 60, function () use ($vehicleType, $brandId, $modelId, $yearId) {
            try {
                $response = Http::timeout(self::REQUEST_TIMEOUT)
                    ->get(self::BASE_URL . "/{$vehicleType}/brands/{$brandId}/models/{$modelId}/years/{$yearId}");

                if ($response->successful()) {
                    $vehicleInfo = $response->json();

                    return [
                        'brand' => $vehicleInfo['brand'] ?? '',
                        'model' => $vehicleInfo['model'] ?? '',
                        'year' => $vehicleInfo['year'] ?? '',
                        'fuel' => $vehicleInfo['fuel'] ?? '',
                        'price' => $vehicleInfo['price'] ?? '',
                        'month_reference' => $vehicleInfo['month_reference'] ?? '',
                        'fuel_acronym' => $vehicleInfo['fuel_acronym'] ?? '',
                        'vehicle_type' => $vehicleInfo['vehicle_type'] ?? ''
                    ];
                }

                Log::error("Erro ao obter informações do veículo da API FIPE", [
                    'vehicle_type' => $vehicleType,
                    'brand_id' => $brandId,
                    'model_id' => $modelId,
                    'year_id' => $yearId,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return [];
            } catch (\Exception $e) {
                Log::error("Exceção ao obter informações do veículo da API FIPE", [
                    'vehicle_type' => $vehicleType,
                    'brand_id' => $brandId,
                    'model_id' => $modelId,
                    'year_id' => $yearId,
                    'error' => $e->getMessage()
                ]);

                return [];
            }
        });
    }
}
