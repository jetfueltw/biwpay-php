## 介紹

全球付 PHP 版本封裝。

## 安裝

使用 Composer 安裝。

```
composer require jetfueltw/biwpay-php
```

## 使用方法

### 掃碼支付下單

使用微信支付、QQ錢包、支付寶掃碼支付，下單後返回支付網址，請自行轉為 QR Code。


```
$merchantId = 'XXXXXXXXXXXXXXX'; // 商家號
$secretKey = 'XXXXXXXXXXXXXXX'; // MD5 密鑰
$tradeNo = '20180109023351XXXXX'; // 商家產生的唯一訂單號
$channel = Channel::WECHAT; // 支付通道，支援微信支付、QQ錢包、支付寶
$amount = 1.00; // 消費金額 (元)
$notifyUrl = 'https://XXX.XXX.XXX'; // 交易完成後異步通知接口
$returnUrl = 'https://XXX.XXX.XXX'; // 交易完成後會跳轉到這個頁面
```
```
$payment = new DigitalPayment(merchantId, secretKey);
$result = $payment->order($tradeNo, $channel, $amount, $notifyUrl, $returnUrl);
```
```
Result:
[
    'mer_no' => 'XXXXXXXXXXXXXXX'; // 商家號
    'mer_order_no' => '20180109023351XXXXX'; // 商家產生的唯一訂單號
    'order_no' => 'XXXXXXXXXXXXX'; // 平台訂單號
    'auth_result' => 'SUCCESS'; // 回應成功
    'trade_result' => 'X'; //0 未支付；1 支付成功 2 支付失敗；3 預支付成功
    'error_msg' =>'XXXXXXXXXX'; //返回錯誤信息
    'trade_return_msg' =>'XXXXXXX', // auth_result=SUCCESS 並且 trade_result=3將會返回對應二維碼url
    'mer_return_msg' => 'XXXXXXXXXX'; //商户返回信息说明
    'sign_type' => 'MD5'; //
    'sign' = > 'XXXXXXXXXXXXXXXXXXXXXXXXXX'; //簽名
];
```

### 掃碼支付交易成功通知

消費者支付成功後，平台會發出 HTTP POST 請求到你下單時填的 $notifyUrl，商家在收到通知並處理完後必須回應 `SUCCESS`。

* 商家必需正確處理重複通知的情況。
* 能使用 `NotifyWebhook@successNotifyResponse` 返回成功回應。  
* 務必使用 `NotifyWebhook@verifyNotifyPayload` 驗證簽證是否正確。
* 通知的消費金額單位為 `元`。 

```
Post Data: 
[
    'trade_result' => 'X'; // 交易結果; 0 未支付 1 支付成功
    'mer_no' => 'XXXXXXXXXXXXXXX'; // 商家號 
    'mer_return_msg' => 'XXXXXXXX'; // 商戶返回信息說明
    'mer_order_no' => '20180109023351XXXXX'; // 商家產生的唯一訂單號
    'notify_type' => 'back_notify'; // 通知類型
    'currency' => 1; //幣種
    'trade_amount' => '1.00'; //交易金額 單位為元
    'order_date' => '2018-01-16 20:13:29'; // 訂單時間
    'pay_date' => '2018-01-16 22:13:29'; // 支付時間
    'order_no' => 'XXXXXXX'; //支付訂單號
    'sign_type' => 'MD5'; //簽名類型
    'sign' => 'XXXXXXXXXXXXXXXXXXXXXX'; //簽名 
]
```

### 掃碼支付訂單查詢

使用商家訂單號查詢單筆訂單狀態。

```
$merchantId = 'XXXXXXXXXXXXXXX'; // 商家號
$secretKey = 'XXXXXXXXXXXXXXX'; // MD5 密鑰
$tradeNo = '20180109023351XXXXX'; // 商家產生的唯一訂單號

```
```
$tradeQuery = new TradeQuery(merchantId, secretKey);
$result = $tradeQuery->find($tradeNo);
```
```
Result:
[
    'auth_result' => 'SUCCESS'; // 請求驗證結果:SUCCESS：成功，其他，失敗
    'trade_result' => 'X'; // 0 未支付 1 支付成功
    'error_msg' => 'XXXXXXXXXXX'; // 錯誤訊息
    'mer_no' => 'XXXXXXXXXXXXXXX'; // 商家號 
    'mer_order_no' => '20180109023351XXXXX'; // 商家訂單號
    'order_no' => 'XXXXXXX'; // 平台訂單號
    'trade_amount' => '1.00'; // 交易金額 單位為元
    'pay_date' => '2018-01-16 22:13:29'; // 支付時間
    'sign' => 'XXXXXXXXXXXXXXXXXX'; //簽名
    'sign_type' => 'MD5'; //簽名類型
]