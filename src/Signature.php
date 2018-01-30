<?php

namespace Jetfuel\Biwpay;

class Signature
{
    /**
     * Generate signature.
     *
     * @param array $payload
     * @param string $secretKey
     * @return string
     */
    public static function generate(array $payload, $secretKey)
    {
        $baseString = self::buildBaseString($payload).'&key='.$secretKey;

        return self::md5Hash($baseString);
    }

    /**
     * @param array $payload
     * @param string $secretKey
     * @param string $signature
     * @return bool
     */
    public static function validate(array $payload, $secretKey, $signature)
    {
        return self::generate($payload, $secretKey) === $signature;
    }

    private static function buildBaseString(array $payload)
    {
        ksort($payload);
        reset($payload);
        $baseString = '';
        foreach ($payload as $key => $value) {
            $baseString .= $key.'='.$value.'&';
        }
        return rtrim($baseString, '&');
    }

    private static function md5Hash($data)
    {
        return strtoupper(md5($data));
    }
}
