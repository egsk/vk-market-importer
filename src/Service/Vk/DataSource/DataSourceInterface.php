<?php


namespace App\Service\Vk\DataSource;


use App\Entity\ImportTarget;
use App\Entity\User;
use Doctrine\Common\Collections\Collection;

interface DataSourceInterface
{
    public function getId(): ?int;

    public function getUser(): ?User;

    public function getImportTarget(): ?ImportTarget;

    public function getVkProducts(): Collection;
}