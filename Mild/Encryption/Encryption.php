<?php
/**
 * Mild Framework component
 *
 * @author Mochammad Riyadh Ilham Akbar Pasya
 * @link https://github.com/mildphp/mild
 * @copyright 2018
 * @license https://github.com/mildphp/mild/blob/master/LICENSE (MIT Licence)
 */
namespace Mild\Encryption;

class Encryption
{
    /**
     * @var string
     */
    protected $key;
    /**
     * @var string
     */
    protected $cipher;
    /**
     * @var array
     */
    protected static $cipherMethods = [
        'AES-128-CBC' => 0,
        'AES-128-CBC-HMAC-SHA1' => 1,
        'AES-128-CBC-HMAC-SHA256' => 2,
        'AES-128-CFB' => 3,
        'AES-128-CFB1' => 4,
        'AES-128-CFB8' => 5,
        'AES-128-CTR' => 6,
        'AES-128-ECB' => 7,
        'AES-128-OCB' => 8,
        'AES-128-OFB' => 9,
        'AES-128-XTS' => 10,
        'AES-192-CBC' => 11,
        'AES-192-CFB' => 12,
        'AES-192-CFB1' => 13,
        'AES-192-CFB8' => 14,
        'AES-192-CTR' => 15,
        'AES-192-ECB' => 16,
        'AES-192-OCB' => 17,
        'AES-192-OFB' => 18,
        'AES-256-CBC' => 19,
        'AES-256-CBC-HMAC-SHA1' => 20,
        'AES-256-CBC-HMAC-SHA256' => 21,
        'AES-256-CFB' => 22,
        'AES-256-CFB1' => 23,
        'AES-256-CFB8' => 24,
        'AES-256-CTR' => 25,
        'AES-256-ECB' => 26,
        'AES-256-OCB' => 27,
        'AES-256-OFB' => 28,
        'AES-256-XTS' => 29,
        'BF-CBC' => 30,
        'BF-CFB' => 31,
        'BF-ECB' => 32,
        'BF-OFB' => 33,
        'CAMELLIA-128-CBC' => 34,
        'CAMELLIA-128-CFB' => 35,
        'CAMELLIA-128-CFB1' => 36,
        'CAMELLIA-128-CFB8' => 37,
        'CAMELLIA-128-CTR' => 38,
        'CAMELLIA-128-ECB' => 39,
        'CAMELLIA-128-OFB' => 40,
        'CAMELLIA-192-CBC' => 41,
        'CAMELLIA-192-CFB' => 42,
        'CAMELLIA-192-CFB1' => 43,
        'CAMELLIA-192-CFB8' => 44,
        'CAMELLIA-192-CTR' => 45,
        'CAMELLIA-192-ECB' => 46,
        'CAMELLIA-192-OFB' => 47,
        'CAMELLIA-256-CBC' => 48,
        'CAMELLIA-256-CFB' => 49,
        'CAMELLIA-256-CFB1' => 50,
        'CAMELLIA-256-CFB8' => 51,
        'CAMELLIA-256-CTR' => 52,
        'CAMELLIA-256-ECB' => 53,
        'CAMELLIA-256-OFB' => 54,
        'CAST5-CBC' => 55,
        'CAST5-CFB' => 56,
        'CAST5-ECB' => 57,
        'CAST5-OFB' => 58,
        'ChaCha20' => 59,
        'ChaCha20-Poly1305' => 60,
        'DES-CBC' => 61,
        'DES-CFB' => 62,
        'DES-CFB1' => 63,
        'DES-CFB8' => 64,
        'DES-ECB' => 65,
        'DES-EDE' => 66,
        'DES-EDE-CBC' => 67,
        'DES-EDE-CFB' => 68,
        'DES-EDE-OFB' => 69,
        'DES-EDE3' => 70,
        'DES-EDE3-CBC' => 71,
        'DES-EDE3-CFB' => 72,
        'DES-EDE3-CFB1' => 73,
        'DES-EDE3-CFB8' => 74,
        'DES-EDE3-OFB' => 75,
        'DES-OFB' => 76,
        'DESX-CBC' => 77,
        'RC2-40-CBC' => 78,
        'RC2-64-CBC' => 79,
        'RC2-CBC' => 80,
        'RC2-CFB' => 81,
        'RC2-ECB' => 82,
        'RC2-OFB' => 83,
        'RC4' => 84,
        'RC4-40' => 85,
        'RC4-HMAC-MD5' => 86,
        'SEED-CBC' => 87,
        'SEED-CFB' => 88,
        'SEED-ECB' => 89,
        'SEED-OFB' => 90,
        'aes-128-cbc' => 91,
        'aes-128-cbc-hmac-sha1' => 92,
        'aes-128-cbc-hmac-sha256' => 93,
        'aes-128-ccm' => 94,
        'aes-128-cfb' => 95,
        'aes-128-cfb1' => 96,
        'aes-128-cfb8' => 97,
        'aes-128-ctr' => 98,
        'aes-128-ecb' => 99,
        'aes-128-gcm' => 100,
        'aes-128-ocb' => 101,
        'aes-128-ofb' => 102,
        'aes-128-xts' => 103,
        'aes-192-cbc' => 104,
        'aes-192-ccm' => 105,
        'aes-192-cfb' => 106,
        'aes-192-cfb1' => 107,
        'aes-192-cfb8' => 108,
        'aes-192-ctr' => 109,
        'aes-192-ecb' => 110,
        'aes-192-gcm' => 111,
        'aes-192-ocb' => 112,
        'aes-192-ofb' => 113,
        'aes-256-cbc' => 114,
        'aes-256-cbc-hmac-sha1' => 115,
        'aes-256-cbc-hmac-sha256' => 116,
        'aes-256-ccm' => 117,
        'aes-256-cfb' => 118,
        'aes-256-cfb1' => 119,
        'aes-256-cfb8' => 120,
        'aes-256-ctr' => 121,
        'aes-256-ecb' => 122,
        'aes-256-gcm' => 123,
        'aes-256-ocb' => 124,
        'aes-256-ofb' => 125,
        'aes-256-xts' => 126,
        'bf-cbc' => 127,
        'bf-cfb' => 128,
        'bf-ecb' => 129,
        'bf-ofb' => 130,
        'camellia-128-cbc' => 131,
        'camellia-128-cfb' => 132,
        'camellia-128-cfb1' => 133,
        'camellia-128-cfb8' => 134,
        'camellia-128-ctr' => 135,
        'camellia-128-ecb' => 136,
        'camellia-128-ofb' => 137,
        'camellia-192-cbc' => 138,
        'camellia-192-cfb' => 139,
        'camellia-192-cfb1' => 140,
        'camellia-192-cfb8' => 141,
        'camellia-192-ctr' => 142,
        'camellia-192-ecb' => 143,
        'camellia-192-ofb' => 144,
        'camellia-256-cbc' => 145,
        'camellia-256-cfb' => 146,
        'camellia-256-cfb1' => 147,
        'camellia-256-cfb8' => 148,
        'camellia-256-ctr' => 149,
        'camellia-256-ecb' => 150,
        'camellia-256-ofb' => 151,
        'cast5-cbc' => 152,
        'cast5-cfb' => 153,
        'cast5-ecb' => 154,
        'cast5-ofb' => 155,
        'chacha20' => 156,
        'chacha20-poly1305' => 157,
        'des-cbc' => 158,
        'des-cfb' => 159,
        'des-cfb1' => 160,
        'des-cfb8' => 161,
        'des-ecb' => 162,
        'des-ede' => 163,
        'des-ede-cbc' => 164,
        'des-ede-cfb' => 165,
        'des-ede-ofb' => 166,
        'des-ede3' => 167,
        'des-ede3-cbc' => 168,
        'des-ede3-cfb' => 169,
        'des-ede3-cfb1' => 170,
        'des-ede3-cfb8' => 171,
        'des-ede3-ofb' => 172,
        'des-ofb' => 173,
        'desx-cbc' => 174,
        'id-aes128-CCM' => 175,
        'id-aes128-GCM' => 176,
        'id-aes128-wrap' => 177,
        'id-aes128-wrap-pad' => 178,
        'id-aes192-CCM' => 179,
        'id-aes192-GCM' => 180,
        'id-aes192-wrap' => 181,
        'id-aes192-wrap-pad' => 182,
        'id-aes256-CCM' => 183,
        'id-aes256-GCM' => 184,
        'id-aes256-wrap' => 185,
        'id-aes256-wrap-pad' => 186,
        'id-smime-alg-CMS3DESwrap' => 187,
        'rc2-40-cbc' => 188,
        'rc2-64-cbc' => 189,
        'rc2-cbc' => 190,
        'rc2-cfb' => 191,
        'rc2-ecb' => 192,
        'rc2-ofb' => 193,
        'rc4' => 194,
        'rc4-40' => 195,
        'rc4-hmac-md5' => 196,
        'seed-cbc' => 197,
        'seed-cfb' => 198,
        'seed-ecb' => 199,
        'seed-ofb' => 200,
    ];

