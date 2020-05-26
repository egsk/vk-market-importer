<?php

namespace App\Entity;

use App\Repository\VkMarketCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VkMarketCategoryRepository::class)
 */
class VkMarketCategory
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=ImportTarget::class, mappedBy="vkMarketCategory")
     */
    private $importTargets;

    /**
     * @ORM\OneToMany(targetEntity=VkProduct::class, mappedBy="vkMarketCategory")
     */
    private $vkProducts;

    public function __construct()
    {
        $this->importTargets = new ArrayCollection();
        $this->vkProducts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection|ImportTarget[]
     */
    public function getImportTargets(): Collection
    {
        return $this->importTargets;
    }

    public function addImportTarget(ImportTarget $importTarget): self
    {
        if (!$this->importTargets->contains($importTarget)) {
            $this->importTargets[] = $importTarget;
            $importTarget->setVkMarketCategory($this);
        }

        return $this;
    }

    public function removeImportTarget(ImportTarget $importTarget): self
    {
        if ($this->importTargets->contains($importTarget)) {
            $this->importTargets->removeElement($importTarget);
            // set the owning side to null (unless already changed)
            if ($importTarget->getVkMarketCategory() === $this) {
                $importTarget->setVkMarketCategory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|VkProduct[]
     */
    public function getVkProducts(): Collection
    {
        return $this->vkProducts;
    }

    public function addVkProduct(VkProduct $vkProduct): self
    {
        if (!$this->vkProducts->contains($vkProduct)) {
            $this->vkProducts[] = $vkProduct;
            $vkProduct->setVkMarketCategory($this);
        }

        return $this;
    }

    public function removeVkProduct(VkProduct $vkProduct): self
    {
        if ($this->vkProducts->contains($vkProduct)) {
            $this->vkProducts->removeElement($vkProduct);
            // set the owning side to null (unless already changed)
            if ($vkProduct->getVkMarketCategory() === $this) {
                $vkProduct->setVkMarketCategory(null);
            }
        }

        return $this;
    }
}
