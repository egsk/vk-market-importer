<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $vkId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $vkAccessToken;

    /**
     * @ORM\OneToMany(targetEntity=ImportTarget::class, mappedBy="user", orphanRemoval=true)
     */
    private $importTargets;

    /**
     * @ORM\OneToMany(targetEntity=CsvLinkDataSource::class, mappedBy="user", orphanRemoval=true)
     */
    private $csvLinkDataSources;

    /**
     * @ORM\OneToMany(targetEntity=VkProduct::class, mappedBy="user", orphanRemoval=true)
     */
    private $vkProducts;

    /**
     * @ORM\OneToMany(targetEntity=UploadTask::class, mappedBy="user", orphanRemoval=true)
     */
    private $uploadTasks;

    public function __construct()
    {
        $this->importTargets = new ArrayCollection();
        $this->csvLinkDataSources = new ArrayCollection();
        $this->vkProducts = new ArrayCollection();
        $this->uploadTasks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string)$this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getVkId(): ?int
    {
        return $this->vkId;
    }

    public function setVkId(?int $vkId): self
    {
        $this->vkId = $vkId;

        return $this;
    }

    public function getVkAccessToken(): ?string
    {
        return $this->vkAccessToken;
    }

    public function setVkAccessToken(?string $vkAccessToken): self
    {
        $this->vkAccessToken = $vkAccessToken;

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
            $importTarget->setUser($this);
        }

        return $this;
    }

    public function removeImportTarget(ImportTarget $importTarget): self
    {
        if ($this->importTargets->contains($importTarget)) {
            $this->importTargets->removeElement($importTarget);
            // set the owning side to null (unless already changed)
            if ($importTarget->getUser() === $this) {
                $importTarget->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CsvLinkDataSource[]
     */
    public function getCsvLinkDataSources(): Collection
    {
        return $this->csvLinkDataSources;
    }

    public function addCsvLinkDataSource(CsvLinkDataSource $csvLinkDataSource): self
    {
        if (!$this->csvLinkDataSources->contains($csvLinkDataSource)) {
            $this->csvLinkDataSources[] = $csvLinkDataSource;
            $csvLinkDataSource->setUser($this);
        }

        return $this;
    }

    public function removeCsvLinkDataSource(CsvLinkDataSource $csvLinkDataSource): self
    {
        if ($this->csvLinkDataSources->contains($csvLinkDataSource)) {
            $this->csvLinkDataSources->removeElement($csvLinkDataSource);
            // set the owning side to null (unless already changed)
            if ($csvLinkDataSource->getUser() === $this) {
                $csvLinkDataSource->setUser(null);
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
            $vkProduct->setUser($this);
        }

        return $this;
    }

    public function removeVkProduct(VkProduct $vkProduct): self
    {
        if ($this->vkProducts->contains($vkProduct)) {
            $this->vkProducts->removeElement($vkProduct);
            // set the owning side to null (unless already changed)
            if ($vkProduct->getUser() === $this) {
                $vkProduct->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UploadTask[]
     */
    public function getUploadTasks(): Collection
    {
        return $this->uploadTasks;
    }

    public function addUploadTask(UploadTask $uploadTask): self
    {
        if (!$this->uploadTasks->contains($uploadTask)) {
            $this->uploadTasks[] = $uploadTask;
            $uploadTask->setUser($this);
        }

        return $this;
    }

    public function removeUploadTask(UploadTask $uploadTask): self
    {
        if ($this->uploadTasks->contains($uploadTask)) {
            $this->uploadTasks->removeElement($uploadTask);
            // set the owning side to null (unless already changed)
            if ($uploadTask->getUser() === $this) {
                $uploadTask->setUser(null);
            }
        }

        return $this;
    }
}
