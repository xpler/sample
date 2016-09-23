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


final class bcCache
{

    public static
    /**
     * ENCODING TYPE
     * 0 = raw, 1 = json 2 = serialize (2 Default)
     * @var int Description Default is 2
     */
            $ENCODING = 2,
            /**
             * CACHE DEFAULT MAX LIFE
             * Default 86400 = 24hr or until next update occurs
             * @var int $LIFE 
             */
            $LIFE_DEFAULT = 86400,
            /**
             * CACHE SYSTEM
             * FileSystem | APCu | Memcache | Shared Memory | SQLite3
             * files | apc | memcache | shm | sqlite
             * Default files
             * @var string $SYSTEM 'files' | 'apc' | 'memcache'  | 'shm' | 'sqlite'
             */
            $SYSTEM = 'files',
            
            /**
             * FILE EXTENSION
             * Applicable only when $SYSTEM is type of 'files' 
             * Tip: $ENCODING is important when expecting server headers to parse specific mime types othe than simplified .cache files, then set to 0.
             * Do not use . (period), only the desired extension name ie: js, css
             * @var string $FILE_EXTENSION default is cache
             */
            $FILE_EXTENSION = 'cache',
            /**
             * STORAGE_RESOURCE
             * if $SYSTEM typeof 'files' then $STORAGE_RESOURCE = 'FQN storage path' Default is BC_PRIVATE_ASSETS_PATH . 'Cache' . DIRECTORY_SEPARATOR <br>
             * if $SYSTEM typeof 'apc' then $STORAGE_RESOURCE is FALSE <br>
             * if $SYSTEM typeof 'memcache' then $STORAGE_RESOURCE = instanceof \Memcache object and php_memcache extension must be available<br>
             * if $SYSTEM typeof 'shm' then $STORAGE_RESOURCE is FALSE<br>
             * if $SYSTEM typeof 'sqlite' then $STORAGE_RESOURCE = FQN storage path' Default is BC_PRIVATE_ASSETS_PATH . 'Cache' . DIRECTORY_SEPARATOR <br>
             * @var mixed 
             */
            $STORAGE_RESOURCE = FALSE,
            /**
             * FALLS BACK if 'apc','memcache', 'sqlite3' or 'shm' is not available then 'files' .
             * Default is TRUE for auto fallback mode, if set to FALSE then only cache typeof selected will be verified
             */
            $FALLBACK = TRUE;
    private static
            $_ns = array(),
            $currNS = NULL,
            $runtimeLife = FALSE;

    private function __construct()
    {
        
    }

    private function __clone()
    {
        
    }

