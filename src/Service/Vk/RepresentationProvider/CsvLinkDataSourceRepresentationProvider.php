<?php


namespace App\Service\Vk\RepresentationProvider;


use App\Repository\VkMarketCategoryRepository;
use App\Service\Vk\DataSource\DataSourceInterface;
use App\Service\Vk\DTO\ProductRepresentation;
use GuzzleHttp\Client;
use Html2Text\Html2Text;

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
        $representation->setName(substr($name, 0, 120));
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
        $price = (float)(str_replace(',', '.', $row[$dataSource->getPrice()]));
        if ($price <= 0.01) {
            $price = 0.01;
        }
        $representation->setPrice($price);
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

    /**
     * @param DataSourceInterface $dataSource
     * @return ProductRepresentation[]
     */
    public function createBatch(DataSourceInterface $dataSource): array
    {
        $data = $this->httpClient->request('GET',
            $dataSource->getSourceUrl()
        )->getBody()->getContents();
        $rows = explode("\n", $data);

        $csv = array_map(function ($row) use ($data, $dataSource) {
            return str_getcsv(
                trim(str_replace("\\xef\\xbb\\xbf", '', $row), "\t\n\r\0\x0B\" "),
                $dataSource->getDelimiter(),
                $dataSource->getEnclosure()
            );
        }, $rows);
        $preparedData = [];
        $keys = array_map(function ($key) {
            return trim($key, "\t\n\r\0\x0B\"");
        }, $csv[0]);
        for ($i = 1; $i < count($csv); $i++) {
            $row = $csv[$i];
            if (count($row) !== count($keys)) {
                continue;
            }
            $preparedData[$i - 1] = [];
            foreach ($row as $key => $field) {
                $preparedData[$i - 1][$keys[$key]] = $field;
            }
        }
        $result = [];
        foreach ($preparedData as $row) {
            $result[] = $this->create($dataSource, $row);
        }

        return $result;
    }
}
