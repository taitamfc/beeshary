$('document').ready(function(){
	bindEvents();
});

function bindEvents(){
	$("#wellcome_form").off('submit').on('submit',function(e){
		$(this).find('button').each(function(elm){
			$(this).removeClass('in');
		});
		$("#loading_frame").addClass('in');

		e.preventDefault();


		doRequest($(this).attr('action'),$(this).serializeArray(),'POST','json',function (data, jqXHR, textStatus) {
			if (data.status == 'error')
				$.each(data.error, function (k, e) {
					showFrameErrorMessage(e);
				});
			if (data.status == "ok") {



				if($("#update_service_register")){
					$("#update_service_register").modal('hide');
				}

				$("#wellcome_form").closest('.portlet').slideUp(500);


				$.each(data.confirmations, function (k, c) {
					showFrameSuccessMessage(c);
				});
			}

		}).always(function(){
			$("#wellcome_form").find('button').each(function(){
				$(this).addClass('in');
			});
			$("#loading_frame").removeClass('in');
		});


	});


	$('.update').off('click').on('click',function(){
		var $overlay = $(this).closest('.panel').children('.panel-overlay');
		$overlay.removeClass('hide');
		var $elm = $(this).closest('.panel');


		var $module = $(this).data('module');
		doRequest(currentIndex+'&token='+token+'&configure=inixframe&ajax=1&action=fetch&json=1',{module:$module},'POST','json',function (data, jqXHR, textStatus) {
			if (data.status == 'error')
				$.each(data.error, function (k, e) {
					showFrameErrorMessage(e);
				});
			if (data.status == "ok") {
				var content = '';

				doRequest(currentIndex+'&token='+token+'&configure=inixframe&ajax=1&action=upgrade&json=1',{module:$module},'POST','json',function(data){
					if (data.status == 'error') {
						$.each(data.error, function (k, e) {
							showFrameErrorMessage(e);
						});
					}

					if (data.status == "ok") {
						$.each(data.confirmations, function (k, c) {
							showFrameSuccessMessage(c);
						});

						$elm.fadeTo(500,0,function(){
							$elm.removeClass('panel-warning').addClass('panel-success').find('.panel-heading').html($up_to_date_msg);
							$elm.find('.btn-warning').hide();
							$elm.find('.btn-primary').hide();
							$elm.find('.panel-body > h4 .label-warning').hide().next().hide();
						});

					}

				}).always(function(){
					$overlay.addClass('hide');
					$elm.fadeTo(500,1);
				});
				$.each(data.confirmations, function (k, c) {

					showFrameSuccessMessage(c);
				});



			}

			if(data.warnings && data.warnings.length){
				$.each(data.warnings, function (k, c) {
					showFrameWarningMessage(c);
				});
			}
		}).always(function(){
			$overlay.addClass('hide');
			$elm.fadeTo(500,1);
		});
	});
}

/**
 * Do ajax request
 * @param url
 * @param data
 * @param method
 * @param data_type
 * @param callback
 * @returns jqxhr request { }
 */
function doRequest(url,data,method,data_type,callback) {
	if(!callback)
		callback = function (data, jqXHR, textStatus) {
			if (data.status == 'error')
				$.each(data.error, function (k, e) {
					showFrameErrorMessage(e);
				});
			if (data.status == "ok") {
				$.each(data.confirmations, function (k, c) {
					showFrameSuccessMessage(c);
				});
			}

			if(data.warnings && data.warnings.length){
				$.each(data.warnings, function (k, c) {
					showFrameWarningMessage(c);
				});
			}
		};
	if(!data)
		data= {};
	if(!method)
		method = 'get';
	if(!data_type)
		data_type = 'json';
	return $.ajax( {
		url: url,
		type: method.toUpperCase(),
		data: data,
		dataType: data_type
	}).done(callback);
}