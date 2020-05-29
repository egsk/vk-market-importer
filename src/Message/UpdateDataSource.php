<?php


namespace App\Message;


use App\Service\Vk\DataSource\DataSourceInterface;

class UpdateDataSource
{
    public function __construct(DataSourceInterface $dataSource)
    {
        $this->dataSourceId = $dataSource->getId();
        $this->dataSourceClass = get_class($dataSource);
    }

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

}