<?php


namespace App\Service\Vk;


use App\Entity\VkMarketCategory;
use Doctrine\ORM\EntityManagerInterface;

class VkMarketCategoryProvider
{
    protected $vkManager;
    protected $entityManager;

    public function __construct(VkManager $vkManager, EntityManagerInterface $entityManager)
    {
        $this->vkManager = $vkManager;
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $accessToken
     * @return VkMarketCategory[]
     */
    public function saveCategories(string $accessToken)
    {
        $repository = $this->entityManager->getRepository(VkMarketCategory::class);
        $localMarketCategories = $repository->findAll();
        $hashMap = [];
        foreach ($localMarketCategories as $localMarketCategory) {
            $hashMap[$localMarketCategory->getId()] = $localMarketCategory;
        }
        $categories = $this->vkManager->getMarketCategoriesList($accessToken);
        foreach ($categories as $category) {
            if (array_key_exists($category['id'], $hashMap)) {
                $hashMap[$category['id']]->setName($category['name']);
                continue;
            }
            $vkMarketCategory = (new VkMarketCategory())
                ->setName($category['name'])
                ->setId($category['id']);
            $hashMap[$vkMarketCategory->getId()] = $vkMarketCategory;
            $this->entityManager->persist($vkMarketCategory);
        }
        $this->entityManager->flush();

        return array_values($hashMap);
    }
}