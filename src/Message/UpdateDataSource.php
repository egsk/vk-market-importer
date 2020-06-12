<?php


namespace App\Message;


use App\Entity\UploadTask;
use App\Service\Vk\DataSource\DataSourceInterface;

class UpdateDataSource
{
    public function __construct(DataSourceInterface $dataSource, UploadTask $uploadTask)
    {
        $this->dataSourceId = $dataSource->getId();
        $this->uploadTaskId = $uploadTask->getId();
        $this->dataSourceClass = get_class($dataSource);
    }

    /**
     * @var int
     */
    protected $uploadTaskId;

    /**
     * @var int
     */
    protected $dataSourceId;
    /**
     * @var string
     */
    protected $dataSourceClass;

    /**
     * @return int
     */
    public function getDataSourceId(): int
    {
        return $this->dataSourceId;
    }

    /**
     * @return string
     */
    public function getDataSourceClass(): string
    {
        return $this->dataSourceClass;
    }

    /**
     * @return int
     */
    public function getUploadTaskId(): int
    {
        return $this->uploadTaskId;
    }

}