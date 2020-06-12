<?php


namespace App\Service\Vk;


use App\Entity\UploadedProduct;
use App\Entity\VkProduct;
use App\Service\Vk\DTO\ProductRepresentation;
use App\Service\Vk\DTO\VkUploadResult;
use GuzzleHttp\Client;
use VK\Client\VKApiClient;
use VK\Exceptions\Api\VKApiAccessMarketException;
use VK\Exceptions\Api\VKApiMarketAlbumNotFoundException;
use VK\Exceptions\Api\VKApiMarketGroupingItemsMustHaveDistinctPropertiesException;
use VK\Exceptions\Api\VKApiMarketGroupingMustContainMoreThanOneItemException;
use VK\Exceptions\Api\VKApiMarketItemAlreadyAddedException;
use VK\Exceptions\Api\VKApiMarketItemHasBadLinksException;
use VK\Exceptions\Api\VKApiMarketItemNotFoundException;
use VK\Exceptions\Api\VKApiMarketPropertyNotFoundException;
use VK\Exceptions\Api\VKApiMarketTooManyAlbumsException;
use VK\Exceptions\Api\VKApiMarketTooManyItemsException;
use VK\Exceptions\Api\VKApiMarketTooManyItemsInAlbumException;
use VK\Exceptions\Api\VKApiMarketVariantNotFoundException;
use VK\Exceptions\Api\VKApiParamHashException;
use VK\Exceptions\Api\VKApiParamPhotoException;
use VK\Exceptions\VKApiException;
use VK\Exceptions\VKClientException;

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

    /**
     * @var ImageHandler
     */
    protected $imageHandler;

    public function __construct(string $defaultPhotoUrl, ImageHandler $imageHandler)
    {
        $this->defaultPhotoUrl = $defaultPhotoUrl;
        $this->vkApiClient = new VKApiClient();
        $this->httpClient = new Client();
        $this->imageHandler = $imageHandler;
    }

    /**
     * @param string $accessToken
     * @param int $ownerId
     * @param ProductRepresentation $productRepresentation
     * @param VkProduct|null $previouslyUploadedProduct ,
     * @param string $vkProductClass
     * @return VkUploadResult
     */
    public function handleRepresentation
    (
        string $accessToken,
        int $ownerId,
        ProductRepresentation $productRepresentation,
        ?VkProduct $previouslyUploadedProduct,
        string $vkProductClass = VkProduct::class
    ): VkUploadResult
    {
        $result = new VkUploadResult();
        $this->accessToken = $accessToken;
        $this->ownerId = -abs($ownerId);
        $this->vkProductClass = $vkProductClass;

        if (!is_null($previouslyUploadedProduct)) {
            try {
                $product = $this->updateProduct($productRepresentation, $previouslyUploadedProduct);
                $status = $productRepresentation->getStatus() ?
                    UploadedProduct::STATUS_UPDATED :
                    UploadedProduct::STATUS_DELETED;
            } catch (\Exception $exception) {
                $product = null;
                $status = $productRepresentation->getStatus() ?
                    UploadedProduct::STATUS_FAILED_TO_UPDATE :
                    UploadedProduct::STATUS_FAILED_TO_DELETE;
            }
        } else {
            try {
                $product = $this->createProduct($productRepresentation);
                $status = $productRepresentation->getStatus() ?
                    UploadedProduct::STATUS_CREATED :
                    UploadedProduct::STATUS_DELETED;
            } catch (\Exception $exception) {
                $product = null;
                $status = $productRepresentation->getStatus() ?
                    UploadedProduct::STATUS_FAILED_TO_CREATE :
                    UploadedProduct::STATUS_FAILED_TO_DELETE;
            }
        }
        $result->setVkProduct($product);
        $result->setStatus($status);

        return $result;
    }

    /**
     * @param ProductRepresentation $representation
     * @return VkProduct|null
     * @throws VKApiAccessMarketException
     * @throws VKApiException
     * @throws VKApiMarketAlbumNotFoundException
     * @throws VKApiMarketItemAlreadyAddedException
     * @throws VKApiMarketItemHasBadLinksException
     * @throws VKApiMarketItemNotFoundException
     * @throws VKApiMarketTooManyItemsException
     * @throws VKApiMarketTooManyItemsInAlbumException
     * @throws VKClientException
     */
    protected function createProduct(ProductRepresentation $representation): ?VkProduct
    {
        if (!$representation->getStatus()) {
            return null;
        }
        $product = $this->createProductEntity($representation);
        $this->uploadProduct($product);


        return $product;
    }

    /**
     * @param VkProduct $product
     * @throws VKApiAccessMarketException
     * @throws VKApiMarketAlbumNotFoundException
     * @throws VKApiMarketItemAlreadyAddedException
     * @throws VKApiMarketItemHasBadLinksException
     * @throws VKApiMarketItemNotFoundException
     * @throws VKApiMarketTooManyItemsException
     * @throws VKApiMarketTooManyItemsInAlbumException
     * @throws VKApiException
     * @throws VKClientException
     */
    protected function uploadProduct($product)
    {
        $photoId = $this->createPhoto($product->getPhotoUrl());
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
            return;
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

    /**
     * @param ProductRepresentation $representation
     * @param VkProduct $productToUpdate
     * @return VkProduct
     * @throws VKApiAccessMarketException
     * @throws VKApiException
     * @throws VKApiMarketAlbumNotFoundException
     * @throws VKApiMarketItemAlreadyAddedException
     * @throws VKApiMarketItemHasBadLinksException
     * @throws VKApiMarketItemNotFoundException
     * @throws VKApiMarketTooManyItemsException
     * @throws VKApiMarketTooManyItemsInAlbumException
     * @throws VKClientException
     * @throws VKApiMarketGroupingItemsMustHaveDistinctPropertiesException
     * @throws VKApiMarketGroupingMustContainMoreThanOneItemException
     * @throws VKApiMarketPropertyNotFoundException
     * @throws VKApiMarketVariantNotFoundException
     */
    protected function updateProduct(ProductRepresentation $representation, VkProduct $productToUpdate): VkProduct
    {
        $oldPhotoUrl = $productToUpdate->getPhotoUrl();
        $oldAlbumName = $productToUpdate->getAlbumName();
        $oldStatus = $productToUpdate->getStatus();
        $productToUpdate = $this->createProductEntity($representation, $productToUpdate);
        $params = [];
        if (!$productToUpdate->getStatus() && $oldStatus) {
            $this->vkApiClient
                ->market()
                ->delete($this->accessToken, [
                    'owner_id' => $this->ownerId,
                    'item_id' => $productToUpdate->getVkMarketId()
                ]);
            $productToUpdate->setVkMarketId(-1);
            sleep(1);

            return $productToUpdate;
        }
        if ($productToUpdate->getStatus() && !$oldStatus) {
            $this->uploadProduct($productToUpdate);

            return $productToUpdate;
        }
        if ($productToUpdate->getPhotoUrl() !== $oldPhotoUrl) {
            $params['main_photo_id'] = $this->createPhoto($productToUpdate->getPhotoUrl());
        }
        if ($productToUpdate->getAlbumName() && $productToUpdate->getAlbumName() !== $oldAlbumName) {
            $this->createAlbumIfNotExists($productToUpdate->getAlbumName());
            $this->vkApiClient
                ->market()
                ->removeFromAlbum($this->accessToken, [
                    'owner_id' => $this->ownerId,
                    'item_id' => $productToUpdate->getVkMarketId(),
                    'album_ids' => $this->getAlbums()[$oldAlbumName]
                ]);
            $this->vkApiClient
                ->market()
                ->addToAlbum($this->accessToken, [
                    'owner_id' => $this->ownerId,
                    'item_id' => $productToUpdate->getVkMarketId(),
                    'album_ids' => $this->getAlbums()[$productToUpdate->getAlbumName()]
                ]);
        }
        $this->vkApiClient
            ->market()
            ->edit($this->accessToken, array_merge([
                'owner_id' => $this->ownerId,
                'item_id' => $productToUpdate->getVkMarketId(),
                'name' => $productToUpdate->getName(),
                'description' => $productToUpdate->getDescription(),
                'categoryId' => $productToUpdate->getCategoryId(),
                'price' => $productToUpdate->getPrice(),
            ], $params));
        sleep(1);

        return $productToUpdate;
    }

    protected function createPhoto($photoUrl = null): int
    {
        if (!$photoUrl) {
            $photoUrl = $this->defaultPhotoUrl;
        }
        try {
            $path = $this->imageHandler->prepareImage($photoUrl);
        } catch (\Exception $e) {
            $path = $this->defaultPhotoUrl;
        }
        try {
            $image = fopen($path, 'r');
            $photo = $this->uploadImage($image);
        } catch (\Exception $e) {
            $image = fopen($this->defaultPhotoUrl, 'r');
            $photo = $this->uploadImage($image);
        }
        $this->imageHandler->clear();

        return $photo[0]['id'];
    }

    /**
     * @param $image
     * @return mixed
     * @throws VKApiException
     * @throws VKClientException
     * @throws VKApiParamHashException
     * @throws VKApiParamPhotoException
     */
    protected function uploadImage($image)
    {
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

        return $this->vkApiClient
            ->photos()
            ->saveMarketPhoto($this->accessToken, array_merge([
                'group_id' => -$this->ownerId
            ], $result));
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
        $vkProduct->setStatus($representation->getStatus());

        return $vkProduct;
    }

    /**
     * @return array
     * @throws VKApiException
     * @throws VKClientException
     */
    protected function getAlbums()
    {
        if (!$this->albums) {
            $this->albums = $this->loadAlbums();
        }

        return $this->albums;
    }

    /**
     * @param string $name
     * @throws VKApiException
     * @throws VKClientException
     * @throws VKApiMarketTooManyAlbumsException
     */
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

    /**
     * @return array
     * @throws VKApiException
     * @throws VKClientException
     */
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