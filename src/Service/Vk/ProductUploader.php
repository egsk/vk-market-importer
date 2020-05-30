<?php


namespace App\Service\Vk;


use App\Entity\VkProduct;
use App\Service\Vk\DTO\ProductRepresentation;
use App\Service\Vk\DTO\VkUploadResult;
use Doctrine\Common\Collections\Collection;
use GuzzleHttp\Client;
use VK\Client\VKApiClient;

class ProductUploader
{
    /**
     * @var VKApiClient
     */
    protected $vkApiClient;
    /**
     * @var string
     */
    protected $accessToken;
    /**
     * @var int
     */
    protected $ownerId;
    /**
     * @var string
     */
    protected $vkProductClass;
    /**
     * @var string
     */
    protected $defaultPhotoUrl;
    /**
     * @var Client
     */
    protected $httpClient;
    /**
     * @var array
     */
    protected $albums;

    public function __construct(string $defaultPhotoUrl)
    {
        $this->defaultPhotoUrl = $defaultPhotoUrl;
        $this->vkApiClient = new VKApiClient();
        $this->httpClient = new Client();
    }

    /**
     * @param string $accessToken
     * @param int $ownerId
     * @param ProductRepresentation[] $productRepresentations
     * @param VkProduct[]|Collection $previouslyUploadedProducts
     * @param string $vkProductClass
     */
    public function upload(
        string $accessToken,
        int $ownerId,
        array $productRepresentations,
        array $previouslyUploadedProducts = [],
        string $vkProductClass = VkProduct::class
    )
    {
        $result = new VkUploadResult();
        $this->accessToken = $accessToken;
        $this->ownerId = -abs($ownerId);
        $this->vkProductClass = $vkProductClass;
        $representationsMap = [];
        $productsMap = [];
        foreach ($productRepresentations as $representation) {
            $representationsMap[$representation->getSourceId()] = $representation;
        }

        foreach ($previouslyUploadedProducts as $product) {
            $productsMap[$product->getSourceId()] = $product;
        }
        $updatingRepresentation = array_intersect_key($representationsMap, $productsMap);
        $productsToUpdate = array_intersect_key($productsMap, $updatingRepresentation);
        if ($productsToUpdate) {
            $updated = $this->updateProducts($updatingRepresentation, $productsToUpdate);
            $result->setUpdated($updated);
        }
        $creationRepresentations = array_diff_key($representationsMap, $updatingRepresentation);
        $created = $this->createProducts($creationRepresentations);
        $result->setCreated($created);

        return $result;
    }

    /**
     * @param ProductRepresentation[]
     * @return VkProduct[]
     */
    protected function createProducts(array $representations): array
    {
        $uploadedProducts = [];
        foreach ($representations as $representation) {
            $product = $this->createProductEntity($representation);
            $photoId = $this->uploadPhoto($product->getPhotoUrl());
            $product->setPhotoId($photoId);
            $params = [
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'category_id' => $product->getCategoryId(),
                'price' => $product->getPrice(),
                'main_photo_id' => $photoId,
                'owner_id' => $this->ownerId
            ];
            if ($url = $product->getUrl()) {
                $params['url'] = $url;
            }
            $uploadedProductId = $this->vkApiClient
                ->market()
                ->add($this->accessToken, $params);
            $product->setVkMarketId($uploadedProductId['market_item_id']);
            $uploadedProducts[] = $product;
            if (!$product->getAlbumName()) {
                continue;
            }
            $this->createAlbumIfNotExists($product->getAlbumName());
            $this->vkApiClient
                ->market()
                ->addToAlbum($this->accessToken, [
                    'owner_id' => $this->ownerId,
                    'item_id' => $uploadedProductId,
                    'album_ids' => $this->getAlbums()[$product->getAlbumName()]
                ]);
            sleep(1);
        }

        return $uploadedProducts;
    }

