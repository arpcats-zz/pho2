<?php
require_once("Photo_Upload.php");

$pu = new Photo_Upload();
$get_photo = $pu->get_photo("userfile");
if($get_photo)
{
	echo json_encode($get_photo);
	exit;
}
?>
