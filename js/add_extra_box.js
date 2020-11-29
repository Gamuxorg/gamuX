jQuery(document).ready(function() {
	$ = jQuery;
    var wrapper = $(".gamux-edit-upload-label");
    var add_button = $(".gamux-upload-add");
    add_button.on('click', function(e){
        e.preventDefault();
		var download_link_index = $('.gamux-edit-upload-option').size();
		wrapper.append(
			'<div class="gamux-edit-upload-option"><input type="text" placeholder="此处显示下载url，可粘贴外部url" name="downurl_'+
			download_link_index+'" class="gamux-up-input" /><input type="text" placeholder="版本说明" name="dtitle_'+download_link_index+
			'" class="gamux-text-input extra-text-input" />'+
			'<input type="text" placeholder="备注" name="comment_'+download_link_index+'" class="gamux-upload-comment">'+
			'<button type="button" class="gamux-up-button">上传</button><button type="button" class="gamux-upload-delete">-</button></div>');  
	}); 
	
	//删除下载链接
    $(wrapper).on("click",".gamux-upload-delete", function(e){
        e.preventDefault(); 
		$(this).parents('.gamux-edit-upload-option').remove();
    });

	//上传按钮
	var formfield_url = '';
	var formfield_title = '';
	var gamux_upload_frame;
	$('.gamux-edit-upload-label').on('click', '.gamux-up-button',function(e) {
		e.preventDefault();
		formfield_url = $(this).prevAll('.gamux-up-input');
		formfield_title = $(this).prevAll('.gamux-text-input');
		if( gamux_upload_frame ){   
			gamux_upload_frame.open();   
			return;   
		}	
		gamux_upload_frame = wp.media({   
			title: '上传新文件',   
			button: {   
				text: '插入',   
			},   
			multiple: false   
		});   

		gamux_upload_frame.on('select',function(){   
			attachment =gamux_upload_frame.state().get('selection').first().toJSON();
			$(formfield_url).val(attachment.url);
			$(formfield_title).val(attachment.title);
			console.log(attachment);
		});
		
		gamux_upload_frame.open();
	});

	//多个购买链接的增加、删除
	var gamux_buyurls = $("#gamux-buyurls");
	var gamux_buyurl_add = $("#gamux-buyurl-add");
	var gamux_buyurl_del = $("#gamux-buyurl-del");
	gamux_buyurl_add.on('click', function() {
		gamux_buyurls.append('<input style="width: 100%;" name="buy_url[]" value="">');
	});
	gamux_buyurl_del.on('click', function() {
		if($("#gamux-buyurls input").size() > 1)
			$("#gamux-buyurls input:last-child").remove();
	});
});
