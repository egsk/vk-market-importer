<?php


namespace App\Service\Vk\RepresentationProvider;


use App\Service\Vk\DataSource\DataSourceInterface;

interface ProductRepresentationProviderInterface
{
    public function create(DataSourceInterface $dataSource): array;
}