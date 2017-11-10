<?php

namespace Godruoyi\Packagist\Service;

use Exception;
use GuzzleHttp\Client;

final class YoudaoTranslate
{
    const API_HTTPS = 'https://openapi.youdao.com/api';

    protected $appId;

    protected $secret;

    protected $keyword;

    public function __construct(string $appId, string $secret)
    {
        $this->appId = $appId;
        $this->secret = $secret;
    }

    public function trans(string $keyword, string $form = 'EN', string $to = 'zh-CHS'): string
    {
        $keyword = trim($keyword);
        $this->keyword = $keyword;

        try {
            return $this->toString($this->getHttpClient()->get(self::API_HTTPS, $this->buildRequestParam($keyword, $form, $to)));
        } catch (Exception $e) {
            var_dump($e->getMessage());
            return $keyword;
        }
    }

    public function toString($response)
    {
        if ($response instanceof \GuzzleHttp\Psr7\Response) {
            $response = json_decode((string) $response->getBody(), true);

            if (json_last_error() === JSON_ERROR_NONE && ! empty($response['translation'][0])) {
                return $response['translation'][0];
            }
        }

        return $this->keyword;
    }

    /**
     * Get Http Client Instance
     *
     * @return \GuzzleHttp\Client
     */
    protected function getHttpClient()
    {
        return new Client();
    }

    public function buildRequestParam(string $keyword, string $form = 'en', string $to = 'zh')
    {
        return [
            // 'headers' => ['content-type' => 'application/json'],
            'query' => [
                'q'     => $keyword = $this->toUTF8($keyword),
                'from'  => $form,
                'to'    => $to,
                'appKey' => $this->appId,
                'salt'  => $salt = mt_rand(10086, 99999),
                'sign'  => $this->sign($keyword, $salt)
            ],
            'verify'=> false,
        ];
    }

    public function toUTF8(string $word)
    {
        return mb_convert_encoding($word, 'UTF-8', 'auto');
    }

    public function sign(string $keyword, string $salt): string
    {
        return md5(join('', [
            $this->appId,
            $keyword,
            $salt,
            $this->secret,
        ]));
    }
}
