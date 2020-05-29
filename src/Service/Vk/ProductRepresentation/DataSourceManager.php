<?php


namespace App\Service\Vk\ProductRepresentation;


use App\Service\Vk\Annotation\DataSource;
use App\Service\Vk\DataSource\DataSourceInterface;
use App\Service\Vk\RepresentationProvider\ProductRepresentationProviderInterface;
use Doctrine\Common\Annotations\Reader;
use ReflectionClass;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DataSourceManager
{
    /**
     * @var Reader
     */
    protected $reader;
    protected $container;
    protected $annotations = [];

    public function __construct(Reader $reader, ContainerInterface $container)
    {
        $this->reader = $reader;
        $this->container = $container;
    }

    public function getProductRepresentationProvider(string $dataSourceClass): ProductRepresentationProviderInterface
    {
        $annotation = $this->getClassAnnotation($dataSourceClass);
        /**
         * @var ProductRepresentationProviderInterface $provider
         */
        $provider = $this->container->get($annotation->providerClass);

        return $provider;
    }

    public function getEntityClass(string $dataSourceClass): string
    {
        return $this->getClassAnnotation($dataSourceClass)->entityClass;
    }

    protected function getClassAnnotation(string $dataSourceClass): DataSource
    {
        if (!array_key_exists($dataSourceClass, $this->annotations)) {
            $reflection = new ReflectionClass($dataSourceClass);
            /**
             * @var DataSource $dataSource
             */
            $annotation = $this->reader->getClassAnnotation($reflection, DataSource::class);
            $this->annotations[$dataSourceClass] = $annotation;
        }

        return $this->annotations[$dataSourceClass];
    }
}