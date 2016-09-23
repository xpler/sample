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

final class bcUUID
{

    private function __construct(){}
    private function __clone(){}

   
    /** 
     * UUID V3 (MD5 hash)
     * @param string $NAMESPACE UUID
     * @param string $NAME DN Distinguished Name
     * @return mixed UUID | FALSE if fails
     */
    public static function v3( $NAMESPACE, $NAME )
    {
        if( !self::checkUUID($NAMESPACE) ):
            return FALSE;
        endif;
        
        //Gets hexadecimal components of namespace
        $nhex = str_replace(array('-','{','}'), '', $NAMESPACE);
        // Binary Value
        $nstr = '';
        //Converts Namespace UUID to bits
        for($i = 0; $i < strlen($nhex); $i+=2) {
          $nstr .= chr(hexdec($nhex[$i].$nhex[$i+1]));
        }

        // Calculate hash value of $NAME
        $hash = md5($nstr . $NAME);
        // 32 bits for "time_low"
        $tL = substr($hash, 0, 8);
        // 16 bits for "time_mid"
        $tM = substr($hash, 8, 4);
         // 16 bits for "time_hi_and_version"
        $tHandV = (hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x3000;
        // 16 bits "clk_seq_hi_res" | 8 bits for "clk_seq_low"
        $clkSeqHorLres = (hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000;
        // 48 bits for "node"
        $node = substr($hash, 20, 12);
        
        return sprintf('%08s-%04s-%04x-%04x-%12s', $tL, $tM, $tHandV, $clkSeqHorLres, $node);
    }

    /** 
     * UUID V4 (random)
     * @return string UUID
     */
    public static function v4()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), // 32 bits for "time_low"
            mt_rand(0, 0xffff), // 16 bits for "time_mid"
            mt_rand(0, 0x0fff) | 0x4000, // 16 bits for "time_hi_and_version"
            mt_rand(0, 0x3fff) | 0x8000, // 16 bits "clk_seq_hi_res" | 8 bits for "clk_seq_low"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff) // 48 bits for "node"
        );
    }


    /** 
     * UUID V5 (SHA1 hash)
     * @param string $NAMESPACE UUID
     * @param string $NAME DN Distinguished Name
     * @return mixed UUID | FALSE if fails
     */
    public static function v5( $NAMESPACE, $NAME )
    {
        if( !self::checkUUID($NAMESPACE) ):
            return FALSE;
        endif;
        
        $nhex = str_replace(array('-','{','}'), '', $NAMESPACE);
        $nstr = '';
        for($i = 0; $i < strlen($nhex); $i+=2) {
          $nstr .= chr(hexdec($nhex[$i].$nhex[$i+1]));
        }
        $hash = sha1($nstr . $NAME);
        $tL = substr($hash, 0, 8);
        $tM = substr($hash, 8, 4);
        $tHandV = (hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x5000;
        $clkSeqHorLres = (hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000;
        $node = substr($hash, 20, 12);
        return sprintf('%08s-%04s-%04x-%04x-%12s',$tL, $tM, $tHandV, $clkSeqHorLres, $node);
    }

   
    private static function checkUUID( $UUID )
    {
        $pattern = '/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i';
        return preg_match($pattern, $UUID) === 1;
    }
}