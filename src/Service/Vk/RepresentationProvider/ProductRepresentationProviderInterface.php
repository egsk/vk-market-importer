<?php


namespace App\Service\Vk\RepresentationProvider;


use App\Service\Vk\DataSource\DataSourceInterface;
use App\Service\Vk\DTO\ProductRepresentation;

interface ProductRepresentationProviderInterface
{
    public function create(DataSourceInterface $dataSource, array $row): ProductRepresentation;
}