function utf8_decode(str_data){var tmp_arr=[],i=0,ac=0,c1=0,c2=0,c3=0;str_data+='';while(i<str_data.length){c1=str_data.charCodeAt(i);if(c1<128){tmp_arr[ac++]=String.fromCharCode(c1);i++;}else if((c1>191)&&(c1<224)){c2=str_data.charCodeAt(i+1);tmp_arr[ac++]=String.fromCharCode(((c1&31)<<6)|(c2&63));i+=2;}else{c2=str_data.charCodeAt(i+1);c3=str_data.charCodeAt(i+2);tmp_arr[ac++]=String.fromCharCode(((c1&15)<<12)|((c2&63)<<6)|(c3&63));i+=3;}}
return tmp_arr.join('');}
function utf8_encode(argString){var string=(argString+'');var utftext="";var start,end;var stringl=0;start=end=0;stringl=string.length;for(var n=0;n<stringl;n++){var c1=string.charCodeAt(n);var enc=null;if(c1<128){end++;}else if(c1>127&&c1<2048){enc=String.fromCharCode((c1>>6)|192)+String.fromCharCode((c1&63)|128);}else{enc=String.fromCharCode((c1>>12)|224)+String.fromCharCode(((c1>>6)&63)|128)+String.fromCharCode((c1&63)|128);}
if(enc!==null){if(end>start){utftext+=string.substring(start,end);}
utftext+=enc;start=end=n+1;}}
if(end>start){utftext+=string.substring(start,string.length);}
return utftext;}

function base64_decode(data){var b64="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";var o1,o2,o3,h1,h2,h3,h4,bits,i=0,ac=0,dec="",tmp_arr=[];if(!data){return data;}
data+='';do{h1=b64.indexOf(data.charAt(i++));h2=b64.indexOf(data.charAt(i++));h3=b64.indexOf(data.charAt(i++));h4=b64.indexOf(data.charAt(i++));bits=h1<<18|h2<<12|h3<<6|h4;o1=bits>>16&0xff;o2=bits>>8&0xff;o3=bits&0xff;if(h3==64){tmp_arr[ac++]=String.fromCharCode(o1);}else if(h4==64){tmp_arr[ac++]=String.fromCharCode(o1,o2);}else{tmp_arr[ac++]=String.fromCharCode(o1,o2,o3);}}while(i<data.length);dec=tmp_arr.join('');dec=this.utf8_decode(dec);return dec;}
function base64_encode(data){var b64="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";var o1,o2,o3,h1,h2,h3,h4,bits,i=0,ac=0,enc="",tmp_arr=[];if(!data){return data;}
data=this.utf8_encode(data+'');do{o1=data.charCodeAt(i++);o2=data.charCodeAt(i++);o3=data.charCodeAt(i++);bits=o1<<16|o2<<8|o3;h1=bits>>18&0x3f;h2=bits>>12&0x3f;h3=bits>>6&0x3f;h4=bits&0x3f;tmp_arr[ac++]=b64.charAt(h1)+b64.charAt(h2)+b64.charAt(h3)+b64.charAt(h4);}while(i<data.length);enc=tmp_arr.join('');switch(data.length%3){case 1:enc=enc.slice(0,-2)+'==';break;case 2:enc=enc.slice(0,-1)+'=';break;}
return enc;}

baseUrl = function(paramLink){
	var getUrl = window.location;
	var host = getUrl .protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1];
	if(paramLink){
		var url = host+'/'+paramLink;
	}else{
		var url = host;
	}
	
	return url;
}	
	
var title_size = 150;
var description_size = 5000;

$.validator.setDefaults( {
	submitHandler: function () {
		var photoCheck = ((typeof photoSend == "photoSend") ? photoSend() : true);
		if(photoCheck == true){
			var $form 	= $(".photo-form"),
			formData 	= new FormData(),
			params   	= $form.serializeArray();

			formData.append("action","save");		
			$.each(params, function(i, val) { formData.append(val.name, val.value); });
			
			$(".photo-form button[type='submit'] ").html("Loading... <i class='fa fa-spinner fa-lg fa-spin'></i>").attr("disaled","disabled");
			$.ajax({
				url: 'Controller.php',
				data: formData,
				cache: false,
				contentType: false,
				processData: false,
				type: 'POST',
				dataType: 'json',
				success: function(data) {
					if(data.result == true){
						$(".photo-form button[type='submit'] ").html("Submit").removeAttr("disabled");
						$(".photo-form")[0].reset();
						$("#template-preview .dz-image-preview").remove();
					}else{
						alert("failed");
					}
				}
			});
		}
	}
} );

$( ".photo-form" ).validate( {
	rules: {
		title:{
			required : true,
			minlength : 3,
			maxlength : title_size,
		},
		description:{
			required : true,
			minlength : 10,
			maxlength : description_size,
		},
		mobile:{
			required : true,
			maxlength : 11,
			number:	true,
		},
	},
	messages: {
		title: {
			required: "Title required",
			minlength: "Minimum 3 characters!",
			maxlength: "Maximum 150 characters!",
		},
		description: {
			required: "Description required",
			minlength: "Minimum 10 characters!",
			maxlength: "Maximum 350 characters!",
		},
		mobile: {
			required: "Mobile Number required",
			maxlength: "Complete 11 digits mobile no.!",
		},
	},
	errorElement: "em",
	errorPlacement: function ( error, element ) {
		error.addClass( "text-danger fderr-msg" );
		error.insertAfter( element );
	},
} );

