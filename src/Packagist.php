<?php

namespace Godruoyi\Packagist;

use GuzzleHttp\Client;
use InvalidArgumentException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

final class Packagist
{
    protected $savePath;

    protected $keyword;

    protected $page = 0;

    protected $applicationId;

    protected $apiKey;

    protected $exceptKeywords = [];

    protected $defaultUrl = '';

    /**
     * Register Client Instance
     * 
     * @param string $applicationId
     * @param string $apiKey       
     */
    public function __construct($applicationId, $apiKey, $savePath = null, $requestUrl = '')
    {
        $this->applicationId = $applicationId;
        $this->apiKey = $apiKey;

        $this->defaultUrl = $requestUrl;
        $this->savePath = $savePath;
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
            'body'  => $this->buildFormData(),
            'verify'=> false
        ];


        echo "start search,the keyword: '{$keyword}', total: '{$total}'\r\n";

        try {
            $result = $this->getHttpClient()->request('POST', $this->getRequestUrl(), $requestData);

            return $this->write($this->parseToArray($result));
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            if ($e->hasResponse()) {
                $response = (string) $e->getResponse();
            } else {
                $response = $e->getMessage();
            }

            echo "Request failure,response: \r\n{$response}\r\n\r\n";
        }
    }

    /**
     * Write Result to path
     * 
     * @param  array  $result
     * 
     * @return bool
     */
    public function write(array $result)
    {
        echo "Fetch done,start write...\r\n";

        $results = $result['results']['hits'] ?? []; 

        $fs = new Filesystem();

        if (! $fs->exists($path = $this->getResultSavePath())) {
            echo "File not exists.\r\n";
            exit;
        }

        foreach ($results as $result) {

            $name = $result['name'];
            $description = $result['description'];
            $repository = $result['repository'];
            $downloads = $result['meta']['downloads'];
            $favers = $result['meta']['favers'];

            echo "{$name} -- \r\n";

            $str = "{$name} - {$description} - [{$name}]({$repository}) - {$downloads} - {$favers}\r\n\r\n\r\n\r\n";

            $fs->appendToFile($path, $str);
        }
    }

    /**
     * Parse Response To array
     * 
     * @param  GuzzleHttp\Psr7\Response $response
     * 
     * @return array
     */
    public function parseToArray(\GuzzleHttp\Psr7\Response $response)
    {
        $body = (string) $response->getBody();

        $result = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "Formatted json failed.\r\n";
            exit;
        }

        return $result;
    }

    /**
     * Build Request Body Form data
     * 
     * @return string
     */
    protected function buildFormData (): string
    {
        $keyword = $this->keyword;
        $facetFilters = urlencode(json_encode([["tags:{$keyword}"]]));

        return '{"requests":[{"indexName":"packagist","params":"facetFilters='.$facetFilters.'"}]}';
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
     * Get Save Path
     * 
     * @return string
     */
    protected function getResultSavePath(): string
    {
        return $this->savePath ?: $this->getDefaultResultFilePath();
    }

    /**
     * Get Request url
     * 
     * @return string
     */
    protected function getRequestUrl(): string
    {
        return empty($this->defaultUrl) ? 'https://m58222sh95-2.algolianet.com/1/indexes/*/queries' : $this->defaultUrl;
    }

    /**
     * Get Default Result file path
     * 
     */
    protected function getDefaultResultFilePath (): string
    {
        return __DIR__ . '/../result.md';
    }
}