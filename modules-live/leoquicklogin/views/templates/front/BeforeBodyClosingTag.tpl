{if !$can_add_to_cart}
<script>
	$('#booking_button, .btn.btn-primary.add-to-cart').on('click',function(){
		
		var clone_obj = '';
		var $status = 'error';
		var $action = 'add';
		clone_obj = $('.leo-temp-'+$status+'>div').clone();
		clone_obj.find('.noti-'+$action).addClass('active');
		$('.leo-notification').append(clone_obj);
		
		var msg = 'MISE AU PANIER  IMPOSSIBLE';
		var msg1 = 'Notre système ne vous permet malheureusement pas d\'ajouter un produit et une activitédans votre panier. ';
		var msg2 = 'Merci de bien vouloir créer un second panier ultérieurement. Merci de votre compréhension.';
		
		/*
		setTimeout(
		  function() 
		  {
			$('.leo-notification .noti.noti-add').text(msg);
			clone_obj.find('.notification').addClass('show');
			
			$('.leo-notification').addClass('active');
		  }, 100);
		  
		setTimeout(
		  function() 
		  {
			clone_obj.find('.notification').removeClass('show').addClass("closed").parent().addClass('disable');
			$('.leo-notification').removeClass('active');
		  }, 5000);
		*/
		$('#modal-prevent-cart').remove();
		var modal_html = '';
		
		modal_html += '<div id="modal-prevent-cart" tabindex="-1" role="dialog" aria-hidden="true" class="modal leo-modal leo-modal-cart fade error-modal">';
			modal_html += '<div class="modal-dialog" role="document">';
				modal_html += '<div class="modal-content col-md-12 no-padding">';
				modal_html += '<a class="btn-close1" data-dismiss="modal">X</a>';
					modal_html += '<div class="modal-body col-md-6 no-padding">';
					modal_html += '<img src="/img/pop-up-bkg.jpg" class="img-responsive img-pop" />';
					modal_html += '</div>';
					modal_html += '<div class="modal-footer col-md-6">';
					modal_html += '<p class="p51">'+msg+'</p>';
					modal_html += '<p class="p16">'+msg1+'</p><br>';
					modal_html += '<p class="p16">'+msg2+'</p><br>';
						modal_html += '<button type="button" class="btn btn-secondary btn-error-modal" data-dismiss="modal">J\'ai compris</button><br>';
					modal_html += '</div>';
				modal_html += '</div>';
			modal_html += '</div>';
		modal_html += '</div>';
		$('.leo-notification').after(modal_html);
		$('#modal-prevent-cart').modal('show');
		
		//alert('Vous ne pouvez pas réserver une activité et acheter un produit physique, merci de faire vos commandes séparément !');
		return false;
	});
</script>
{/if}