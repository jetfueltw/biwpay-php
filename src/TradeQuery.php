<?php

namespace Jetfuel\Biwpay;

use Jetfuel\Biwpay\Traits\ResultParser;

class TradeQuery extends Payment
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
     * Find Order by trade number.
     *
     * @param string $tradeNo
     * @return array|null
     */
    public function find($tradeNo)
    {
        $payload = $this->signPayload([
            'mer_order_no' => $tradeNo,
        ]);

        $order = $this->parseResponse($this->httpClient->post('query/order/doquery', $payload));
       
        $successOrder = array('0', '1'); // From real test, if order not found, $order['trade_result] will return '-2'
        if ($order['auth_result'] !== 'SUCCESS'|| !in_array($order['trade_result'], $successOrder, true)) {
            return null;
        }

        return $order;
    }

    /**
     * Is order already paid.
     *
     * @param string $tradeNo
     * @return bool
     */
    public function isPaid($tradeNo)
    {
        $order = $this->find($tradeNo);

        if ($order === null || !isset($order['trade_result']) || $order['trade_result'] !== '1') {
            return false;
        }

        return true;
    }
}
