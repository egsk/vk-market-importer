<?php


namespace App\Service\Vk\RepresentationProvider;


use App\Repository\VkMarketCategoryRepository;
use App\Service\Vk\DataSource\DataSourceInterface;
use App\Service\Vk\DTO\ProductRepresentation;
use GuzzleHttp\Client;
use Html2Text\Html2Text;
use Symfony\Component\VarDumper\Cloner\Data;

/**
 * Class CsvLinkDataSourceRepresentationProvider
 * @package App\Service\Vk\RepresentationProvider
 */
class CsvLinkDataSourceRepresentationProvider implements ProductRepresentationProviderInterface
{
    /**
     * @var Client
     */
    protected $httpClient;
    /**
     * @var VkMarketCategoryRepository
     */
    protected $repository;

    public function __construct(VkMarketCategoryRepository $repository)
    {
        $this->httpClient = new Client();
        $this->repository = $repository;
    }

    public function create(DataSourceInterface $dataSource, array $row): ProductRepresentation
    {
        $keys = array_map(function ($key) {
            return '%' . $key . '%';
        }, array_keys($row));

        $representation = new ProductRepresentation();
        $name = $row[$dataSource->getName()];
        if ($dataSource->getNameHandlePattern()) {
            $matches = [];
            preg_match_all($dataSource->getNameHandlePattern(), $name, $matches);
            if (!empty($matches[0])) {
                $name = $matches[0][0];
            }
        }
        $representation->setName($name);
        $description = $dataSource->getDescriptionPattern() ?
            str_replace($keys, array_values($row), $dataSource->getDescriptionPattern()) :
            '';
        $description = (new Html2Text($description))->getText();
        $representation->setDescription($description);
        $categoryNameField = $dataSource->getCategoryName();
        $fetchedCategory = [];
        if (
            !empty($row[$categoryNameField]) &&
            $vkMarketCategory = !empty($fetchedCategory[$row[$categoryNameField]]) ?
                $fetchedCategory[$categoryNameField] :
                $this->repository
                    ->findOneBy(['name' => $row[$categoryNameField]])
        ) {
            $fetchedCategory[$row[$categoryNameField]] = $vkMarketCategory;
            $representation->setCategoryName($vkMarketCategory->getName());
            $representation->setCategoryId($vkMarketCategory->getId());
        } else {
            $vkMarketCategory = $dataSource
                ->getImportTarget()
                ->getVkMarketCategory();
            $representation
                ->setCategoryName($vkMarketCategory->getName())
                ->setCategoryId($vkMarketCategory->getId());
        }
        $representation->setPrice($row[$dataSource->getPrice()]);
        $representation->setPhotoUrl($row[$dataSource->getPhotoUrl()]);
        $albumName = $row[$dataSource->getAlbumName()] ?? null;
        if ($albumHandlePattern = $dataSource->getAlbumHandlePattern()) {
            $matches = [];
            preg_match_all($albumHandlePattern ?: '', $albumName, $matches);
            if (!empty($matches[0])) {
                $albumName = $matches[0][0];
            }
        }
        if ($status = $dataSource->getStatus()) {
            $representation->setStatus((bool)$row[$dataSource->getStatus()]);
        }
        $representation->setAlbumName($albumName);
        $representation->setUrl($row[$dataSource->getUrl()]);
        $representation->setSourceId($row[$dataSource->getUniqueId()]);

        return $representation;
    }
}