<?php

namespace App\Entity;

use App\Repository\VkProductRepository;
use App\Service\Vk\DataSource\DataSourceInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\InheritanceType;

/**
 * @ORM\Entity(repositoryClass=VkProductRepository::class)
 * @ORM\Table(name="vk_product")
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="data_source_type", type="string")
 * @DiscriminatorMap({"default"="VkProduct", "csv_data_source"="CsvLinkDataSourceVkProduct"})
 */
class VkProduct
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $vkMarketId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $sourceId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="float")
     */
    protected $price;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $photoUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $albumName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $url;

    /**
     * @ORM\Column(type="integer")
     */
    protected $ownerId;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    protected $oldPrice;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="vkProducts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="integer")
     */
    private $categoryId;

    /**
     * @ORM\Column(type="integer")
     */
    private $photoId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVkMarketId(): ?int
    {
        return $this->vkMarketId;
    }

    public function setVkMarketId(int $vkMarketId): self
    {
        $this->vkMarketId = $vkMarketId;

        return $this;
    }

    public function getSourceId(): ?string
    {
        return $this->sourceId;
    }

    public function setSourceId(string $sourceId): self
    {
        $this->sourceId = $sourceId;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getPhotoUrl(): ?string
    {
        return $this->photoUrl;
    }

    public function setPhotoUrl(string $photoUrl): self
    {
        $this->photoUrl = $photoUrl;

        return $this;
    }

    public function getAlbumName(): ?string
    {
        return $this->albumName;
    }

    public function setAlbumName(?string $albumName): self
    {
        $this->albumName = $albumName;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getOwnerId(): ?int
    {
        return $this->ownerId;
    }

    public function setOwnerId(int $ownerId): self
    {
        $this->ownerId = $ownerId;

        return $this;
    }

    public function getOldPrice(): ?float
    {
        return $this->oldPrice;
    }

    public function setOldPrice(?float $oldPrice): self
    {
        $this->oldPrice = $oldPrice;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCategoryId(): ?int
    {
        return $this->categoryId;
    }

    public function setCategoryId(int $categoryId): self
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    public function getPhotoId(): ?int
    {
        return $this->photoId;
    }

    public function setPhotoId(int $photoId): self
    {
        $this->photoId = $photoId;

        return $this;
    }

    public function getDataSource()
    {
        return null;
    }

    public function setDataSource(?DataSourceInterface $dataSource)
    {
        return $this;
    }
}
