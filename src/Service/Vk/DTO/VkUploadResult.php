<?php


namespace App\Service\Vk\DTO;


use App\Entity\VkProduct;

class VkUploadResult
{
    /**
     * @var VkProduct|null
     */
    protected $vkProduct;
    /**
     * @var string
     */
    protected $status;

    /**
     * @return VkProduct|null
     */
    public function getVkProduct(): ?VkProduct
    {
        return $this->vkProduct;
    }

    /**
     * @param VkProduct|null $vkProduct
     * @return $this
     */
    public function setVkProduct(?VkProduct $vkProduct): self
    {
        $this->vkProduct = $vkProduct;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

}