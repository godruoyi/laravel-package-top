<?php

final class Packagist
{
    const REQUEST_API = 'https://m58222sh95-2.algolianet.com/1/indexes/*/queries';

    protected $applicationId;

    protected $apiKey;

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

    public function fetch($total)
    {
        # code...
    }
}