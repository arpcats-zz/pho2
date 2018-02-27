<?php
/**
 * 
 * 
 * Purpose: Uploading multiple photos with dropzone plugins
 * File Name: Photo_Upload.php
 * Class Name: Photo_Upload
 * Author: Anthony Payumo
 * Email: 1010payumo@yahoo.com
 * Git Repo: github.com/arpcats
 * 
*/

define("MAX_SIZE","1400");
Class Photo_Upload
{
	public function __construct()
	{
	
	}
	
	public function get_photo($name)
	{
		$data = $this->set_photo($name);
		return $data;
	}
	
	public function set_photo($filename)
	{
		if(!empty($_FILES[$filename]))
		{
            $filesCount = count($_FILES[$filename]['size']);
            $pathDirectory = 'assets/dropzone/images/';
			
			$baseDIR = $pathDirectory;
			$newDIR = $baseDIR.date('Y/m/d/');
			
			/*Check file exists and writable*/
			if(!file_exists($newDIR) AND is_writable($baseDIR)) 
			{
				mkdir($newDIR, 0755, true);
				@chmod($newDIR, 0777);
				@chmod($baseDIR.date('Y'), 0777);
				@chmod($baseDIR.date('Y/m'), 0777);
			}

            foreach($_FILES as $k => $v)
            {
                for ($s=0; $s <= ($filesCount-1); $s++)
                {
                    $_FILES[$filename]['name'] 		= $v['name'][$s];
                    $_FILES[$filename]['type'] 		= $v['type'][$s];
                    $_FILES[$filename]['tmp_name'] 	= $v['tmp_name'][$s];
                    $_FILES[$filename]['error'] 	= $v['error'][$s];
                    $_FILES[$filename]['size'] 		= $v['size'][$s];
                    
					/*get image extension*/
					$current_extension = explode(".", $this->clean_file($_FILES[$filename]['name']));
					$ext = end($current_extension);
					
					srand((double)microtime()*1000000);
					$datename = rand(1000000, 9999999);
                
					/*original*/
					$original = $newDIR."/".$datename.".".$ext;
					/*medium*/
					$medium = $newDIR."/medium_".$datename.".".$ext;
					/*thumbnail*/
					$thumb = $newDIR."/thumb_".$datename.".".$ext;

					/*$dimension = getimagesize($_FILES[$filename]['tmp_name']);*/
					$this->copy_resize($medium, $_FILES[$filename]['tmp_name'], 200);
					$this->copy_resize($thumb, $_FILES[$filename]['tmp_name'], 90);
					move_uploaded_file($_FILES[$filename]['tmp_name'], $original);
                
                    $info[] = array(
                    "filename" 	=> $_FILES[$filename]['name'],
                    "datename" 	=> $datename,
                    "original" 	=> $original,
                    "medium" 	=> $medium,
                    "thumbnail" => $thumb,
                    );
                }
			}/*end foreach*/
			
			return $info;
        }	
	}

	public function copy_resize($imagename, $filename, $newwidth)
	{
		$image = stripslashes($imagename);
		$extension = $this->file_extension($image);
		$extension = strtolower($extension);

		if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif")) 
		{
			$msg = '<div class="msgdiv">Unknown Image extension </div> ';
			#  $errors=1;
			return $msg;
		}
		else
		{
			$size = filesize($filename);
			if ($size > MAX_SIZE * 1024)
			{
				// echo   $change='<div class="msgdiv">You have exceeded the size limit!</div> ';
				#  $errors=1;
			}

			if($extension == "jpg" || $extension == "jpeg" )
			{
				$src = imagecreatefromjpeg($filename);
			}
			else if($extension == "png")
			{
				$src = imagecreatefrompng($filename);
			}
			else
			{
				$src = imagecreatefromgif($filename);
			}  
		}

		list($width,$height) = getimagesize($filename);
		$newheight = ($height/$width) * $newwidth;
		$tmp = imagecreatetruecolor($newwidth, $newheight);

		imagecopyresampled($tmp, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
		$filepath = $imagename;
		imagejpeg($tmp, $filepath, 80);
		imagedestroy($src);
		imagedestroy($tmp);
	}
	
	public function file_extension($str) 
	{
		$i = strrpos($str,".");
		if (!$i)
			return ""; 
		
		$l = strlen($str) - $i;
		$ext = substr($str,$i+1,$l);
		return $ext;
	}
   
    public function clean_file($file_name)
    {
		$bad = array(
		"<!--",
		"-->",
		"'",
		"<",
		">",
		'"',
		'&',
		'$',
		'=',
		';',
		'?',
		'/',
		"%20",
		"%22",
		"%3c",      // <
		"%253c",   // <
		"%3e",      // >
		"%0e",      // >
		"%28",      // (
		"%29",      // )
		"%2528",   // (
		"%26",      // &
		"%24",      // $
		"%3f",      // ?
		"%3b",      // ;
		"%3d"      // =
		);

		$file_name = str_replace($bad, '', $file_name);
		return stripslashes($file_name);  
    }
      
}
?>
