<?php

namespace App\Contracts;

/**
 * Interface para serviços que consomem a API da FIPE
 *
 * Esta interface define os métodos necessários para interagir com a API da FIPE
 * e obter informações sobre veículos, marcas, modelos e preços.
 */
interface FipeServiceInterface
{
    /**
     * Obtém os tipos de veículos disponíveis
     *
     * @return array Array com os tipos de veículos (carros, motos, caminhões)
     */
    public function getVehicleTypes(): array;

    /**
     * Obtém as marcas disponíveis para um tipo de veículo específico
     *
     * @param string $vehicleType Tipo do veículo (cars, motorcycles, trucks)
     * @return array Array com as marcas disponíveis
     */
    public function getBrands(string $vehicleType): array;

    /**
     * Obtém os modelos disponíveis para uma marca específica
     *
     * @param string $vehicleType Tipo do veículo
     * @param int $brandId ID da marca
     * @return array Array com os modelos disponíveis
     */
    public function getModels(string $vehicleType, int $brandId): array;

    /**
     * Obtém os anos disponíveis para um modelo específico
     *
     * @param string $vehicleType Tipo do veículo
     * @param int $brandId ID da marca
     * @param int $modelId ID do modelo
     * @return array Array com os anos disponíveis
     */
    public function getYears(string $vehicleType, int $brandId, int $modelId): array;

    /**
     * Obtém as informações detalhadas do veículo incluindo preço FIPE
     *
     * @param string $vehicleType Tipo do veículo
     * @param int $brandId ID da marca
     * @param int $modelId ID do modelo
     * @param string $yearId ID do ano
     * @return array Array com as informações detalhadas do veículo
     */
    public function getVehicleInfo(string $vehicleType, int $brandId, int $modelId, string $yearId): array;
}
