<?php

namespace App\Helpers;

class Cript
{
     public static function encriptar($string)
        {
            $ciphering = "AES-128-CTR";
            $iv_length = openssl_cipher_iv_length($ciphering);
            $options = 0;
            $encryption_iv = '6664566667896666';
            $encryption_key = 'c0cin2z0mbi3';
            $encryption = openssl_encrypt($string, $ciphering, $encryption_key, $options, $encryption_iv);
            
            return $encryption;
            
        }

        public static function decriptar($string)
        {
            $ciphering = "AES-128-CTR";
            $iv_length = openssl_cipher_iv_length($ciphering);
            $options = 0;
            $decryption_iv = '6664566667896666';
            $decryption_key = 'c0cin2z0mbi3';
            $decryption = openssl_decrypt($string, $ciphering, $decryption_key, $options, $decryption_iv);
            
            return $decryption;
        }   
}