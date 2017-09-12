$(function() {
	$("#fileupload").fileinput({
		language: "ru",
		showUpload: false,
		showCaption: false,
		showRemove: false,
		fileActionSettings: {
			showZoom: false
		},
		allowedFileExtensions : ['jpg', 'png','pdf','doc'],		
		autoReplace: true,
		overwriteInitial: true,
		showUploadedThumbs: true,
		maxFileCount: 1,
		maxFileSize : 1024,// 1024kb = 1mb
		dropZoneEnabled  : false,
		initialPreviewShowDelete: false,
		layoutTemplates: {actionDelete: ''},		
		previewFileIcon: "<i class='glyphicon glyphicon-king'></i>"
	}).on('dragover drop', function(e) {
		e.preventDefault();		
	});
	
	  $("#fileupload2").fileinput({
		language: "ru",
		showUpload: false,
		showCaption: false,
		showRemove: false,
		fileActionSettings: {
			showZoom: false
		},
		allowedFileExtensions : ['jpg', 'png','pdf','doc'],		
		autoReplace: true,
		overwriteInitial: true,
		showUploadedThumbs: true,
		maxFileCount: 1,
		maxFileSize : 1024,// 1024kb = 1mb
		dropZoneEnabled  : false,
		initialPreviewShowDelete: false,
		layoutTemplates: {actionDelete: ''},		
		previewFileIcon: "<i class='glyphicon glyphicon-king'></i>"
	});
	
	$('#contactForm').submit(function(event) {    
		event.preventDefault();    
		var formData = new FormData(document.forms.contactForm);		
		$.ajax({		
			type: "POST",		
			url: "/process.php",        
			data: formData,
			dataType : "json",
			processData: false,			    
			contentType: false,
			cache: false,	    	
			success : function(data){				
				// скрываем форму обратной связи
				$('#contactForm').hide();
				$('#error').html('');
				// удаляем у элемента, имеющего id=successMessage, класс hidden
				$('#successMessage').removeClass('hidden');
			},
			error: function (request) {				
				$('#error').text(request.responseJSON.message);
			}        
		});
	})	 
});