    /**
     * NOTE: If namespaces and their respective configurations are meant to persist, then set them at _GLobal dispatcher level
     * Use an existing namespace or create a new one with provided STORAGE_RESOURCE and settings
     * Returns string 'files' if $SYSTEM fails to initialize with 'memcache' | 'apc' and gracefully falls back on typeof 'files' UNLESS
     * $FALLBACK is set to false 
     * @param string $NAMESPACE
     * @return mixed TRUE | FALSE if no $FALLBACK mode is matched
     */
    public static function useNamespace( $NAMESPACE )
    {
        self::$currNS = $NAMESPACE;
        //namespace exists?
        if ( isset( self::$_ns[$NAMESPACE] ) ):
            self::$ENCODING = 2;
            self::$LIFE_DEFAULT = 86400;
            self::$SYSTEM = 'files';
            self::$FILE_EXTENSION = 'cache';
            self::$STORAGE_RESOURCE = FALSE;
            self::$FALLBACK = TRUE;
            return TRUE;
        endif;

        self::$SYSTEM = \strtolower( self::$SYSTEM );

        $fallback = self::$FALLBACK;
        self::$_ns[self::$currNS]['LIFE_DEFAULT'] = self::$LIFE_DEFAULT;
        self::$_ns[self::$currNS]['ENCODING'] = self::$ENCODING;
        $system = self::$_ns[self::$currNS]['SYSTEM'] = self::$SYSTEM;
        self::$_ns[self::$currNS]['EXTENSION'] = '.' . self::$FILE_EXTENSION;
        $storage = self::$STORAGE_RESOURCE;

        //reset defaults;
        self::$ENCODING = 2;
        self::$LIFE_DEFAULT = 86400;
        self::$SYSTEM = 'files';
        self::$FILE_EXTENSION = 'cache';
        self::$STORAGE_RESOURCE = FALSE;
        self::$FALLBACK = TRUE;

        switch ( TRUE ):
            case $system === 'memcache' && $storage instanceof \Memcache:
                self::$_ns[self::$currNS]['RESOURCE'] = $storage;
                return TRUE;
            case $system === 'apc' && extension_loaded( 'apc' ) && ini_get( 'apc.enabled' ) :
                self::$_ns[self::$currNS]['RESOURCE'] = NULL;
                return TRUE;
            case $system === 'shm' && extension_loaded( 'shm' ) :
                self::$_ns[self::$currNS]['RESOURCE'] = NULL;
                return TRUE;
            case $system === 'sqlite' && extension_loaded( 'sqlite3' ) :

                if ( !$storage ):
                    $storage = BC_PRIVATE_ASSETS_PATH . 'Cache' . DIRECTORY_SEPARATOR . 'bcCacheStorage.cached';
                endif;

                self::$_ns[self::$currNS]['RESOURCE'] = $storage;

                try {


                    $db = self::$_ns[self::$currNS]['OBJECT'] = new \SQLite3( $storage );
                    $tbl = md5( self::$currNS );

                    $sql = $db->query( "SELECT name FROM sqlite_master WHERE type='table' AND name='" . $tbl . "'" );
                    $sql = $sql->fetchArray();

                    if ( isset( $sql['name'] ) ):
                        return TRUE;
                    endif;

                    $sql = "CREATE TABLE '" . $tbl . "'  (" .
                            "id INTEGER PRIMARY KEY, " .
                            "key TEXT NOT NULL, " .
                            "value TEXT NULL, " .
                            "last_modified INTEGER NOT NULL)";
                    $db->exec( $sql );
                    return TRUE;
                } catch ( \Exception $ex ) {
                    return FALSE;
                }
            default:
                if ( ($system === 'apc' || $system === 'memcache' || $system === 'shm' || $system === 'sqlite') && $fallback === FALSE ):
                    unset( self::$_ns[self::$currNS] );
                    return FALSE;
                endif;

                self::$_ns[self::$currNS]['SYSTEM'] = 'files';

                if ( !$storage ):
                    $storage = BC_PRIVATE_ASSETS_PATH . 'Cache' . DIRECTORY_SEPARATOR;
                endif;

                if ( !is_dir( $storage ) ):
                    mkdir( $storage );
                endif;

                self::$_ns[self::$currNS]['RESOURCE'] = $storage;
                return TRUE;
        endswitch;
    }

    /**
     * Check if Namespace exists
     * Namespaces only exist if they were successfuly created
     * @param string $NAMESPACE
     * @return bool
     */
    public static function hasNamespace( $NAMESPACE )
    {
        return isset( self::$_ns[$NAMESPACE] ) ? TRUE : FALSE;
    }

    /**
     * Deletes Namespace and runs a garbage collection
     * @param string $NAMESPACE
     * @param bool $GC_ONLY Garbage Collection Only, Preserve Settings and Namespace. Default FALSE
     * @return bool
     */
    public static function deleteNamespace( $NAMESPACE, $GC_ONLY = FALSE)
    {
        if ( !isset( self::$_ns[$NAMESPACE] ) ):
            return FALSE;
        endif;

        $keyCollection = md5( $NAMESPACE );
        switch ( self::$_ns[$NAMESPACE]['SYSTEM'] ):
            case 'files':
                if ( !is_dir( self::$_ns[$NAMESPACE]['RESOURCE'] ) ):
                    return FALSE;
                endif;
                $_files = glob( self::$_ns[$NAMESPACE]['RESOURCE'] . $NAMESPACE . '-*' . self::$_ns[self::$currNS]['EXTENSION'] );
                if ( $_files ):
                    foreach ( $_files as $f ):
                        unlink( $f );
                    endforeach;
                endif;
                break;
            case 'apc':
                $_keys = apc_fetch( $keyCollection );
                if ( $_keys ):
                    foreach ( $_keys as $k => $v ):
                        apc_delete( $k );
                    endforeach;
                endif;
                break;
            case 'shm':
                $keyCollection = substr( base_convert( $keyCollection, 16, 10 ), -5 );
                $shmid = shmop_open( $keyCollection, "a", 0, 0 );
                shmop_delete( $shmid );
                shmop_close( $shmid );
                break;
            case 'sqlite':
                $db = self::$_ns[$NAMESPACE]['OBJECT'];
                $db->exec( "DROP TABLE IF EXISTS " . $keyCollection );
                break;
            case 'memcache':
                $_keys = self::$_ns[$NAMESPACE]['RESOURCE']->get( $keyCollection );
                if ( $_keys ):
                    foreach ( $_keys as $k => $v ):
                        self::$_ns[$NAMESPACE]['RESOURCE']->delete( $k );
                    endforeach;
                endif;
                break;
        endswitch;
        
        if (!$GC_ONLY):
            unset( self::$_ns[$NAMESPACE] );
        endif;
        
        return TRUE;
    }

