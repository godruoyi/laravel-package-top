<?php

namespace Godruoyi\Packagist;

use GuzzleHttp\Client;
use InvalidArgumentException;

final class Packagist
{
    protected $config;

    protected $keyword;

    protected $page = 0;

    protected $applicationId;

    protected $apiKey;

    protected $exceptKeywords = [];

    protected $defaultUrl = 'https://m58222sh95-2.algolianet.com/1/indexes/*/queries';

    /**
     * Register Client Instance
     * 
     * @param string $applicationId
     * @param string $apiKey       
     */
    public function __construct($applicationId, $apiKey)
    {
        $this->applicationId = $applicationId;
        $this->apiKey = $apiKey;
    }

    /**
     * Search top from Packagist
     * 
     * @param  string $keyword
     * @param  int $total
     * 
     * @return array
     */
    public function search($keyword = 'laravel', $total = 100)
    {
        $this->keyword = $keyword;

        $requestData = [
            'headers' => ['content-type' => 'application/json'],
            'query' => $this->buildQueryString(),
            'body'  => json_encode($this->buildFormData(), JSON_UNESCAPED_UNICODE)
        ];

        var_dump($requestData);
        $result = $this->getHttpClient()->request('POST', $this->defaultUrl, $requestData);

        var_dump($result);
    }


    public function write(array $result, $path)
    {
        $path = $path ?: $this->getDefaultResultFilePath ();
    }

    /**
     * Set Request Url
     * 
     * @param string $url
     *
     * @return static
     */
    public function setUrl($url)
    {
        $this->defaultUrl = $url;

        return $this;
    }

    /**
     * Build Request Body Form data
     * 
     * @return array
     */
    protected function buildFormData (): array
    {
        return [
            'requests' => [
                [
                    'indexName' => 'packagist',
                    'params' => [
                        'facetFilters' => [
                            [http_build_query(['tags' => $this->keyword])]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Build Request Query String
     * 
     * @return array
     */
    protected function buildQueryString (): array
    {
        return [
            'x-algolia-application-id' => $this->applicationId,
            'x-algolia-api-key' => $this->apiKey,
        ];
    }

    /**
     * Get Http Client Instance
     * 
     * @return \GuzzleHttp\Client
     */
    protected function getHttpClient ()
    {
        return new Client ();
    }

    /**
     * Get Default Result file path
     * 
     */
    protected function getDefaultResultFilePath (): string
    {
        return __DIR__ . '/result.md';
    }
}