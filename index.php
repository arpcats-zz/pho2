<?php
require_once("config.php");
require_once("Database_Model.php");

$db = Database_Model::get_instance();

$eid = isset($_GET["eid"]) ? $_GET["eid"] : "";
$action = isset($_GET["action"]) ? $_GET["action"] : "";

if($eid && $action == "edit")
{
	$title_head = "Edit";
	$eid = base64_decode($eid);
	if(is_numeric($eid))
	{
		$listings = $db->arp_get_record("pho2_listings", sprintf("WHERE id = %s", $eid));
		$listing = $db->arp_obj_rows($listings);

		$resources = $db->arp_get_record("pho2_listings_resources", sprintf("WHERE listing_id = %s", $listing->id));
	}
	else
	{
		//show something error;
	}
}
else
{
	$title_head = "Add";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Pho2 Gallery by Anthony Payumo</title>

	<link rel="stylesheet" href="assets/font-awesome-4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="assets/bs/css/bootstrap.min.css">
	<link rel="stylesheet" href="assets/dropzone/dropzone.css">
	<link rel="stylesheet" href="assets/main.css">
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="col-sm-3"></div>
			<div class="col-sm-6" id="arpPostingBox">
				
				<h3 class="text-muted">
					<?php echo $title_head?> Photos 
					<?php echo (isset($_GET['err']) ? "<small class='text-danger'>Invalid photo upload!</small>" : "");?>
					<div class="pull-right"><a href="listings.php" class="arp-size12">Listings</a></div>	
				</h3>
				
				<div class="photo-box">
					<form class="photo-form" method="POST" action="upload.php">
						<div class="form-group">
							<label>Add Photos <span class="text-danger">*</span></label>
							<div class="pull-right">
								<span class="arp-countphoto">
								<?php
								if(isset($resources->num_rows))
								{
									$total_resources = $resources->num_rows;
									echo $total_resources .' / '. (12 - $total_resources);
								}
								else
								{
									echo 0 .' / '. 12;
								}
								?>
								</span>
							</div>
							<div class="arpdz-photos">
								<div id="dropzone" class="dropzone clearfix row no-gutters">
									<div id="template-preview">
										<div class="col-xs-3 col-sm-3 arp-nopadding arp-myclick" >
											<div class="dz-preview dz-file-preview" id="dz-preview-template">
												<div class="dz-details">
													<div class="hide dz-filename"><span data-dz-name></span></div>
													<div class="hide dz-size" data-dz-size></div>
													<div class="post-photos">
														<img class="img-responsive center-block" src="assets/images/no_image.jpg" data-dz-thumbnail />
													</div>
												</div>
												
												<div class="progress progress-striped">
													<div class="progress-bar progress-bar-warning" role="progressbar" data-dz-uploadprogress>
														<span class="progress-text"></span>
													</div>
												</div>
												
												<div class="dz-success-mark"><span></span></div>
												<div class="dz-error-mark"><span></span></div>
												<div class="dz-error-message"><span data-dz-errormessage></span></div>
												<div class="arpdz-action">
													<div class="dzprimary"></div>
													<div class="dzremove" data-dz-remove></div>
													<div class="arpdz-note text-center text-primary"> Click Add <i class="fa fa-cloud-upload fa-lg"></i></div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="clearfix"></div>
							</div>
							<span class="dzmsg-err"></span>
						</div>

						<div class="current-resources">
						<?php if(!empty($resources)):?>
							<div class="row no-gutters crlist-box" data-rcount="<?php echo $total_resources;?>">
							<?php foreach($resources as $resource):?>
								<div class="col-xs-3 col-sm-3 currlist<?php echo $resource['resources_id']?>" id="currlist">
									<div class="dz-preview dz-file-preview" id="dz-preview-template">
										<div class="dz-details">
											<div class="post-photos">
												<img class="img-responsive center-block" src="<?php echo $resource['thumbnail'];?>">
											</div>
										</div>
										<div class="arp-padding1 arp-bgorange"></div>
										<div class="arpdz-action">
											<div class="radio  pull-left arp-nomargin arp-nopadding">
												<label>
													<input name="primary" value="<?php echo $resource['datename'];?>" type="radio" <?php echo ($resource['ordered']) ? "checked" : ""; ?>> 
													<small class="hidden-xs hidden-sm">Primary</small>
												</label>
											</div>
											<?php if($resources->num_rows > 1):?>
											<span class="pull-right">
												<i class="fa fa-remove fa-lg text-danger arp-rmphoto" data-auth="<?php echo base64_encode($resource['resources_id'].'-'.$resource['listing_id']);?>" onclick="removePhoto(this, `<?php echo $resource['resources_id']?>`)"></i>
											</span>
											<?php endif;?>
										</div>
									</div>
								</div>
							<?php endforeach;?>
							</div>
							<div class="clearfix"></div>
						<?php endif;?>
						</div>

						<div class="form-group">
							<label>Title <span class="text-danger">*</span></label>
							<span class="pull-right limit-title">0 / 150</span>
							<input type="text" name="title" class="form-control input-lg title" maxlength="150" placeholder="Title" value="<?php echo isset($listing->title) ? $listing->title : ""?>">
						</div>
						<div class="form-group">
							<label>Description <span class="text-danger">*</span></label>	
							<span class="pull-right limit-desc">0 / 5000</span>
							<textarea rows="10" name="description" class="form-control input-lg description" maxlength="5000" placeholder="Description"><?php echo isset($listing->description) ? $listing->description : ""?></textarea>
						</div>
						<div class="form-group">
							<label>Mobile Number <span class="text-danger">*</span></label>
							<input type="text" name="mobile" maxlength="11" required class="form-control input-lg" placeholder="(090x)-xxx-xxxx" value="<?php echo isset($listing->mobile_number) ? $listing->mobile_number : ""?>">
						</div>
						<?php if(isset($_GET['err'])):?>
						<a href="/pho2" class="btn btn-danger btn-block btn-lg">Back</a>
						<?php else:?>
						<button type="submit" class="btn btn-success btn-block btn-lg">Submit</button>
						<?php endif;?>
					</form>
				</div>
			</div>
			<div class="col-sm-3"></div>
		</div>
	</div>
	<br>
	<script src="assets/jquery-3.2.1.min.js"></script>
	<script src="assets/bs/js/bootstrap.min.js"></script>
	<script src="assets/dropzone/min/dropzone.min.js"></script>
	<script src="assets/jquery.validate.min.js"></script>
	<script src="assets/main.js"></script>
	<script src="assets/upload.js"></script>
</body>

</html>
