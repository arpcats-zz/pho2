<?php
/**
 * 
 * 
 * Purpose: Database Configuration
 * File Name: config.php
 * Class Name: Arp_Config
 * Author: Anthony Payumo
 * Email: 1010payumo@yahoo.com
 * Git Repo: github.com/arpcats
 * 
*/

class Config {
    private static $instance;
    
	public function __construct()
	{
        /*MySQL INIT*/
        $this->host 	= "localhost";
        $this->port 	= 3306;
        $this->username = "root";
        $this->password = "";
        $this->database = "pho2";
    }
    
    public static function get_instance () 
    {
        if (is_null(self::$instance)) 
        {
            self::$instance = new Config();
        }
        return self::$instance;
    }
}
?>
