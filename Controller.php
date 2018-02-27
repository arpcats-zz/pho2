<?php
/**
 * 
 * 
 * Purpose: Saving Photos 
 * File Name: Controller.php
 * Class Name: N/A
 * Author: Anthony Payumo
 * Email: 1010payumo@yahoo.com
 * Git Repo: github.com/arpcats
 * 
*/

define("SYSTEM_CURRENT_DATE", date("Y-m-d H:i:s"));

require_once("config.php");
require_once("Database_Model.php");

$db = Database_Model::get_instance();

$pho2_id = !empty($_POST["id"]) ? $_POST["id"] : "";
$action = !empty($_POST["action"]) ? $_POST["action"] : "";

$title = !empty($_POST["title"]) ? $_POST["title"] : "";
$description = !empty($_POST["description"]) ? $_POST["description"] : "";
$mobile = !empty($_POST["mobile"]) ? $_POST["mobile"] : "";

/*IMAGE*/
$filename = !empty($_POST["filename"]) ? $_POST["filename"] : "";
$datename = !empty($_POST["datename"]) ? $_POST["datename"] : "";
$original = !empty($_POST["original"]) ? $_POST["original"] : "";
$medium = !empty($_POST["medium"]) ? $_POST["medium"] : "";
$thumbnail = !empty($_POST["thumbnail"]) ? $_POST["thumbnail"] : "";


if($action == "save")
{
	$db = Database_Model::get_instance();
	
	$info["user_id"] = 1;
	$info["title"] = $title;
	$info["description"] = $description;
	$info["mobile_number"] = $mobile;
	$info["date_added"] = SYSTEM_CURRENT_DATE;
	$listing_id = $db->arp_save_record("pho2_listings", $info, "insert");
	
	if(is_array($filename))
	{
		for($i=0; $i<count($filename); $i++)
		{
			$img["listing_id"] = $listing_id;
			$img["filename"] = $filename[$i];
			$img["datename"] = $datename[$i];
			$img["original"] = $original[$i];
			$img["medium"] = $medium[$i];
			$img["thumbnail"] = $thumbnail[$i];
			$img["ordered"] = $i;
			
			/* IMAGE COUNT ORDERING */
			$image_resources_count = isset($resources_count) ? $resources_count : 0;
			/* IMAGE DATE DIRECTORY FOLDER */
			$imgDIR = date("Y/m/d");
			$set_as_primary = false;
			
						
						
			$db->arp_save_record("pho2_listings_resources", $img, "insert");
		}
	}
	
	$db->arp_close_db();
	$arr["result"] = true;
	echo json_encode($arr);
	exit;
}
else if($action == "view")
{
	$pho2_id = base64_decode($pho2_id);
	$listings = $db->arp_get_record("pho2_listings", sprintf("WHERE id = %s", $pho2_id));
	if($listings)
	{
		$listing = $db->arp_obj_rows($listings);
		$arr["info"] = $listing;
		
		$resources = $db->arp_get_record("pho2_listings_resources", sprintf("WHERE listing_id = %s", $listing->id));
		foreach($resources as $r)
		{
			$img[] = $r;
		}
	
		$arr["resource"] = $img;
		echo json_encode($arr);
		exit;
	}
}
else if($action == "delete")
{
	$pho2_id = base64_decode($pho2_id);
	
	$db->arp_delete("pho2_listings", sprintf("WHERE id = %s", $pho2_id));
	$resources = $db->arp_get_record("pho2_listings_resources", sprintf("WHERE listing_id = %s", $pho2_id));
	if($resources)
	{
		foreach($resources as $r)
		{
			@unlink($r["original"]);
			@unlink($r["medium"]);
			@unlink($r["thumbnail"]);
		}
		
		$db->arp_delete("pho2_listings_resources", sprintf("WHERE listing_id = %s", $pho2_id));
	}
	
	$res["result"] = true;
	echo json_encode($res);
	exit;
}