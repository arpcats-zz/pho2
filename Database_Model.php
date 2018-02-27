<?php
/**
 * 
 * 
 * Purpose: Database Model
 * File Name: Database_Model.php
 * Class Name: Database_Model
 * Author: Anthony Payumo
 * Email: 1010payumo@yahoo.com
 * Git Repo: github.com/arpcats
 * 
 * Note: show_sql = TRUE is showing query debugging 
 * 
*/

Class Database_Model
{
	public $con;
	private static $instance;
	
	public function __construct()
	{
		$conf = Config::get_instance();
		if (isset($conf->host) && isset($conf->username) && isset($conf->password)) 
		{
			$link = mysqli_connect($conf->host, $conf->username, $conf->password, $conf->database);
			if (!$link) 
			{
				die('Could not connect to your MySQL server'. mysqli_error($this->con));
				exit;    
			}

			$this->con = $link;  
			if ($conf->database);
				$this->arp_select_db($conf->database);
		}   
	}

	public static function get_instance () 
	{
		if (is_null(self::$instance)) 
		{ 
			self::$instance = new Database_Model();
		}
		return self::$instance;
	}

	public function arp_select_db($database) 
	{ 
		if (!mysqli_select_db($this->con, $database)) 
		{
			die('Connection refuse ::..:|:..:: database name <u>'.$database.'</u>'. mysqli_error($this->con));
		}
	}
	
	public function arp_close_db()
	{
		mysqli_close($this->con);
	}
   
	public function arp_get_record($table, $con='', $field = '',  $show_sql = false)
	{
		$field = $field ? $field : '*';
		$sql = "SELECT {$field} FROM {$table} {$con}";

		if($show_sql)  print($sql);
		else  $data = mysqli_query($this->con, $sql) or die ("Invalid: ::..:|:..:: {$sql} <br>". mysqli_error($this->con));

		return $data;
	}
   	
	public function arp_save_record($table, $arr, $con = 'insert', $qoute = false, $show_sql = false)
	{
		$field = "";
		$values = "";
		$set = "";
		$count = count($arr);
		foreach($arr as $key => $val)
		{
			if($count > 1)
			{
				$field .= ",{$key}";
				$values .= ",'{$val}'";
				$set .= ",{$key} = '{$val}'";
			}
			else
			{	/*dont remove spaces*/ 
				$field .= " {$key}";
				$values .= " '{$val}'";
				$set .= " {$key} = '{$val}'" ;
			}
		}
		
		if($qoute)
		{	/*check if db operator cannot use single or double qoute*/
			$flds = substr($field,1);
			$qvals = explode("'",$values);
			$qset = explode("'",$set);
			foreach($qvals as $val){$vals .= $val;}
			foreach($qset as $upd){ $setvals .= $upd;}
			
			$vals = substr($vals,1);
			$setvals = substr($setvals,1);
		}
		else
		{
			$flds = substr($field,1);
			$vals = substr($values,1);
			$setvals = substr($set,1);
		}

		if($con == "update")
			$sql = "UPDATE {$table} SET {$setvals} {$con};";
		else if($con == "insert")
			$sql = "INSERT INTO {$table} ({$flds}) VALUES ({$vals});";
		
		if($show_sql)
		{
			print($sql);
		}
		else
		{
			mysqli_query($this->con, $sql) or die("Invalid ::..:|:..:: {$sql} <br>". mysqli_error($this->con));
			if($con == "insert")
				return mysqli_insert_id($this->con);	
		}
	}
	
	public function arp_delete($table, $con = '', $show_sql = false)
	{
		$sql = "DELETE FROM {$table} {$con}";
		/*$conn = mysql_affected_rows($this->con);*/
		
        if($show_sql)
            print($sql);
        else
            $data = mysqli_query($this->con, $sql) or die("Invalid ::..:|:..:: {$sql} <br>". mysqli_error($this->con));
       
		return $data;
	}
	
	/*create table <name> (column datatype);*/
	public function arp_create_table($table, $arr, $show_sql = false)
	{
		$val = "";
		if(is_array($arr))
		{
			$count = count($arr);
			foreach($arr as $col => $dtype)
			{
				if($count > 1)
					$val .= ", {$col} {$dtype}";
				else
					$val .= " {$col} {$dtype}";   
			}
		}

		$gval = substr($val, 1);
		$sql = "CREATE TABLE {$table} ({$gval})";

		if($show_sql)
		{
			print($sql);
		}
		else
		{
			$data = mysqli_query($this->con, $sql) or die("Invalid ::..:|:..:: {$sql} <br> ". mysqli_error($this->con));
			mysql_close($this->con);
		}

		return $data;
	}

	public function arp_drop_table($table, $show_sql = false)
	{
		$sql = "DROP TABLE {$table}";
		if($show_sql)  return $sql;
		else  mysqli_query($this->con, $sql);
	}

	public function arp_sanitize_sql($str)
	{
		if( function_exists( "mysql_real_escape_string" ) )
			$ret_str = mysqli_real_escape_string($this->con, $str);
		else
			$ret_str = addslashes( $str );

		return $ret_str;
	}   

	public function arp_obj_rows($data = "")
	{
		$res = $data ? mysqli_fetch_object($data) : "";
		return $res; 
	}  

	public function arp_arr_rows($data = "")
	{
		$res = $data ? mysqli_fetch_array($data) : "";
		return $res;
	}

	public function arp_nums($data = "")
	{
		$res = $data ? mysqli_num_rows($data) : "";
		return $res;  
	}

}  //end class     
?>

