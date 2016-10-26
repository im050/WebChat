<?php
namespace Auth;
/**
 * JWT
 *
 * @author: memory<service@im050.com>
 */
class JWT
{

    /**
     * 加密算法类型
     * @var array
     */
    public static $supported_algs = array(
        'HS256' => array('hash_hmac', 'SHA256'),
        'HS512' => array('hash_hmac', 'SHA512'),
        'HS384' => array('hash_hmac', 'SHA384'),
        'RS256' => array('openssl', 'SHA256'),
    );

    public static function decode($jwt, $key, $algo = 'HS256')
    {
        $segments = explode(".", $jwt);

        $payload = json_decode(JWT::urlsafeB64Decode($segments[1]));

        return $payload;
    }

    /**
     * 校验jwt中的签名是否正确合法
     *
     * @param $jwt
     * @param $key
     * @return bool
     * @throws Exception
     */
    public static function verify($jwt, $key)
    {
        $segments = explode(".", $jwt);
        if (count($segments) != 3) {
            return false;
        }
        $header = json_decode(JWT::urlsafeB64Decode($segments[0]));
        //得到算法
        $algo = $header->alg;
        //JWT中的签名
        $jwt_signature = JWT::urlsafeB64Decode($segments[2]);
        //去除签名
        array_pop($segments);
        $output = implode(".", $segments);
        //根据payload计算的签名
        $valid_signature = JWT::sign($output, $key, $algo);
        return $jwt_signature == $valid_signature;

    }

    public static function encode($payload, $key, $algo = 'HS256')
    {
        $header = [
            'typ' => 'JWT',
            'alg' => $algo
        ];
        $segments = [];
        $segments[] = JWT::urlsafeB64Encode(json_encode($header));
        $segments[] = JWT::urlsafeB64Encode(json_encode($payload));
        $output = implode(".", $segments);
        $sign = JWT::sign($output, $key, $algo);
        return $output . '.' . JWT::urlsafeB64Encode($sign);
    }

    public static function sign($input, $key, $algo = 'HS256')
    {
        list($function, $algorithm) = JWT::$supported_algs[$algo];
        switch ($function) {
            case 'hash_hmac':
                $result = hash_hmac($algorithm, $input, $key);
                break;
            case 'openssl':
                $signature = '';
                $result = openssl_sign($input, $signature, $key, $algorithm);
                if (!$result) {
                    throw new Exception("OpenSSL unable to sign data");
                }
                break;
        }
        return $result;

    }

    public static function urlsafeB64Decode($input)
    {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $input .= str_repeat('=', $padlen);
        }
        return base64_decode(strtr($input, '-_', '+/'));
    }

    public static function urlsafeB64Encode($input)
    {
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }

}