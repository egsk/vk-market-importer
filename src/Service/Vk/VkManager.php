<?php


namespace App\Service\Vk;


use App\Entity\User;
use VK\Client\VKApiClient;


class VkManager
{
    /**
     * @var VKApiClient
     */
    protected $vkApiClient;

    public function __construct(VKApiClient $vkApiClient)
    {
        $this->vkApiClient = $vkApiClient;
    }

    public function getGroupsList(User $user)
    {
        $this->vkApiClient
            ->groups()
            ->get($user->getVkAccessToken(), [
                'filter' => 'admin',
                'extended' => 1
            ]);
    }


}