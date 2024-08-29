<?php
namespace API\Helper;

class EncryptHelper
{
    private $alg = 'aes-256-cbc';
    private $key = '6dsSuf^%LSy4@b^KN6VZvt@FtKp8scUr';
    private $iv = '%N6S3%2!w^sJKVUs';

    public function __construct()
    {
        $this->key = substr(hash('sha256', $this->key, true), 0, 32);
        $this->iv = substr($this->iv, 0, 16);
    }

    public function encryptData($data)
    {
        $ciphertext = openssl_encrypt($data, $this->alg, $this->key, OPENSSL_RAW_DATA, $this->iv);
        return bin2hex($ciphertext);
    }

    public function decryptData($data)
    {
        $data = hex2bin($data);
        return openssl_decrypt($data, $this->alg, $this->key, OPENSSL_RAW_DATA, $this->iv);
    }
}
