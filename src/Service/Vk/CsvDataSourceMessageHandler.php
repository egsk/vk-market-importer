<?php


namespace App\Service\Vk;


use App\Entity\CsvLinkDataSource;
use App\Entity\CsvLinkDataSourceVkProduct;
use App\Entity\UploadedProduct;
use App\Entity\UploadTask;
use App\Service\Vk\RepresentationProvider\CsvLinkDataSourceRepresentationProvider;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;

class CsvDataSourceMessageHandler
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;
    /**
     * @var Client
     */
    protected $httpClient;
    /**
     * @var CsvLinkDataSource
     */
    protected $dataSource;
    /**
     * @var UploadTask
     */
    protected $uploadTask;
    /**
     * @var string
     */
    protected $path;
    protected $resource;
    protected $productUploader;
    protected $representationProvider;
    protected $fullPath;

    public function __construct(string $path, EntityManagerInterface $entityManager, ProductUploader $productUploader, CsvLinkDataSourceRepresentationProvider $representationProvider)
    {
        $this->entityManager = $entityManager;
        $this->httpClient = new Client();
        $this->path = $path;
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        $this->productUploader = $productUploader;
        $this->representationProvider = $representationProvider;
    }

    public function handle(CsvLinkDataSource $dataSource, UploadTask $uploadTask)
    {
        $this->dataSource = $dataSource;
        $this->uploadTask = $uploadTask;
        $this->resource = $this->createResource();
        $this->process();
        $this->clear();
    }

    protected function createResource()
    {
        $url = $this->dataSource->getSourceUrl();
        $fileName = md5($url) . '_' . time() . '.csv';
        $this->fullPath = $this->path . '/' . $fileName;
        $resource = fopen($this->fullPath, 'w+');
        $this->httpClient
            ->request('GET', $url, ['sink' => $resource]);
        $resource = fopen($this->fullPath, 'r');

        return $resource;
    }

    protected function process()
    {
        $keys = fgetcsv($this->resource, 0, $this->dataSource->getDelimiter(), $this->dataSource->getEnclosure());
        $keys = array_map(function ($key) {
            return trim($key, "\t\n\r\0\x0B\"");
        }, $keys);
        $user = $this->dataSource->getUser();
        while ($row = fgetcsv($this->resource, 0, $this->dataSource->getDelimiter(), $this->dataSource->getEnclosure())) {
            if (count($row) !== count($keys)) {
                continue;
            }
            $row = array_combine($keys, $row);
            $representation = $this->representationProvider->create($this->dataSource, $row);
            /**
             * @var CsvLinkDataSourceVkProduct $product
             */
            $product = $this->entityManager
                ->getRepository(CsvLinkDataSourceVkProduct::class)
                ->findOneBy([
                    'dataSource' => $this->dataSource->getId(),
                    'sourceId' => $representation->getSourceId()
                ]);
            $isNew = is_null($product);
            $result = $this->productUploader->handleRepresentation(
                $user->getVkAccessToken(),
                $this->dataSource->getImportTarget()->getGroupId(),
                $representation,
                $product,
                CsvLinkDataSourceVkProduct::class
            );
            $product = $result->getVkProduct();
            $uploadedProduct = new UploadedProduct();
            $uploadedProduct->setUploadTask($this->uploadTask);
            $uploadedProduct->setName($representation->getName());
            $uploadedProduct->setStatus($result->getStatus());
            $uploadedProduct->setSourceId($representation->getSourceId());
            if ($isNew && !is_null($product)) {
                $product->setUser($user);
                $product->setDataSource($this->dataSource);
                $this->entityManager->persist($product);
            }
            $this->entityManager->persist($uploadedProduct);
            $this->entityManager->flush();
        }
    }

    protected function clear()
    {
        fclose($this->resource);
        unlink($this->fullPath);
    }
}