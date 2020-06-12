<?php


namespace App\MessageHandler;


use App\Entity\CsvLinkDataSource;
use App\Entity\UploadTask;
use App\Message\UpdateDataSource;
use App\Service\Vk\CsvDataSourceMessageHandler;
use App\Service\Vk\ProductRepresentation\DataSourceManager;
use App\Service\Vk\ProductUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class UpdateDataSourceMessageHandler implements MessageHandlerInterface
{
    protected $entityManager;
    protected $csvDataSourceMessageHandler;

    public function __construct(
        EntityManagerInterface $entityManager,
        CsvDataSourceMessageHandler $csvDataSourceMessageHandler
    )
    {
        $this->entityManager = $entityManager;
        $this->csvDataSourceMessageHandler = $csvDataSourceMessageHandler;
    }

    public function __invoke(UpdateDataSource $message)
    {
        $dataSourceClass = $message->getDataSourceClass();
        /**
         * @var CsvLinkDataSource $dataSource
         */
        $dataSource = $this->entityManager
            ->getRepository($dataSourceClass)
            ->find($message->getDataSourceId());
        /**
         * @var UploadTask $uploadTask
         */
        $uploadTask = $this->entityManager
            ->getRepository(UploadTask::class)
            ->find($message->getUploadTaskId());
        $uploadTask->setStatus(UploadTask::STATUS_IN_PROGRESS);
        $this->entityManager->flush();
        switch ($dataSourceClass) {
            case CsvLinkDataSource::class:
            default:
                $this->csvDataSourceMessageHandler
                    ->handle($dataSource, $uploadTask);
        }
        $uploadTask->setStatus(UploadTask::STATUS_FINISHED);
        $uploadTask->setCompletedAt(new \DateTime());
        $this->entityManager->flush();
    }
}