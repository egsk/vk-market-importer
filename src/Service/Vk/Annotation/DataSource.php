<?php


namespace App\Service\Vk\Annotation;


use App\Entity\VkProduct;

/**
 * Class ProductRepresentationProvider
 * @package App\Service\Vk\ProductRepresentation\Annotation
 *
 * @Annotation
 * @Target({"CLASS"})
 */
final class DataSource
{
    /**
     * @var string
     */
    public $providerClass;

    /**
     * @var string
     */
    public $entityClass = VkProduct::class;
}