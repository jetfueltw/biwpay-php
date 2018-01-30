<?php

namespace Jetfuel\Biwpay;

use Jetfuel\Biwpay\HttpClient\GuzzleHttpClient;

class Payment
{
    const BASE_API_URL   = 'http://api.biwpay.com/';

    /**
     * @var string
     */
    protected $merchantId;

    /**
     * @var string
     */
    protected $secretKey;

    /**
     * @var string
     */
    protected $baseApiUrl;

    /**
     * @var \Jetfuel\Biwpay\HttpClient\HttpClientInterface
     */
    protected $httpClient;

    /**
     * Payment constructor.
     *
     * @param string $merchantId
     * @param string $secretKey
     * @param null|string $baseApiUrl
     */
    protected function __construct($merchantId, $secretKey, $baseApiUrl = null)
    {
        $this->merchantId = $merchantId;
        $this->secretKey = $secretKey;
        $this->baseApiUrl = $baseApiUrl === null ? self::BASE_API_URL : $baseApiUrl;

        $this->httpClient = new GuzzleHttpClient($this->baseApiUrl);
    }

    /**
     * Sign request payload.
     *
     * @param array $payload
     * @return array
     */
    protected function signPayload(array $payload)
    {
        $payload['mer_no'] = $this->merchantId;
        $payload['sign'] = Signature::generate($payload, $this->secretKey);
        $payload['sign_type'] = 'MD5';

        return $payload;
    }

}
