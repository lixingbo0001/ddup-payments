<?php
/**
 * Created by PhpStorm.
 * Date: 2020/6/8
 * Time: 下午5:05
 */

namespace Ddup\Payments\Upay2\Kernel;

use Ddup\Part\Exception\ExceptionCustomCodeAble;

class BillSignTool
{
    private $keyPassword;

    public function __construct($keyPassword)
    {
        $this->keyPassword = $keyPassword;
    }

    public function encode($con, $privateKeyPath)
    {
        if (!is_file($privateKeyPath)) {
            throw new ExceptionCustomCodeAble("秘钥文件不存在:" . $privateKeyPath);
        }

        $pem = file_get_contents($privateKeyPath);

        if (!openssl_pkcs12_read($pem, $certs, $this->keyPassword)) {
            throw new ExceptionCustomCodeAble("openssl读取秘钥失败,password:" . $this->keyPassword);
        }

        $privateKey = $certs['pkey'];

        if (!openssl_sign(utf8_encode($con), $binarySignature, $privateKey, OPENSSL_ALGO_SHA256)) {
            throw new ExceptionCustomCodeAble("openssl_sign执行失败");
        }

        return bin2hex($binarySignature);
    }

    public function decode($con, $sign, $publicKeyPath)
    {
        if (!is_file($publicKeyPath)) {
            throw new ExceptionCustomCodeAble("证书文件不存在:" . $publicKeyPath);
        }

        $pem = file_get_contents($publicKeyPath);

        if (!$pem) {
            throw new ExceptionCustomCodeAble("证书内容为空:" . $publicKeyPath);
        }

        $content = self::derToPem($pem);

        $publicId = openssl_x509_read($content);

        if (!$publicId) {
            throw new ExceptionCustomCodeAble("无效的证书:" . $publicKeyPath);
        }

        return (bool)openssl_verify(utf8_encode($con), $sign, $publicId, OPENSSL_ALGO_SHA256);
    }

    public function derToPem($certificateCAcerContent)
    {
        return '-----BEGIN CERTIFICATE-----' . PHP_EOL
            . chunk_split(base64_encode($certificateCAcerContent), 64, PHP_EOL)
            . '-----END CERTIFICATE-----' . PHP_EOL;
    }
}
