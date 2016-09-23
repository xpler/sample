<?php

/**
 * BriskCoder
 *
 * NOTICE OF LICENSE
 *
 * @category    Library
 * @package     Package
 * @internal    Xpler Corporation Staff Only
 * @copyright   Copyright (c) 2010 Xpler Corporation. (http://www.xpler.com)
 * @license     http://www.briskcoder.com/license/  proprietary license, All rights reserved.
 */

namespace BriskCoder\Package\Library;

use BriskCoder\bc;
    

class bcCrypt
{

    public static

            /**
             * mcrypt || openssl
             * @var string $LIBRARY Cryptographic Library to use. Default is mcrypt
             */
            $LIBRARY = 'mcrypt',
            
            /**
             * aes-128 || aes-192 || aes-256  
             * @var string $CYPHER Cryptographic Cypher to use. Default is AES-128
             */
            $CYPHER = 'aes-128',
            
            /**
             * CBC is currently enforced as default.
             * @var string $MODE Cryptographic Cypher Mode to use. Default is cbc
             */
            $MODE = 'cbc',
            
            /**
             * PASSWORD
             * @var string $PASSWORD Cryptographic Password.
             */
            $PASSWORD = NULL,
            
            /**
             * SALT
             * @var string $SALT Cryptographic Salt.
             */
            $SALT = '~#$Brisk1111Coder*@9+=-)!_!~20==12';
    
    
    private static
            $_ns = array(),
            $currNS = NULL;

    private function __construct(){}

    private function __clone(){}

    /**
     * 
     */
    public static function useNamespace( $NAMESPACE )
    {
         self::$currNS = $NAMESPACE;
        //namespace exists?
        if ( isset( self::$_ns[$NAMESPACE] ) ):
            return;
        endif;
        
        //validate options or set default
        if ( self::$MODE !== 'cbc' ):
            self::$MODE = 'cbc';
        endif;
        
        if ( self::$PASSWORD === FALSE ):
            //call proper debug here
            exit('Please set a password and keep it safe');
        endif;
        
        
        //process password for proper key size
        switch (self::$CYPHER):
            case 'aes-128'://AES-128 key will use md5
                self::$PASSWORD = hash('md5', crypt( self::$PASSWORD, '$6$rounds=1111$' . self::$SALT . '$' ) , TRUE);
                break;
            case 'aes-192'://AES-192 key will use tiger192,4
                self::$PASSWORD = hash('tiger192,4', crypt( self::$PASSWORD, '$6$rounds=1111$' . self::$SALT . '$' ) , TRUE);
                break;
            case 'aes-256'://AES-256  key will use SHA256
                self::$PASSWORD = hash('SHA256', crypt( self::$PASSWORD, '$6$rounds=1111$' . self::$SALT . '$' ) , TRUE);
                break;
        endswitch;
        
        
        //defines cypher and mode according to library
        switch ( self::$LIBRARY ):
            case 'openssl':
                if (self::$CYPHER === 'aes-128'):
                    self::$CYPHER = 'AES-128-' . strtoupper(self::$MODE);
                    break;
                endif;
                
                if (self::$CYPHER === 'aes-192'):
                    self::$CYPHER = 'AES-192-' . strtoupper(self::$MODE);
                    break;
                endif;
                
                if (self::$CYPHER === 'aes-256'):
                    self::$CYPHER = 'AES-256-' . strtoupper(self::$MODE);
                    break;
                endif;
                //failed then use default
                self::$CYPHER = 'AES-128-' . strtoupper(self::$MODE);
                break;
            default://mcrypt
                self::$CYPHER = MCRYPT_RIJNDAEL_128;
        endswitch;
        
        

        self::$_ns[self::$currNS]['LIBRARY'] = self::$LIBRARY;
        self::$_ns[self::$currNS]['CYPHER'] = self::$CYPHER;
        self::$_ns[self::$currNS]['MODE'] = self::$MODE;
        self::$_ns[self::$currNS]['KEY'] = self::$PASSWORD;//already hashed and salted

        
        //reset to defaults
        self::$LIBRARY = 'mcrypt';
        self::$CYPHER = 'aes-128';
        self::$MODE = 'cbc';
        self::$PASSWORD = NULL;
        self::$SALT = '~#$Brisk1111Coder*@9+=-)!_!~20==12';
    }
    