    /**
     * Encryption constructor.
     * @param $key
     * @param string $cipher
     */
    public function __construct($key, $cipher = 'AES-256-CBC')
    {
        if (!isset(static::$cipherMethods[$cipher])) {
            throw new EncryptionException('Unsupported cipher ['.$cipher.'] in encryption');
        }
        $this->key = $key;
        $this->cipher = $cipher;
    }

    /**
     * @param $value
     * @return string
     */
    public function encrypt($value)
    {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->cipher));
        $value = openssl_encrypt(serialize($value), $this->cipher, $this->key, 0, $iv);
        if ($value === false) {
            throw new EncryptionException('Could not encrypt the data.');
        }
        $value = base64_encode(json_encode(['iv' => base64_encode($iv), 'value' => $value]));
        if (json_last_error() !== 0) {
            throw new EncryptionException('Could not encrypt the data.');
        }
        return $value;
    }

    /**
     * @param $value
     * @return string
     */
    public function decrypt($value)
    {
        $value = base64_decode($value);
        $value = json_decode($value, true);
        if (!is_array($value) && !isset($value['iv']) || !isset($value['value'])) {
            throw new EncryptionException('Could decrypt the data');
        }
        $iv = base64_decode($value['iv']);
        $value = openssl_decrypt($value['value'], $this->cipher, $this->key, 0, $iv);
        return unserialize($value);
    }

    /** 
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /** 
     * @return string
     */
    public function getCipher()
    {
        return $this->cipher;
    }
}