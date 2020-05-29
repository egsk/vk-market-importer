<?php


namespace App\Service\Vk;


use App\Service\Vk\DTO\Group;
use App\Service\Vk\DTO\MarketCategory;
use Exception;
use Psr\Log\LoggerInterface;
use VK\Client\VKApiClient;


class VkManager
{
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var VKApiClient
     */
    protected $vkApiClient;

    public function __construct(LoggerInterface $logger)
    {
        $this->vkApiClient = new VKApiClient();
        $this->logger = $logger;
    }

    /**
     * @param string $accessToken
     * @return Group[]
     */
    public function getGroupsList(string $accessToken)
    {
        try {
            $response = $this->vkApiClient
                ->groups()
                ->get($accessToken, [
                    'filter' => 'admin',
                    'extended' => 1,
                    'count' => 1000
                ]);
            $rawGroups = $response['items'];
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());

            return [];
        }

        $groups = [];
        foreach ($rawGroups as $group) {
            $groups[] = (new Group())
                ->setId($group['id'])
                ->setName($group['name'])
                ->setPhoto($group['photo_50']);
        }

        return $groups;
    }

    /**
     * @param string $accessToken
     * @return array
     */
    public function getMarketCategoriesList(string $accessToken)
    {
        try {
            $response = $this->vkApiClient
                ->market()
                ->getCategories($accessToken, [
                    'filter' => 'admin',
                    'extended' => 1,
                    'count' => 1000
                ]);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());

            return [];
        }

        return $response['items'];
    }


}