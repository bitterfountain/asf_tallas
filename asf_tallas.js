
var selected_id_product 			= '';
var selected_id_product_attribute 	= '';

$(document).ready(function() {


	//$('.combinations .combinacion:first-child').addClass('seleccionado');
	
	//$('.combinations .sin_stock').removeClass('seleccionado');

	//add a link on the span 'view full size' and on the big image

	$(document).on('click', '.compradirecta', function(e) 
	{
		selected_id_product = $(this).attr('data-idproduct');
		

		var url_ajax 		= $(this).attr('href') + '&ajax=true';
		var msg1 			= Array();
		var msg2 			= Array();

		e.preventDefault();

		msg1[1]  = 'Agregando a la cesta';
		msg1[4]  = 'Adding to cart';

		msg2[1]  = 'En su cesta!';
		msg2[4]  = 'In your cart!';


		/*

		$('#info'+selected_id_product).html('<img src="'+prestashop.urls.img_ps_url+'ajax_loading_peque.gif" alt="" />  ' + msg1[prestashop.language.id] );

		if ( $(this).attr('rel') !== 'ready'  ) {
			$('#msg'+selected_id_product).removeClass('oculto');
			$('#info'+selected_id_product).html('');
		} else {
			$.ajax({
				type: 'post',
				url: url_ajax,
				async: true,
				cache: false,
				data: url_ajax,
				dataType : "json",
				success: function(jsonData,textStatus,jqXHR)
				{
					prestashop.emit('updateCart', {reason: jsonData});

					$('#info'+selected_id_product).html( msg2[prestashop.language.id] );
				},
				error: function(XMLHttpRequest, textStatus, errorThrown)
				{
					$('#info'+selected_id_product).html(JSON.stringify(XMLHttpRequest, null, 4) );
					//prestashop.emit('updateCart', {reason: resp});
					prestashop.emit('updateCart');

				}
			});

		}
		*/

	
		$('#info'+selected_id_product).html('<img src="'+prestashop.urls.img_ps_url+'ajax_loading_peque.gif" alt="" />  ' + msg1[prestashop.language.id] );

		if ( $(this).attr('rel') !== 'ready'  ) {
			$('#msg'+selected_id_product).removeClass('oculto');
			$('#info'+selected_id_product).html('');
		} else {

			$.post(url_ajax, url_ajax, null, 'json').then(function (resp) {
				if (resp.statusText=="OK") {
					prestashop.emit('updateCart', {reason: resp});				
					$('#info'+selected_id_product).html( msg2[prestashop.language.id] );
				}
				//console.log(resp);
			}).fail(function (resp) {
				if (resp.statusText=="OK") {
					prestashop.emit('updateCart', {reason: resp});
					$('#info'+selected_id_product).html( msg2[prestashop.language.id] );
				}

		  	});
				

		}
		

	});








/*

function changeQuantity(minimal_quantity, operator, id, id_attribute, quantincart){
	var token = prestashop.static_token; //important for logged user
	if (operator == 1){ //subtract
		var actionURL = '/index.php';
		var query= 'controller=cart&add=1&action=update&ajax=true&qty=1&op=down&token='+token+'&id_product='+ id +'&ipa='+ id_attribute;
		
		$.post(actionURL, query, null, 'json').then(function (resp) {
			prestashop.emit('updateCart', {reason: resp});		
			console.log(resp);		
		}).fail(function (resp) {
			prestashop.emit('handleError', { eventType: 'addProductToCart', resp: resp });
	   	});

	}else if (operator == 2){ //sum

		var actionURL = '/index.php';
		var query= 'controller=cart&add=1&action=update&ajax=true&qty=1&op=up&token='+token+'&id_product='+ id +'&ipa='+ id_attribute;
		$.post(actionURL, query, null, 'json').then(function (resp) {
			prestashop.emit('updateCart', {reason: resp});
			console.log(resp);
		}).fail(function (resp) {
			prestashop.emit('handleError', { eventType: 'addProductToCart', resp: resp });
	  	});

	}else if (operator == 3){ //delete
		var actionURL = '/index.php';
		var query= 'controller=cart&add=1&action=update&ajax=true&qty='+quantincart+'&op=down&token='+token+'&id_product='+ id +'&ipa='+ id_attribute;
		$.post(actionURL, query, null, 'json').then(function (resp) {
			prestashop.emit('updateCart', {	reason: resp});
			console.log(resp);
		}).fail(function (resp) {
			prestashop.emit('handleError', { eventType: 'addProductToCart', resp: resp });
	  	});
	}
}



*/


















	$(document).on('click', '.tallasn', function(e)
	{
		var tok = '';

		if (prestashop.customer.is_logged) {
			tok = '&token=' + prestashop.static_token;
		} else {
			tok = '&token=' + prestashop.token;			
		}


		if ( $(this).parent().hasClass('sin_stock') == false ) {
			selected_id_product 			= $(this).attr('data-idproduct');
			selected_id_product_attribute 	= $(this).attr('data-idproductattribute');

			//var precio						= $(this).children('a').attr('data-total') + ' €';
			/* var url_foto					= $(this).children('a').attr('data-imgpath'); */
			
			$(this).parent().parent().children().removeClass('seleccionado');

			if ( $(this).parent().hasClass('seleccionado') == false  )
			{
				$('#msg'+selected_id_product).addClass('oculto');

			 	$(this).parent().addClass('seleccionado');

			 	$('#compradirecta'+selected_id_product).attr('href', prestashop.urls.base_url + '?controller=cart&add=1&op=up&id_product='+selected_id_product+'&id_product_attribute='+selected_id_product_attribute+tok );
			 	$('#compradirecta'+selected_id_product).attr('rel', 'ready');
			 	$('#comprar'+selected_id_product).removeClass('hidden');
			 	
			 	/* $('#img'+selected_id_product).attr('src', url_foto); */

			 	//$('#precio'+selected_id_product).html(precio);
			}
		}

	});


});