    /**
     * Deletes ALL Namespaces and runs a Global garbage collection
     * ALL cached data will be wiped! Attention when sharing same storage resources
     * @return bool
     */
    public static function flush()
    {

        if ( !isset( self::$_ns[self::$currNS]['SYSTEM'] ) )
            return;

        foreach ( self::$_ns as $ns => $d ):
            switch ( $d['SYSTEM'] ):
                case 'files':
                    if ( !is_dir( $d['RESOURCE'] ) ):
                        return FALSE;
                    endif;
                    $_files = glob( $d['RESOURCE'] . '*' . self::$_ns[self::$currNS]['EXTENSION'] );
                    if ( $_files ):
                        foreach ( $_files as $f ):
                            chmod( $f, 0777 );
                            unlink( $f );
                        endforeach;
                    endif;
                    break;
                case 'apc':
                    apc_clear_cache();
                    apc_clear_cache( 'user' );
                    break;
                case 'shm':
                    $keyCollection = substr( base_convert( md5( $ns ), 16, 10 ), -5 );
                    $shmid = shmop_open( $keyCollection, "a", 0, 0 );
                    shmop_delete( $shmid );
                    shmop_close( $shmid );
                    break;
                case 'sqlite':
                    if ( isset( $d['OBJECT'] ) ):
                        $d['OBJECT']->close();
                        if ( is_file( $d['RESOURCE'] ) ):
                            chmod( $d['RESOURCE'], 0777 );
                            unlink( $d['RESOURCE'] );
                        endif;
                    endif;
                    break;
                case 'memcache':
                    $mc = new \Memcache();
                    $mc->flush();
                    $time = time() + 1; //one second future 
                    while ( time() < $time ) {
                        //sleep 
                    }
                    break;
            endswitch;
        endforeach;

        self::$_ns = array();
        return TRUE;
    }

