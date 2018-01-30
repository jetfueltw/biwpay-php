<?php

namespace Jetfuel\Biwpay;

use Jetfuel\Biwpay\Traits\ResultParser;

class BankPayment extends Payment
{
    use ResultParser;

    /**
     * BankPayment constructor.
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
     * Create bank payment order.
     *
     * @param string $tradeNo
     * @param string $bank
     * @param float $amount
     * @param string $notifyUrl
     * @param string $returnUrl
     * @return string
     */
    public function order($tradeNo, $bank, $amount, $notifyUrl, $returnUrl)
    {
        $payload = $this->signPayload([
            
            'mer_order_no'      => $tradeNo,
            'trade_amount'      => $amount,
            'channel_code'      => $bank,
            'service_type'      => 'b2c',
            'order_date'        => $this->getCurrentTime(),
            'page_url'          => $returnUrl,
            'back_url'           => $notifyUrl,
            
        ]);

        return $this->httpClient->post('payment/web/receive', $payload);
    }
}