    /**
     * Checks if bcCrypt Namespace is set
     * @param string $namespace bcCrypt namespace
     * @return bool
     */
    public  static function  hasNamespace( $NAMESPACE )
    {
        return isset(self::$_ns[$NAMESPACE]) ? TRUE : FALSE;
    }
    
    /**
     * Gets current namespace
     * @return string 
     */
    public static function getNamespace()
    {
        return self::$currNS;
    }

        /**
     * Encrypt 
     * @param mixed $DATA is automatically serialized
     * @return mixed Returns encrypted data or FALSE on failure
     */
    public static function encrypt( $DATA  )
    {
        if ( !isset( self::$_ns[self::$currNS] ) ):
            //bc::debugger()->CODE = 'DB-CONN:10000'; //todo write debuge codes
            bc::debugger()->_SOLUTION[] = self::$currNS;//and messages
            bc::debugger()->invoke();
        endif;
        
        if ( !isset($DATA) || $DATA === '' ):
            return FALSE;
        endif;

        $DATA = serialize($DATA);
        
        if ( self::$_ns[self::$currNS]['LIBRARY'] === 'openssl' )://openssl
            $strong = TRUE;
            $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(self::$_ns[self::$currNS]['CYPHER']), $strong);
            $openssl = base64_encode(openssl_encrypt( $DATA, self::$_ns[self::$currNS]['CYPHER'], self::$_ns[self::$currNS]['KEY'], true, $iv ));
            return base64_encode(base64_encode($iv) . base64_encode('+B+=+C+') . $openssl); //GLUE '+B+=+C+' must never change
        endif;
        
        //mcrypt
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(self::$_ns[self::$currNS]['CYPHER'], self::$_ns[self::$currNS]['MODE']), MCRYPT_DEV_RANDOM);
        //must use padding no matter what so openssl can decrypt mcrypt
        $block_size = mcrypt_get_block_size (self::$_ns[self::$currNS]['CYPHER'], self::$_ns[self::$currNS]['MODE']);
        $pad = $block_size - (strlen($DATA) % $block_size);
        $DATA .= str_repeat(chr ($pad), $pad); 

        $mcrypt = base64_encode(mcrypt_encrypt(self::$_ns[self::$currNS]['CYPHER'], self::$_ns[self::$currNS]['KEY'], $DATA, self::$_ns[self::$currNS]['MODE'], $iv));
        return base64_encode(base64_encode($iv) . base64_encode('+B+=+C+') . $mcrypt); //GLUE '+B+=+C+' must never change
    }
    
    
    /**
     * Decrypt
     * @param mixed $DATA is automatically unserialized
     * @return mixed Returns encrypted data or FALSE on failure
     */
    public static function decrypt( $DATA )
    {
        if ( !isset( self::$_ns[self::$currNS] ) ):
            //bc::debugger()->CODE = 'DB-CONN:10000'; //todo write debuge codes
            bc::debugger()->_SOLUTION[] = self::$currNS;//and messages
            bc::debugger()->invoke();
        endif;
    
        $DATA = explode(base64_encode('+B+=+C+'), base64_decode($DATA));//GLUE '+B+=+C+' must never change

        if ( !isset($DATA[0]) && !isset($DATA[1])):
            return FALSE;
        endif;

        if ( self::$_ns[self::$currNS]['LIBRARY'] === 'openssl' )://openssl
            return unserialize(openssl_decrypt(base64_decode($DATA[1]), self::$_ns[self::$currNS]['CYPHER'], self::$_ns[self::$currNS]['KEY'], true, base64_decode($DATA[0])));//$iv
        endif;
        
        //mcrypt
        return unserialize(mcrypt_decrypt(self::$_ns[self::$currNS]['CYPHER'], self::$_ns[self::$currNS]['KEY'],  base64_decode($DATA[1]), self::$_ns[self::$currNS]['MODE'], base64_decode($DATA[0])));
    }

}