    /**
     * READ cached data
     * when $SYSTEM typeof 'files' unserialization happens internally
     * @param string $KEY
     * @return mixed  FALSE if no $KEY found
     */
    public static function read( $KEY )
    {
        if ( !isset( self::$_ns[self::$currNS] ) ):
            return FALSE;
        endif;

        $keyCollection = md5( self::$currNS );

        switch ( self::$_ns[self::$currNS]['SYSTEM'] ):
            case 'files':
                $KEY = self::$currNS . '-' . $KEY;
                $file = self::$_ns[self::$currNS]['RESOURCE'] . $KEY . self::$_ns[self::$currNS]['EXTENSION'];
                if ( is_file( $file ) ): 
                    clearstatcache();
                    $life = self::$_ns[self::$currNS]['LIFE_DEFAULT'];
                    if ( self::$runtimeLife !== FALSE ):
                        $life = self::$runtimeLife;
                        self::$runtimeLife = FALSE;
                    endif;
                    if ( (filemtime( $file ) + $life) > $_SERVER['REQUEST_TIME'] ): 
                        switch ( self::$_ns[self::$currNS]['ENCODING'] ):
                            case 0://raw
                                return file_get_contents( $file );
                            case 1://json
                                return json_decode( file_get_contents( $file ), TRUE );
                            case 2://serialize
                                return unserialize( file_get_contents( $file ) );
                        endswitch;
                    endif;
                endif;
                return FALSE;
            case 'apc':
                return apc_fetch( $KEY );
            case 'shm':
                $key = substr( base_convert( $keyCollection, 16, 10 ), -5 );
                $shmid = shmop_open( $key, "a", 0, 0 );

                if ( !$shmid ):
                    return FALSE;
                endif;

                $_DATA = shmop_read( $shmid, 0, shmop_size( $shmid ) );

                if ( !$_DATA ):
                    return FALSE;
                endif;

                $_DATA = unserialize( $_DATA );
                shmop_close( $shmid );

                $life = self::$_ns[self::$currNS]['LIFE_DEFAULT'];
                if ( self::$runtimeLife !== FALSE ):
                    $life = self::$runtimeLife;
                    self::$runtimeLife = FALSE;
                endif;

                if ( isset( $_DATA[$KEY] ) ):
                    if ( ($_DATA[$KEY]['LAST_MODIFIED'] + $life) > $_SERVER['REQUEST_TIME'] ):
                        return $_DATA[$KEY]['DATA'];
                    endif;
                endif;
                return FALSE;
            case 'sqlite':
                $_DATA = array();
                try {
                    $db = self::$_ns[self::$currNS]['OBJECT'];
                    $sql = $db->prepare( "SELECT id, key, value, last_modified FROM '" . $keyCollection . "' WHERE key=:key" );
                    $sql->bindValue( ':key', $KEY, SQLITE3_TEXT );
                    $result = $sql->execute();
                    $_DATA = $result->fetchArray();
                } catch ( \Exception $ex ) {
                    return FALSE;
                }

                if ( !isset( $_DATA['last_modified'] ) ):
                    return FALSE;
                endif;

                $life = self::$_ns[self::$currNS]['LIFE_DEFAULT'];
                if ( self::$runtimeLife !== FALSE ):
                    $life = self::$runtimeLife;
                    self::$runtimeLife = FALSE;
                endif;

                if ( ($_DATA['last_modified'] + $life) > $_SERVER['REQUEST_TIME'] ):
                    return unserialize( $_DATA['value'] );
                endif;

                try {
                    $db = self::$_ns[self::$currNS]['OBJECT'];
                    $db->exec( "DELETE FROM '" . $keyCollection . "' WHERE id=" . $_DATA['id'] );
                } catch ( \Exception $ex ) {
                    
                }
                return FALSE;
            case 'memcache':
                return self::$_ns[self::$currNS]['RESOURCE']->get( $KEY );
        endswitch;
    }

