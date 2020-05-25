<?php

namespace App\Entity;

use App\Repository\ImportTargetRepository;
use App\Service\Vk\DTO\Group;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ImportTargetRepository::class)
 */
class ImportTarget
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $groupId;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="importTargets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=VkMarketCategory::class, inversedBy="importTargets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $vkMarketCategory;

    /**
     * @var Group
     */
    private $group;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $groupName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $groupPhoto;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGroupId(): ?int
    {
        return $this->groupId;
    }

    public function setGroupId($groupId): self
    {
        $this->groupId = $groupId;

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

    public function getVkMarketCategory(): ?VkMarketCategory
    {
        return $this->vkMarketCategory;
    }

    public function setVkMarketCategory(?VkMarketCategory $vkMarketCategory): self
    {
        $this->vkMarketCategory = $vkMarketCategory;

        return $this;
    }

    public function setGroup(Group $group): self
    {
        $this->groupId = $group->getId();
        $this->groupPhoto = $group->getPhoto();
        $this->groupName = $group->getName();

        return $this;
    }

    public function getGroup(): Group
    {
        return (new Group())
            ->setId($this->groupId)
            ->setName($this->groupName)
            ->setPhoto($this->groupPhoto);
    }

    public function getGroupName(): ?string
    {
        return $this->groupName;
    }

    public function setGroupName(string $groupName): self
    {
        $this->groupName = $groupName;

        return $this;
    }

    public function getGroupPhoto(): ?string
    {
        return $this->groupPhoto;
    }

    public function setGroupPhoto(string $groupPhoto): self
    {
        $this->groupPhoto = $groupPhoto;

        return $this;
    }
}
