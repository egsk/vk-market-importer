<?php

namespace App\Entity;

use App\Repository\CsvLinkDataSourceVkProductRepository;
use App\Service\Vk\DataSource\DataSourceInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CsvLinkDataSourceVkProductRepository::class)
 */
class CsvLinkDataSourceVkProduct extends VkProduct
{
    /**
     * @ORM\ManyToOne(targetEntity=CsvLinkDataSource::class, inversedBy="vkProducts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $dataSource;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDataSource(): ?DataSourceInterface
    {
        return $this->dataSource;
    }

    public function setDataSource(?DataSourceInterface $dataSource): self
    {
        $this->dataSource = $dataSource;

        return $this;
    }
}
