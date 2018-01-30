<?php

namespace Test;

use Faker\Factory;
use Jetfuel\Biwpay\BankPayment;
use Jetfuel\Biwpay\Constants\Bank;
use Jetfuel\Biwpay\Constants\Channel;
use Jetfuel\Biwpay\DigitalPayment;
use Jetfuel\Biwpay\TradeQuery;
use Jetfuel\Biwpay\Traits\NotifyWebhook;
use PHPUnit\Framework\TestCase;

class UnitTest extends TestCase
{
    private $merchantId;
    private $secretKey;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->merchantId = getenv('MERCHANT_ID');
        $this->secretKey = getenv('SECRET_KEY');
    }

    public function testDigitalPaymentOrder()
    {
        $faker = Factory::create();
        $tradeNo = $faker->uuid;
        $channel = Channel::QQ;
        $amount = 1;
        $notifyUrl = $faker->url;
        $returnUrl = $faker->url;

        $payment = new DigitalPayment($this->merchantId, $this->secretKey);
        $result = $payment->order($tradeNo, $channel, $amount, $notifyUrl, $returnUrl);

        var_dump($result);

        $this->assertEquals('3', $result['trade_result']);

        return $tradeNo;
    }

    /**
     * @depends testDigitalPaymentOrder
     *
     * @param $tradeNo
     */
    public function testDigitalPaymentOrderFind($tradeNo)
    {
        $tradeQuery = new TradeQuery($this->merchantId, $this->secretKey);
        $result = $tradeQuery->find($tradeNo);
        var_dump($result);
        $this->assertEquals('00', $result['code']);
    }

    /**
     * @depends testDigitalPaymentOrder
     *
     * @param $tradeNo
     */
    public function testDigitalPaymentOrderIsPaid($tradeNo)
    {
        $tradeQuery = new TradeQuery($this->merchantId, $this->secretKey);
        $result = $tradeQuery->isPaid($tradeNo);

        $this->assertFalse($result);
    }

    public function testBankPaymentOrder()
    {
        $faker = Factory::create();
        $tradeNo = $faker->uuid;
        $bank = Bank::ABC;
        $amount = 2.5;
        $returnUrl = $faker->url;
        $notifyUrl = $faker->url;

        $payment = new BankPayment($this->merchantId, $this->secretKey);
        $result = $payment->order($tradeNo, $bank, $amount, $returnUrl, $notifyUrl);
        var_dump($result);
        $this->assertContains('<form', $result, '', true);

        return $tradeNo;
    }

    /**
     * @depends testBankPaymentOrder
     *
     * @param $tradeNo
     */
    public function testBankPaymentOrderFind($tradeNo)
    {
        $tradeQuery = new TradeQuery($this->merchantId, $this->secretKey);
        $result = $tradeQuery->find($tradeNo);
        var_dump($result);
        $this->assertEquals('SUCCESS', $result['auth_result']);
    }

    /**
     * @depends testBankPaymentOrder
     *
     * @param $tradeNo
     */
    public function testBankPaymentOrderIsPaid($tradeNo)
    {
        $tradeQuery = new TradeQuery($this->merchantId, $this->secretKey);
        $result = $tradeQuery->isPaid($tradeNo);

        $this->assertFalse($result);
    }

    public function testTradeQueryFindOrderNotExist()
    {
        $faker = Factory::create();
        $tradeNo = $faker->uuid;

        $tradeQuery = new TradeQuery($this->merchantId, $this->secretKey);
        $result = $tradeQuery->find($tradeNo);

        $this->assertNull($result);
    }

    public function testTradeQueryIsPaidOrderNotExist()
    {
        $faker = Factory::create();
        $tradeNo = $faker->uuid;

        $tradeQuery = new TradeQuery($this->merchantId, $this->secretKey);
        $result = $tradeQuery->isPaid($tradeNo);

        $this->assertFalse($result);
    }

    public function testNotifyWebhookVerifyNotifyPayload()
    {
        $mock = $this->getMockForTrait(NotifyWebhook::class);

        $payload = [
            'trade_result' => '1',
            'mer_no'       => '390200003',
            'mer_return_msg' => '已付款',
            'mer_order_no'    => '201801290001',
            'notify_type'   => 'back_notify',
            'currency'    => 1,
            'trade_amount' => '1.50',
            'order_date'   => '2018-01-29 09:08:08',
            'pay_date'     => '2018-01-29 19:08:08',
            'order_no'     => '10000094',
            'sign_type'    => 'MD5',
            'sign'         => '633A61BEFD0748E5725D2151ED156D90',
        ];

        $this->assertTrue($mock->verifyNotifyPayload($payload, $this->secretKey));
    }

    public function testNotifyWebhookParseNotifyPayload()
    {
        $mock = $this->getMockForTrait(NotifyWebhook::class);

        $payload = [
            'trade_result' => '1',
            'mer_no'       => '390200003',
            'mer_return_msg' => '已付款',
            'mer_order_no'    => '201801290001',
            'notify_type'   => 'back_notify',
            'currency'    => 1,
            'trade_amount' => '1.50',
            'order_date'   => '2018-01-29 09:08:08',
            'pay_date'     => '2018-01-29 19:08:08',
            'order_no'     => '10000094',
            'sign_type'    => 'MD5',
            'sign'         => '633A61BEFD0748E5725D2151ED156D90',
        ];

        $this->assertEquals([
            'trade_result' => '1',
            'mer_no'       => '390200003',
            'mer_return_msg' => '已付款',
            'mer_order_no'    => '201801290001',
            'notify_type'   => 'back_notify',
            'currency'    => 1,
            'trade_amount' => '1.50',
            'order_date'   => '2018-01-29 09:08:08',
            'pay_date'     => '2018-01-29 19:08:08',
            'order_no'     => '10000094',
            'sign_type'    => 'MD5',
            'sign'         => '633A61BEFD0748E5725D2151ED156D90',
        ], $mock->parseNotifyPayload($payload, $this->secretKey));
    }

    public function testNotifyWebhookSuccessNotifyResponse()
    {
        $mock = $this->getMockForTrait(NotifyWebhook::class);

        $this->assertEquals('SUCCESS', $mock->successNotifyResponse());
    }
}
