<?php


namespace App\Service\Vk;


use GuzzleHttp\Client;

class VkOAuthProvider
{
    protected const OAUTH_URL = 'https://oauth.vk.com/authorize';
    protected const ACCESS_TOKEN_URL = 'https://oauth.vk.com/access_token';

    /**
     * @var int
     */
    protected $appId;
    /**
     * @var string
     */
    protected $appPrivateKey;
    /**
     * @var string
     */
    protected $appServiceKey;
    /**
     * @var string
     */
    protected $appRedirectUri;

    /**
     * VkAuthManager constructor.
     * @param string $appId
     * @param string $appPrivateKey
     * @param string $appServiceKey
     * @param string $appRedirectUri
     */
    public function __construct(string $appId, string $appPrivateKey, string $appServiceKey, string $appRedirectUri)
    {
        $this->appId = $appId;
        $this->appPrivateKey = $appPrivateKey;
        $this->appServiceKey = $appServiceKey;
        $this->appRedirectUri = $appRedirectUri;
    }

    public function createOAuthURL()
    {
        $params = [
            'client_id' => $this->appId,
            'redirect_uri' => $this->appRedirectUri,
            'display' => 'popup',
            'response_type' => 'code',
            'scope' => 'offline,groups,market,photos'
        ];
        $query = http_build_query($params);

        return static::OAUTH_URL . '?' . $query;
    }

    /**
     * @param string $code
     * @return AccessTokenResponse
     */
    public function createAccessTokenResponse(string $code): AccessTokenResponse
    {
        $params = [
            'client_id' => $this->appId,
            'client_secret' => $this->appPrivateKey,
            'redirect_uri' => $this->appRedirectUri,
            'code' => $code
        ];
        $query = http_build_query($params);
        $url = static::ACCESS_TOKEN_URL . '?' . $query;
        $rawResponse = (new Client())->request('GET', $url)->getBody()->getContents();
        $response = json_decode($rawResponse, true);

        return (new AccessTokenResponse())
            ->setAccessToken($response['access_token'])
            ->setUserId($response['user_id']);
    }
}