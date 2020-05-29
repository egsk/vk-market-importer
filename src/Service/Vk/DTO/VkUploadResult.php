<?php


namespace App\Service\Vk\DTO;


use App\Entity\VkProduct;

class VkUploadResult
{
    /**
     * @var VkProduct[]
     */
    protected $created;
    /**
     * @var VkProduct[]
     */
    protected $updated;

    /**
     * @return VkProduct[]
     */
    public function getCreated(): array
    {
        return $this->created;
    }

    /**
     * @param VkProduct[] $created
     * @return $this
     */
    public function setCreated(array $created): self
    {
        $this->created = $created;

        return $this;
    }

    /**
     * @return VkProduct[]
     */
    public function getUpdated(): array
    {
        return $this->updated;
    }

    /**
     * @param VkProduct[] $updated
     * @return $this
     */
    public function setUpdated(array $updated): self
    {
        $this->updated = $updated;

        return $this;
    }

}