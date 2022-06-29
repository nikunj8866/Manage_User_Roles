(function( $ ) {
	'use strict';
	//Search user on input
	var $searchAjax = false;
	var timer = null;
	
	$(document).on("input", "#manage-user-roles-username", function(e){
	
		clearTimeout(timer); 
		timer = setTimeout(getSuggestResult, 1000)
    });
	function getSuggestResult(){
		var $input = $('#manage-user-roles-username');
        if($input.val().length > 2)
        {
			$input.addClass('processing')
			if($searchAjax)
			{
				$searchAjax.abort();
			}
            $searchAjax = $.ajax({
					dataType: 'json',
					url: manage_user_roles.ajax_url,
					data: {
						terms: $input.val(),
						action: 'manage_user_roles_search',
					},
					beforeSend: function(){
						$input.addClass('processing')
						$('.search-result').remove();
					},
					success: function(data) {
						if(data)
						{
							if(data.length > 0)
							{
								var $html = '<ul class="search-result">';
								$.each(data, function( index, value ) {
									$html +='<li><span data-value="'+value.value+'">'+value.label+'</span></li>';
								});
								$html +='</ul>';
								$input.after($html)
							}

						}
						$input.removeClass('processing')
					},
					error: function(){
						$input.removeClass('processing')
					}
			});
		}
	}
    
	var $userAjax = false;
	$(document).on("click", '.search-result li span', function(){
		var $val = $(this).attr('data-value');
		var $formElement = $('#manage-user-roles_user');
		$('#manage-user-roles-username').val($val);
		$('.search-result').remove();
		if($userAjax)
		{
			$userAjax.abort();
		}
		$userAjax = $.ajax({
				type: "POST",
				dataType: 'json',
				url: manage_user_roles.ajax_url,
				data: {
					user: $val,
					action: 'get_user_roles_data',
				},
				beforeSend: function(){
					$formElement.addClass('processing')
				},
				success: function(data) {
					if(data)
					{
						$formElement.find('table tbody').html(data.html);
						$('.user-role-manage-table').show();
						$('#manage-user-roles_save_user').show();
					}
					$formElement.removeClass('processing')
				},
				error: function(){
					$formElement.removeClass('processing')
				}
		});
	});
	$(document).on("keypress", '#manage-user-roles_user', function (event) {
		var keyPressed = event.keyCode || event.which;
		if (keyPressed === 13) {
			event.preventDefault();
            return false;
		}
	});
	$(document).on("submit", '#manage-user-roles_user', function(e){
		e.preventDefault();
		var $formElement = $(this);
		var $btn = $('#manage-user-roles_save_user');
		var $data = new FormData(this);

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: manage_user_roles.ajax_url,
			data: $data,
			processData: false,
			contentType: false,
            cache: false,
			beforeSend: function(){
				$formElement.addClass('processing');
				$btn.addClass("processing")
				$('#setting-error-manage_user_role_messages').remove()
			},
			success: function ( data ) {
				if(data)
				{
					if(data.status == 'success')
					{
						var $htmlMsg = '<div id="setting-error-manage_user_role_messages" class="notice notice-success settings-error"><p><strong>'+data.msg+'</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button><button type="button" class="notice-dismiss"><span class="screen-reader-text"></span></button></div>';
					}
					else
					{
						var $htmlMsg = '<div id="setting-error-manage_user_role_messages" class="notice notice-error settings-error"><p><strong>'+data.msg+'</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button><button type="button" class="notice-dismiss"><span class="screen-reader-text"></span></button></div>';
					}
					$('#manage-user-roles_user').before($htmlMsg)
				}
				$formElement.removeClass('processing');
				$btn.removeClass("processing")
				reSet_form();
				
			},
			error: function(){
				var $htmlMsg = '<div id="setting-error-manage_user_role_messages" class="notice notice-error settings-error"><p><strong>Something went wrong! Please try again letter</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button><button type="button" class="notice-dismiss"><span class="screen-reader-text"></span></button></div>';
				$('#manage-user-roles_user').before($htmlMsg)
				$formElement.removeClass('processing');
				$btn.removeClass("processing")
				reSet_form();
			}
		});
	});

	function reSet_form()
	{
		var $formElement = $('#manage-user-roles_user');
		$formElement[0].reset();
		// $formElement.find("input[type='checkbox']").prop('checked', false);
		// $formElement.find("select").val("customer");
		// $formElement.find("select").attr("disabled", true);
		$('.user-role-manage-table').hide();
		$('#manage-user-roles_save_user').hide();
		$("html, body").animate({ scrollTop: 0 });
		// setTimeout(function() {
		// 	$('#setting-error-manage_user_role_messages').fadeOut()
		// }, 1000);
	}

	$(document).on("change", "input[name='is-user-active[]']", function(){
		if(this.checked) {
			$(this).parents('tr').find('select').attr("disabled", false);
		}
		else
		{
			$(this).parents('tr').find('select').attr("disabled", true);
		}
	})
	$(document).on("change", "#check-uncheck-all-site", function(){
		if(this.checked) {
			$("input[name='is-user-active[]']").prop("checked", true);
			$("input[name='is-user-active[]']").trigger("change")
		}
		else
		{
			$("input[name='is-user-active[]']").prop("checked", false);
			$("input[name='is-user-active[]']").trigger("change")
		}
	})


})( jQuery );
