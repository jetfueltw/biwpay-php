<?php

namespace Jetfuel\Biwpay;

use Jetfuel\Biwpay\Traits\ResultParser;

class DigitalPayment extends Payment
{
    use ResultParser;

    /**
     * DigitalPayment constructor.
     *
     * @param string $merchantId
     * @param string $secretKey
     * @param null|string $baseApiUrl
     */
    public function __construct($merchantId, $secretKey, $baseApiUrl = null)
    {
        parent::__construct($merchantId, $secretKey, $baseApiUrl);
    }

    /**
     * Create digital payment order.
     *
     * @param string $tradeNo
     * @param int $channel
     * @param float $amount
     * @param string $notifyUrl
     * @return array
     */
    public function order($tradeNo, $channel, $amount, $notifyUrl, $returnUrl)
    {
        $payload = $this->signPayload([
            'mer_order_no'      => $tradeNo,
            'trade_amount'      => $amount,
            'service_type'      => $channel,
            'order_date'        => $this->getCurrentTime(),
            'page_url'          => $returnUrl,
            'back_url'           => $notifyUrl,
        ]);

        return $this->parseResponse($this->httpClient->post('payment/api/scanpay', $payload));
    }
}