$("input.title").keyup(function() {
	var maxLength = title_size;
	var length = $.trim(this.value).length;
	$(".limit-title").text(length+' / '+(maxLength));
});

$("textarea.description").keyup(function() {
	var maxLength = description_size;
	var length = this.value.length;
	$(".limit-desc").text(length+' / '+(maxLength));
});

photoSend = function(){
	dzsize = $("#dropzone .dz-processing.dz-image-preview.dz-success.dz-complete").length;
	if(dzsize > 0){
		$(".dzmsg-err em").remove();
		return true;
	}else{
		$(".dzmsg-err").html('<em class="text-danger fderr-msg">Require: Please upload photo at least one!</em>');
		return false;
	}
}

Dropzone.autoDiscover = false;
$('#dropzone').dropzone({
	url: 'upload.php',
	thumbnailWidth: 100,
	thumbnailHeight: 80,
	clickable: ".arp-myclick",
	paramName: "userfile[]",
	maxFile: 12,
	acceptedFiles: ".png,.jpg,.gif,.bmp,.jpeg",
	previewTemplate: document.getElementById('template-preview').innerHTML,
	
	dictMaxFilesExceeded: "You can only upload 12 image",
	dictDefaultMessage: "<abbr title='Clicl Here to upload photos'>Click</abbr> or Drag and Drop your photos here",
	dictCancelUpload: "Cancel",
	dictCancelUploadConfirmation: "Are you sure you want to cancel this upload?",
	dictRemoveFile: "Remove",
	
	init: function() {
		var countphoto = 12;
		var $objcountphoto = $(".arp-countphoto");
		var $objdzmsg = $(".arp-dz-message");
		
		this.on('addedfile', function(file) {
			startcount = this.files.length;
			endcount = (countphoto - startcount);
			
			$objdzmsg.hide();
			if(startcount <= countphoto){
				$objcountphoto.html( startcount +" / "+endcount);
			}
			else{
				alert("Sorry: You reach exceeded number of IMAGES!");
				this.removeFile(this.files[0]);
			}

			/*show first before my file*/
			$('#template-preview').prepend($(file.previewElement));
		});
		
		this.on('removedfile', function(file) {
			startcount = this.files.length;
			endcount = (countphoto - startcount);
			
			if(startcount <= countphoto){
				$objcountphoto.html(startcount+" / "+endcount);
			}
			
			if(startcount == 0){
				$objdzmsg.show();
			}
		});
		
		this.on("success", function(file, response) {
			var obj = jQuery.parseJSON(response)
			var newfld = "";
			for(n in obj[0]){
				newfld = Dropzone.createElement("<div class='bns_newfld'><input type='hidden' name='"+n+"[]' value='"+obj[0][n]+"'></div>");
				file.previewElement.appendChild(newfld);
			}

			file.previewElement.querySelector(".dzprimary").innerHTML = ('<div class="radio  pull-left arp-nomargin arp-nopadding"><label><input name="primary" value="'+base64_encode(obj[0]["imgDatename"])+'" type="radio"><small class="hidden-xs hidden-sm">Primary</small></label></div>');
			$(".dropzone .dz-processing .arpdz-action .dzremove").html('<span class="pull-right"><i class="fa fa-remove fa-lg text-danger"></i></span>');
			$(".dropzone .dz-processing .arpdz-action .arpdz-note").remove();
		});

		this.on("error", function(file, errorMessage) {
			if (errorMessage.indexOf('Error 404') !== -1) {
				var errorDisplay = document.querySelectorAll('[data-dz-errormessage]');
				errorDisplay[errorDisplay.length - 1].innerHTML = 'Error 404: The upload page was not found on the server';
			}
		});
		
		this.on('uploadprogress', function(file, progress, sent) {
			if (file.previewElement) {
				var progressElement = file.previewElement.querySelector("[data-dz-uploadprogress]");
				progressElement.style.width = progress + "%";
				progressElement.querySelector(".progress-text").textContent = progress + "%";
			}
		});
		
		this.on('maxfilesreached', function() {
			$('.dropzone').removeClass('dz-clickable'); // remove cursor
			$('.dropzone')[0].removeEventListener('click', this.listeners[1].events.click);
			alert("Sorry: You reach exceeded number of IMAGES!");
		});
	}
});














removePhoto = function(elem, id){
	auth = $(elem).data("auth");
	if(auth){
		$.post(baseUrl("post-ad/remove-resources"), $.param({rid:auth}), function(data){
			if(data.result == true){
				if(parseInt(data.count) <= 1){
					$(".arp-rmphoto").remove();
				}
				
				$(".currlist"+id).addClass("arp-bgred").fadeOut("slow");
			}else{
				alert(data.error_msg);
			}
		},"json");
	}
}
