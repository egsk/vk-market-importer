<?php

namespace App\Entity;

use App\Repository\UploadedProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=UploadedProductRepository::class)
 */
class UploadedProduct
{
    public const STATUS_CREATED = 'created';
    public const STATUS_FAILED_TO_CREATE = 'failed_to_create';

    public const STATUS_UPDATED = 'updated';
    public const STATUS_FAILED_TO_UPDATE = 'failed_to_update';

    public const STATUS_DELETED = 'deleted';
    public const STATUS_FAILED_TO_DELETE = 'failed_to_delete';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"status"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=UploadTask::class, inversedBy="uploadedProducts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $uploadTask;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"status"})
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"status"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"status"})
     */
    private $sourceId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUploadTask(): ?UploadTask
    {
        return $this->uploadTask;
    }

    public function setUploadTask(?UploadTask $uploadTask): self
    {
        $this->uploadTask = $uploadTask;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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
}