    /**
     * WRITE data to cache
     * when $SYSTEM typeof 'files' serialization happens internally
     * @param string $KEY Keys are internally md5 hashed preceding by current Namespace and - (ie: ns-md5(key).cache)
     * @param mixed $_DATA 
     * @return void 
     */
    public static function write( $KEY, $_DATA )
    {
        if ( !isset( self::$_ns[self::$currNS] ) ):
            return FALSE;
        endif;


        $keyCollection = md5( self::$currNS );

        switch ( self::$_ns[self::$currNS]['SYSTEM'] ):
            case 'files':
                switch ( self::$_ns[self::$currNS]['ENCODING'] ):
                    case 0://raw
                        break;
                    case 1://json
                        $_DATA = json_encode( $_DATA );
                        break;
                    case 2://serialize
                        $_DATA = serialize( $_DATA );
                        break;
                endswitch;
                $KEY = self::$currNS . '-' . $KEY;
                file_put_contents( self::$_ns[self::$currNS]['RESOURCE'] . $KEY . self::$_ns[self::$currNS]['EXTENSION'] , $_DATA );
                break;
            case 'apc':
                $life = self::$_ns[self::$currNS]['LIFE_DEFAULT'];
                if ( self::$runtimeLife !== FALSE ):
                    $life = self::$runtimeLife;
                    self::$runtimeLife = FALSE;
                endif;
                //store key in the keys collection also the timestamp of last creation time
                $_keys = apc_fetch( $keyCollection );
                if ( $_keys ):
                    $_keys[$KEY] = $_SERVER['REQUEST_TIME'];
                    apc_store( $keyCollection, $_keys, $life );
                endif;
                apc_store( $KEY, $_DATA, $life );
                break;
            case 'shm':
                $keyCollection = substr( base_convert( $keyCollection, 16, 10 ), -5 );
                $shmid = @shmop_open( $keyCollection, "a", 0, 0 );
                if ( $shmid ):
                    $_store = shmop_read( $shmid, 0, shmop_size( $shmid ) );
                    if ( $_store ):
                        $_store = unserialize( $_store );
                    else:
                        $_store = array();
                    endif;
                    shmop_close( $shmid );
                endif;

                $_store[$KEY]['DATA'] = $_DATA;
                $_store[$KEY]['LIFE'] = $life;
                $_store[$KEY]['LAST_MODIFIED'] = $_SERVER['REQUEST_TIME'];
                $_DATA = serialize( $_store );
                $shmid = shmop_open( $keyCollection, "c", 0644, strlen( $_DATA ) );
                shmop_write( $shmid, $_DATA, 0 );
                shmop_close( $shmid );
                break;
            case 'sqlite':
                try {
                    $stmt = "INSERT OR REPLACE INTO '" . $keyCollection . "' (id, key, value, last_modified ) VALUES(null, :key, :value, :last_modified )";
                    $db = self::$_ns[self::$currNS]['OBJECT'];
                    $sql = $db->prepare( $stmt );
                    $sql->bindValue( ':key', $KEY, SQLITE3_TEXT );
                    $sql->bindValue( ':value', serialize( $_DATA ), SQLITE3_TEXT );
                    $sql->bindValue( ':last_modified', $_SERVER['REQUEST_TIME'], SQLITE3_INTEGER );
                    $sql->execute();
                } catch ( \Exception $ex ) {
                    return FALSE;
                }
                break;
            case 'memcache':
                $life = self::$_ns[self::$currNS]['LIFE_DEFAULT'];
                if ( self::$runtimeLife !== FALSE ):
                    $life = self::$runtimeLife;
                    self::$runtimeLife = FALSE;
                endif;
                //store key in the keys collection
                $_keys = self::$_ns[self::$currNS]['RESOURCE']->get( $keyCollection );
                if ( $_keys ):
                    $_keys[$KEY] = $_SERVER['REQUEST_TIME'];
                    self::$_ns[self::$currNS]['RESOURCE']->set( $keyCollection, $_keys, MEMCACHE_COMPRESSED, $life );
                endif;

                self::$_ns[self::$currNS]['RESOURCE']->set( $KEY, $_DATA, MEMCACHE_COMPRESSED, $life );
                break;
        endswitch;
    }

    /**
     * GETS LAST MODIFIED TIMESTAMP OF A KEY
     * @param type $KEY
     * @return int 0 if fails or UNIX TIMESTAMP
     */
    public static function getLastModified( $KEY )
    {
        if ( !isset( self::$_ns[self::$currNS] ) ):
            return FALSE;
        endif;

        $keyCollection = md5( self::$currNS );
        switch ( self::$_ns[self::$currNS]['SYSTEM'] ):
            case 'files':
                $KEY = self::$currNS . '-' .  $KEY;
                $file = self::$_ns[self::$currNS]['RESOURCE'] . $KEY . self::$_ns[self::$currNS]['EXTENSION'];
                clearstatcache();
                if ( is_file( $file ) ):
                    return filemtime( $file );
                endif;
                return 0;
            case 'apc':
                $_keys = apc_fetch( $keyCollection );
                return isset( $_keys[$KEY] ) ? $_keys[$KEY] : 0;
            case 'shm':
                $keyCollection = substr( base_convert( $keyCollection, 16, 10 ), -5 );
                $shm_id = shmop_open( $keyCollection, "a", 0, 0 );

                if ( !$shm_id ):
                    return 0;
                endif;
                $_DATA = shmop_read( $shm_id, 0, shmop_size( $shm_id ) );

                if ( !$_DATA ):
                    return 0;
                endif;

                $_DATA = unserialize( $_DATA );
                shmop_close( $shmid );
                return isset( $_DATA[$KEY]['LAST_MODIFIED'] ) ? $_DATA[$KEY]['LAST_MODIFIED'] : 0;
            case 'sqlite':
                $_DATA = array();
                try {
                    $db = self::$_ns[$NAMESPACE]['OBJECT'];
                    $sql = $db->prepare( "SELECT last_modified FROM '" . $keyCollection . "' WHERE key=:key" );
                    $sql->bindValue( ':key', $KEY, SQLITE3_TEXT );
                    $_DATA = $sql->execute();
                } catch ( \Exception $ex ) {
                    return FALSE;
                }
                return isset( $_DATA['last_modified'] ) ? $_DATA['last_modified'] : 0;
            case 'memcache':
                $_keys = self::$_ns[self::$currNS]['RESOURCE']->get( $keyCollection );
                return isset( $_keys[$KEY] ) ? $_keys[$KEY] : 0;
        endswitch;
    }

