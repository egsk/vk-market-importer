<?php

namespace App\Entity;

use App\Repository\UploadTaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=UploadTaskRepository::class)
 */
class UploadTask
{
    public const STATUS_NEW = 'new';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_FINISHED = 'finished';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"status"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="uploadTasks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"status"})
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"status"})
     */
    private $completed_at;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"status"})
     */
    private $status;

    /**
     * @ORM\Column(type="integer")
     */
    private $uploadedProductsCount = 0;

    /**
     * @ORM\OneToMany(targetEntity=UploadedProduct::class, mappedBy="uploadTask", orphanRemoval=true)
     * @Groups({"status"})
     */
    private $uploadedProducts;

    public function __construct()
    {
        $this->uploadedProducts = new ArrayCollection();
        $this->created_at = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getCompletedAt(): ?\DateTimeInterface
    {
        return $this->completed_at;
    }

    public function setCompletedAt(\DateTimeInterface $completed_at): self
    {
        $this->completed_at = $completed_at;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getUploadedProductsCount(): ?int
    {
        return $this->uploadedProductsCount;
    }

    public function setUploadedProductsCount(int $uploadedProductsCount): self
    {
        $this->uploadedProductsCount = $uploadedProductsCount;

        return $this;
    }

    /**
     * @return Collection|UploadedProduct[]
     */
    public function getUploadedProducts(): Collection
    {
        return $this->uploadedProducts;
    }

    public function addUploadedProduct(UploadedProduct $uploadedProduct): self
    {
        if (!$this->uploadedProducts->contains($uploadedProduct)) {
            $this->uploadedProducts[] = $uploadedProduct;
            $uploadedProduct->setUploadTask($this);
        }

        return $this;
    }

    public function removeUploadedProduct(UploadedProduct $uploadedProduct): self
    {
        if ($this->uploadedProducts->contains($uploadedProduct)) {
            $this->uploadedProducts->removeElement($uploadedProduct);
            // set the owning side to null (unless already changed)
            if ($uploadedProduct->getUploadTask() === $this) {
                $uploadedProduct->setUploadTask(null);
            }
        }

        return $this;
    }
}
