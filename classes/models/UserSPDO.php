<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserSPDO
 *
 * @author Alex
 */
class usersPDO extends PDO{
	private static $instance=null;

CONST dsn = 'mysql:host=localhost;dbname=test';
CONST user = 'root';
CONST password = '';

function __construct(){
    try{
        parent::__construct(self::dsn, self::user, self::password);
    }catch(PDOException $e){
        echo 'connection failed: '.$e->getMessage();
    }
}

public static function singleton()
            {
                    if( self::$instance == null )
                    {
                            self::$instance = new self();
                    }
                    return self::$instance;
            }

}
