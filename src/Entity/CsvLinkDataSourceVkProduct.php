<?php

namespace App\Entity;

use App\Repository\CsvLinkDataSourceVkProductRepository;
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

    public function getDataSource(): ?CsvLinkDataSource
    {
        return $this->dataSource;
    }

    public function setDataSource(?CsvLinkDataSource $dataSource): self
    {
        $this->dataSource = $dataSource;

        return $this;
    }
}