    /**
     * @param ProductRepresentation[] $updatingRepresentation
     * @param VkProduct[] $productsToUpdate
     * @return VkProduct[]
     */
    protected function updateProducts(array $updatingRepresentation, array $productsToUpdate): array
    {
        $updatedProducts = [];
        foreach ($updatingRepresentation as $key => $representation) {
            $product = $productsToUpdate[$key];
            $oldPhotoUrl = $product->getPhotoUrl();
            $oldAlbumName = $product->getAlbumName();
            $product = $this->createProductEntity($representation, $productsToUpdate[$key]);
            $params = [];
            if ($product->getPhotoUrl() !== $oldPhotoUrl) {
                $params['main_photo_id'] = $this->uploadPhoto($product->getPhotoUrl());
            }
            if ($product->getAlbumName() && $product->getAlbumName() !== $oldAlbumName) {
                $this->createAlbumIfNotExists($product->getAlbumName());
                $this->vkApiClient
                    ->market()
                    ->removeFromAlbum($this->accessToken, [
                        'owner_id' => $this->ownerId,
                        'item_id' => $product->getVkMarketId(),
                        'album_ids' => $this->getAlbums()[$oldAlbumName]
                    ]);
                $this->vkApiClient
                    ->market()
                    ->addToAlbum($this->accessToken, [
                        'owner_id' => $this->ownerId,
                        'item_id' => $product->getVkMarketId(),
                        'album_ids' => $this->getAlbums()[$product->getAlbumName()]
                    ]);
            }
            $this->vkApiClient
                ->market()
                ->edit($this->accessToken, array_merge([
                    'owner_id' => $this->ownerId,
                    'item_id' => $product->getVkMarketId(),
                    'name' => $product->getName(),
                    'description' => $product->getDescription(),
                    'categoryId' => $product->getCategoryId(),
                    'price' => $product->getPrice(),
                ], $params));
            sleep(1);

            $updatedProducts[] = $product;
        }

        return $updatedProducts;
    }


    protected function uploadPhoto($photoUrl = null): int
    {
        if (!$photoUrl) {
            $photoUrl = $this->defaultPhotoUrl;
        }
        $image = fopen($photoUrl, 'r');
        $uploadServer = $this->vkApiClient
            ->photos()
            ->getMarketUploadServer($this->accessToken, [
                'group_id' => -$this->ownerId,
                'main_photo' => 1,
            ]);
        $result = $this->httpClient->request('POST', $uploadServer['upload_url'], [
            'multipart' => [
                [
                    'name' => 'file',
                    'contents' => $image
                ]
            ]
        ])->getBody()->getContents();
        $result = json_decode($result, true);
        $photo = $this->vkApiClient
            ->photos()
            ->saveMarketPhoto($this->accessToken, array_merge([
                'group_id' => -$this->ownerId
            ], $result));

        return $photo[0]['id'];
    }

    /**
     * @param ProductRepresentation $representation
     * @param VkProduct|null $vkProduct
     * @return VkProduct
     */
    protected function createProductEntity(ProductRepresentation $representation, $vkProduct = null)
    {
        if (is_null($vkProduct)) {
            /**
             * @var VkProduct $vkProduct
             */
            $vkProduct = new $this->vkProductClass();
        }
        $vkProduct->setName($representation->getName());
        $vkProduct->setPrice($representation->getPrice());
        $vkProduct->setCategoryId($representation->getCategoryId());
        $vkProduct->setDescription($representation->getDescription());
        $vkProduct->setSourceId($representation->getSourceId());
        $vkProduct->setUrl($representation->getUrl());
        $vkProduct->setAlbumName($representation->getAlbumName());
        $vkProduct->setOwnerId($this->ownerId);
        $vkProduct->setPhotoUrl($representation->getPhotoUrl());

        return $vkProduct;
    }

    protected function getAlbums()
    {
        if (!$this->albums) {
            $this->albums = $this->loadAlbums();
        }

        return $this->albums;
    }

    protected function createAlbumIfNotExists(string $name)
    {
        if (array_key_exists($name, $this->getAlbums())) {
            return;
        }
        $album = $this->vkApiClient
            ->market()
            ->addAlbum($this->accessToken, [
                'owner_id' => $this->ownerId,
                'title' => $name
            ]);
        $this->albums[$name] = $album['market_album_id'];
    }

    protected function loadAlbums(): array
    {
        $offset = 0;
        $count = 100;
        $albums = [];
        while (true) {
            $response = $this->vkApiClient
                ->market()
                ->getAlbums($this->accessToken, [
                    'owner_id' => $this->ownerId,
                    'count' => $count,
                    'offset' => $offset,
                ]);
            $albums = array_merge($albums, $response['items']);
            $offset += $count;
            if ($response['count'] <= $offset) {
                break;
            }
        }
        $result = [];
        foreach ($albums as $album) {
            $result[$album['title']] = $album['id'];
        }

        return $result;
    }

}