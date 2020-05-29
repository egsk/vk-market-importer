<?php

namespace App\Entity;

use App\Repository\CsvLinkDataSourceRepository;
use App\Service\Vk\Annotation\DataSource;
use App\Service\Vk\DataSource\DataSourceInterface;
use App\Service\Vk\RepresentationProvider\CsvLinkDataSourceRepresentationProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CsvLinkDataSourceRepository::class)
 * @DataSource(providerClass=CsvLinkDataSourceRepresentationProvider::class, entityClass=CsvLinkDataSourceVkProduct::class)
 */
class CsvLinkDataSource implements DataSourceInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=ImportTarget::class, inversedBy="csvLinkDataSources")
     * @ORM\JoinColumn(nullable=false)
     */
    private $importTarget;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Url()
     */
    private $sourceUrl;

    /**
     * @ORM\Column(type="string", length=1)
     * @Assert\Length(1)
     * @Assert\NotBlank()
     */
    private $delimiter = ',';

    /**
     * @ORM\Column(type="string", length=1)
     * @Assert\Length(1)
     */
    private $enclosure = '"';

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nameHandlePattern;

    /**
     * @ORM\Column(type="text")
     */
    private $descriptionPattern;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $categoryName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photoUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $albumName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $albumHandlePattern;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sourceLabel;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="csvLinkDataSources")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $uniqueId;

    /**
     * @ORM\Column(type="boolean")
     */
    private $validated = false;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @ORM\OneToMany(targetEntity=CsvLinkDataSourceVkProduct::class, mappedBy="dataSource", orphanRemoval=true)
     */
    private $vkProducts;

    public function __construct()
    {
        $this->vkProducts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImportTarget(): ?ImportTarget
    {
        return $this->importTarget;
    }

    public function setImportTarget(?ImportTarget $importTarget): self
    {
        $this->importTarget = $importTarget;

        return $this;
    }

    public function getSourceUrl(): ?string
    {
        return $this->sourceUrl;
    }

    public function setSourceUrl(string $sourceUrl): self
    {
        $this->sourceUrl = $sourceUrl;

        return $this;
    }

    public function getDelimiter(): ?string
    {
        return $this->delimiter;
    }

    public function setDelimiter(string $delimiter): self
    {
        $this->delimiter = $delimiter;

        return $this;
    }

    public function getEnclosure(): ?string
    {
        return $this->enclosure;
    }

    public function setEnclosure(string $enclosure): self
    {
        $this->enclosure = $enclosure;

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

    public function getNameHandlePattern(): ?string
    {
        return $this->nameHandlePattern;
    }

    public function setNameHandlePattern(?string $nameHandlePattern): self
    {
        $this->nameHandlePattern = $nameHandlePattern;

        return $this;
    }

    public function getDescriptionPattern(): ?string
    {
        return $this->descriptionPattern;
    }

    public function setDescriptionPattern(string $descriptionPattern): self
    {
        $this->descriptionPattern = $descriptionPattern;

        return $this;
    }

    public function getCategoryName(): ?string
    {
        return $this->categoryName;
    }

    public function setCategoryName(?string $categoryName): self
    {
        $this->categoryName = $categoryName;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getPhotoUrl(): ?string
    {
        return $this->photoUrl;
    }

    public function setPhotoUrl(?string $photoUrl): self
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

    public function getAlbumHandlePattern(): ?string
    {
        return $this->albumHandlePattern;
    }

    public function setAlbumHandlePattern(?string $albumHandlePattern): self
    {
        $this->albumHandlePattern = $albumHandlePattern;

        return $this;
    }

    public function getSourceLabel(): ?string
    {
        return $this->sourceLabel;
    }

    public function setSourceLabel(string $sourceLabel): self
    {
        $this->sourceLabel = $sourceLabel;

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

    public function getUniqueId(): ?string
    {
        return $this->uniqueId;
    }

    public function setUniqueId(string $uniqueId): self
    {
        $this->uniqueId = $uniqueId;

        return $this;
    }

    public function getValidated(): ?bool
    {
        return $this->validated;
    }

    public function setValidated(bool $validated): self
    {
        $this->validated = $validated;

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

    /**
     * @return Collection|CsvLinkDataSourceVkProduct[]
     */
    public function getVkProducts(): Collection
    {
        return $this->vkProducts;
    }

    public function addVkProduct(CsvLinkDataSourceVkProduct $vkProduct): self
    {
        if (!$this->vkProducts->contains($vkProduct)) {
            $this->vkProducts[] = $vkProduct;
            $vkProduct->setDataSource($this);
        }

        return $this;
    }

    public function removeVkProduct(CsvLinkDataSourceVkProduct $vkProduct): self
    {
        if ($this->vkProducts->contains($vkProduct)) {
            $this->vkProducts->removeElement($vkProduct);
            // set the owning side to null (unless already changed)
            if ($vkProduct->getDataSource() === $this) {
                $vkProduct->setDataSource(null);
            }
        }

        return $this;
    }
}