    /**
     * GETS LIFE DEFAULT TIME OF CURRENT NAMESPACE
     * @return int 
     */
    public static function getLifeDefault()
    {
        if ( !isset( self::$_ns[self::$currNS] ) ):
            return 0;
        endif;

        return self::$_ns[self::$currNS]['LIFE_DEFAULT'];
    }

    /**
     * DELETE specific data 
     * @param string $KEY 
     */
    public static function delete( $KEY )
    {
        if ( !isset( self::$_ns[self::$currNS] ) ):
            return FALSE;
        endif;


        $keyCollection = md5( self::$currNS );

        switch ( self::$_ns[self::$currNS]['SYSTEM'] ):
            case 'files':
                $KEY = self::$currNS . '-' . $KEY;
                $f = self::$_ns[self::$currNS]['RESOURCE'] . $KEY . self::$_ns[self::$currNS]['EXTENSION'];
                if ( is_file( $f ) ):
                    unlink( $f );
                    return TRUE;
                endif;
                return FALSE;
            case 'apc':
                $life = self::$_ns[self::$currNS]['LIFE_DEFAULT'];
                if ( self::$runtimeLife !== FALSE ):
                    $life = self::$runtimeLife;
                    self::$runtimeLife = FALSE;
                endif;
                //delete key from the keys collection
                $_keys = apc_fetch( $keyCollection );
                if ( $_keys ):
                    unset( $_keys[$KEY] );
                    apc_store( $keyCollection, $_keys, $life );
                endif;
                apc_delete( $KEY );
                break;
            case 'shm':
                $keyCollection = substr( base_convert( $keyCollection, 16, 10 ), -5 );
                $shmid = shmop_open( $keyCollection, "a", 0, 0 );

                if ( !$shmid ):
                    return FALSE;
                endif;

                $_DATA = shmop_read( $shmid, 0, shmop_size( $shmid ) );
                if ( !$_DATA ):
                    return FALSE;
                endif;

                $_DATA = unserialize( $_DATA );

                if ( !isset( $_DATA[$KEY] ) ):
                    return FALSE;
                endif;

                unset( $_DATA[$KEY] );
                shmop_delete( $shmid );
                shmop_close( $shmid );

                $_DATA = serialize( $_DATA );
                $shmid = shmop_open( $keyCollection, "c", 0644, strlen( $_DATA ) );
                shmop_write( $shmid, $_DATA, 0 );
                shmop_close( $shmid );
                return TRUE;
            case 'sqlite':
                try {
                    $db = self::$_ns[$NAMESPACE]['OBJECT'];
                    $sql = $db->prepare( "DELETE FROM '" . $keyCollection . "' WHERE key=?" );
                    $sql->bindValue( ':key', $KEY, SQLITE3_TEXT );
                    $sql->execute();
                    return TRUE;
                } catch ( \Exception $ex ) {
                    return FALSE;
                }
                break;
            case 'memcache':
                $life = self::$_ns[self::$currNS]['LIFE_DEFAULT'];
                if ( self::$runtimeLife !== FALSE ):
                    $life = self::$runtimeLife;
                    self::$runtimeLife = FALSE;
                endif;
                //delete key from the keys collection
                $_keys = self::$_ns[self::$currNS]['RESOURCE']->get( $keyCollection );
                if ( $_keys ):
                    unset( $_keys[$KEY] );
                    self::$_ns[self::$currNS]['RESOURCE']->set( $keyCollection, $_keys, MEMCACHE_COMPRESSED, $life );
                endif;

                self::$_ns[self::$currNS]['RESOURCE']->delete( $KEY );

                break;
        endswitch;
    }

    /**
     * This overrides global $LIFE_DEFAULT for one runtime execution
     * Invoke this method anytime before writting data to cache if using apc | memcache and when reading for typeof files
     * @param int $TIME Time in seconds to expire a key at runtime.
     * @return void 
     */
    public static function expire( $TIME )
    {
        self::$runtimeLife = $TIME;
    }

}