<?php


namespace App\MessageHandler;


use App\Message\UpdateDataSource;
use App\Service\Vk\DataSource\DataSourceInterface;
use App\Service\Vk\ProductRepresentation\DataSourceManager;
use App\Service\Vk\ProductUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class UpdateDataSourceMessageHandler implements MessageHandlerInterface
{
    protected $entityManager;
    protected $dataSourceManager;
    protected $productUploader;

    public function __construct(
        EntityManagerInterface $entityManager,
        DataSourceManager $representationProviderFactory,
        ProductUploader $productUploader
    )
    {
        $this->entityManager = $entityManager;
        $this->dataSourceManager = $representationProviderFactory;
        $this->productUploader = $productUploader;
    }

    public function __invoke(UpdateDataSource $message)
    {
        $dataSourceClass = $message->getDataSourceClass();
        /**
         * @var DataSourceInterface $dataSource
         */
        $dataSource = $this->entityManager
            ->getRepository($dataSourceClass)
            ->find($message->getDataSourceId());
        $user = $dataSource->getUser();
        $products = $dataSource->getVkProducts();
        $productRepresentations = $this->dataSourceManager
            ->getProductRepresentationProvider($dataSourceClass)
            ->create($dataSource);
        $result = $this->productUploader->upload(
            $user->getVkAccessToken(),
            $dataSource->getImportTarget()->getGroupId(),
            $productRepresentations,
            $products->toArray(),
            $this->dataSourceManager->getEntityClass($dataSourceClass)
        );
        $createdProducts = $result->getCreated();
        foreach ($createdProducts as $product) {
            $product->setDataSource($dataSource);
            $product->setUser($user);
            $this->entityManager->persist($product);
        }
        $this->entityManager->flush();
    }
}