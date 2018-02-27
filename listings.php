<?php
require_once("config.php");
require_once("Database_Model.php");

$db = Database_Model::get_instance();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>BNSPH CENTRAL PORTAL</title>

	<link rel="stylesheet" href="assets/font-awesome-4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="assets/bs/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="assets/datatables/css/jquery.datatables.min.css"/>
	<link rel="stylesheet" href="assets/main.css">
</head>
<body>
	<br><br>
	<div class="container">
		<div class="row">
			<div class="col-sm-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<div class="row">
							<div class="col-sm-9">
								<h3 class="panel-title">Listings</h3>
							</div>
							<div class="col-sm-3 text-right">
								<a href="index.php">Add</a>
							</div>
						</div>
					</div>
					<div class="panel-body">
						<div class="table-responsive">
							<table id="listings" class="table table-condensed">
								<thead>
									<tr>
										<th>SysId</th>
										<th>UserId</th>
										<th>Title</th>
										<th>Description</th>
										<th width="150px">Mobile#</th>
										<th width="160px">Date Created</th>
										<th width="10px">Action</th>
									</tr>
								</thead>
								<tbody>
									<?php 
									$record = $db->arp_get_record("pho2_listings"); 
									if($record)
									{
										foreach($record as $r)
										{
											$html[] = "
											<tr id='row".$r["id"]."' >
												<td>".$r["id"]."</td>
												<td>".$r["user_id"]."</td>
												<td>".$r["title"]."</td>
												<td>".$r["description"]."</td>
												<td>".$r["mobile_number"]."</td>
												<td>".date("M d, Y h:i:s", strtotime($r["date_added"]))."</td>
												<td>
													<div class='btn-group'>
														<button type='button' class='btn btn-sm btn-default dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Action <span class='caret'></span></button>
														<ul class='dropdown-menu dropdown-menu-right'>
															<li><a onclick='action(`".base64_encode($r["id"])."`, `view`)' href='javascript:void(0)'><i class='fa fa-eye'></i> View</a></li>
															<li><a onclick='action(`".base64_encode($r["id"])."`, `edit`)' href='javascript:void(0)'><i class='fa fa-pencil'></i> Edit</a></li>
															<li><a onclick='action(`".base64_encode($r["id"])."`, `delete`)' href='javascript:void(0)'><i class='fa fa-trash'></i> Delete</a></li>
														</ul>
													</div>
												</td>
											</tr>
											";
										}
										echo implode("", $html);
									}
									?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="modal fade" id="listing-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content"></div>
		</div>
	</div>

	<br>
	<script src="assets/jquery-3.2.1.min.js"></script>
	<script src="assets/bs/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="assets/datatables/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="assets/main.js"></script>
	<script>
	$(document).ready(function() {
		
		$('#listings').DataTable( {
			"columnDefs": [ {
				"targets": [6], // column or columns numbers
				"orderable": false,  // set orderable for selected columns
			}],
		} );
		
		action = function(id, type){
			if(type == "view"){
				$('#listing-modal').modal({
				  keyboard: false,
				  backdrop: 'static'
				});
				
				$.post("Controller.php", $.param({id:id, action:type}), function(data){
					dINFO = data.info;
					dSRC = data.resource;
					
					header = '\
					<div class="modal-header">\
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>\
						<h4 class="modal-title" id="myModalLabel">'+dINFO['title']+'</h4>\
					</div>\
					';
					
					var items = indicators = "";
					for (var r in dSRC) {
						
						ctr_class = (r==0) ? 'active' : "";
						indicators = '<li data-target="#carousel-example-generic" data-slide-to="'+r+'" class="'+ctr_class+'"></li>';
						items += '\
						<div class="item '+ctr_class+'">\
							<img class="img-responsive center-block" src="'+dSRC[r].original+'" alt="'+dSRC[r].filename+'">\
							<div class="carousel-caption">'+dSRC[r].filename+'</div>\
						</div>\
						';
					}
					
					content = '\
					<div class="modal-body arp-nopadding">\
						<div class="table-responsive">\
							<table class="table table-condensed">\
								<tr><td>System Id</td><td>'+dINFO['id']+'</td></tr>\
								<tr><td>User Id</td><td>'+dINFO['user_id']+'</td></tr>\
								<tr><td>Mobile Number</td><td>'+dINFO['mobile_number']+'</td></tr>\
								<tr><td>Date Created</td><td>'+dINFO['date_added']+'</td></tr>\
								<tr><td colspan="2">Description: '+dINFO['description']+'</td></tr>\
							</table>\
						</div>\
						<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">\
							'+indicators+'\
							<div class="carousel-inner" role="listbox">'+items+'</div>\
							<a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">\
								<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>\
								<span class="sr-only">Previous</span>\
							</a>\
							<a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">\
								<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>\
								<span class="sr-only">Next</span>\
							</a>\
						</div>\
					</div>\
					';
					
					footer = '<div class="modal-footer arp-padding5"><button type="button" class="btn btn-danger" data-dismiss="modal">Close</button></div>';
					
					$("#listing-modal .modal-content").html(header+content+footer);
				},"json");
				
			}else if(type == 'edit'){
				window.location.href = baseUrl("index.php?eid="+id+"&action=edit");
			}else if(type == 'delete'){
				r = confirm("Are you sure want to delete!");
				if(r == true){
					$.post("Controller.php", $.param({id:id, action:type}), function(){
						$("#row"+base64_decode(id)).addClass("danger").fadeOut("slow");
					},"json");
				}
			}
		}
		
	} );
	</script>
</body>

</html>
