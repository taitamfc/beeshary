{*
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from SAS Comptoir du Code
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SAS Comptoir du Code is strictly forbidden.
 * In order to obtain a license, please contact us: contact@comptoirducode.com
 *
 * @package   cdc_googletagmanager
 * @author    Vincent - Comptoir du Code
 * @copyright Copyright(c) 2015-2016 SAS Comptoir du Code
 * @license   Commercial license
 *}
{if count($dataLayer)}
<script>
var cdcGtmOpcLastStep = 1;
var cdcGtmOpc = {
	pushCheckoutStep : function(step, option) {
		console.log("[call] step: " + step + " : " + option);
		if(step > cdcGtmOpcLastStep) {
			var dataLayerOpc = {$dataLayer nofilter};
			dataLayerOpc.event = "checkout_opc";
			dataLayerOpc.ecommerce.checkout.actionField.step = step;
			dataLayerOpc.ecommerce.checkout.actionField.option = option;
			dataLayer.push(dataLayerOpc);
			console.log("[sent] step: " + step + " : " + option);
			cdcGtmOpcLastStep = step;
		}
	}
}

// address
function gtmOpcAddress() {
	cdcGtmOpc.pushCheckoutStep(2, "address");
}
// delivery option chosen
function gtmOpcDelivery() {
	var delay = 0;
	if(cdcGtmOpcLastStep < 2) {
		gtmOpcAddress();
		delay = 100;
	}
	setTimeout(function() {
		cdcGtmOpc.pushCheckoutStep(3, "delivery");
	}, delay);
}
// payment chosen
function gtmOpcPayment() {
	var delay = 0;
	if(cdcGtmOpcLastStep < 3) {
		gtmOpcDelivery();
		delay = 100;
	}
	setTimeout(function() {
		cdcGtmOpc.pushCheckoutStep(4, "payment");
	}, delay);
}


// supercheckout - Knowband - One Page Checkout, Social Login & Mailchimp v3.0.7
if($("#velsof_supercheckout_form").length) {
	console.log("[CDCGTM] supercheckout - Knowband");
	$("#checkoutShippingAddress").on("click", function(e) {
		if(!e.isTrigger) {
			gtmOpcAddress();
		}
	});
	$("#velsof_supercheckout_form").on("click", "#shipping-method", function(e) {
		if(!e.isTrigger) {
			gtmOpcDelivery();
		}
	});
	$("#velsof_supercheckout_form").on("click", "#payment-method", function(e) {
		if(!e.isTrigger) {
			gtmOpcPayment();
		}
	});
}

// onepagecheckoutps - PresTeamShop - One Page Checkout PrestaShop v1.3.8 -> v2.2.2
else if($("#onepagecheckoutps").length) {
	console.log("[CDCGTM] onepagecheckoutps - PresTeamShop");
	$("#onepagecheckoutps").on("click", "#onepagecheckoutps_step_one", function(e) {
		if(!e.isTrigger) {
			gtmOpcAddress();
		}
	});
	$("#onepagecheckoutps").on("click", "#onepagecheckoutps_step_two", function(e) {
		if(!e.isTrigger) {
			gtmOpcDelivery();
		}
	});
	$("#onepagecheckoutps").on("click", "#onepagecheckoutps_step_three", function(e) {
		if(!e.isTrigger) {
			gtmOpcPayment();
		}
	});
}

// bestkit_opc - best-kit - One Step Checkout / One Page Checkout v1.6.7
else if($("#3column_opc").length || $("#bigcart_opc").length) {
	var $opc_wrapper = null;
	// 2 styles: 3 columns or bigcart
	if($("#3column_opc").length) {
		$opc_wrapper = $("#3column_opc");
	} else {
		$opc_wrapper = $("#bigcart_opc");
	}
	console.log("[CDCGTM] bestkit_opc - best-kit - style: " + $opc_wrapper.prop('id'));
	$opc_wrapper.on("click", "#opc_account", function(e) {
		if(!e.isTrigger) {
			gtmOpcAddress();
		}
	});
	$opc_wrapper.on("click", "#opc_delivery_methods", function(e) {
		if(!e.isTrigger) {
			gtmOpcDelivery();
		}
	});
	$opc_wrapper.on("click", "#opc_payments", function(e) {
		if(!e.isTrigger) {
			gtmOpcPayment();
		}
	});
}

// klarnaofficial - Prestaworks AB - Klarna v1.8.42
else if($(".kco-main").length) {
	console.log("[CDCGTM] klarnaofficial - Prestaworks AB");
	$(".kco-main").on("click", "#klarnacarrier", function(e) {
		if(!e.isTrigger) {
			gtmOpcDelivery();
		}
	});
	$(".kco-main").on("click", "#klarna-checkout-container", function(e) {
		if(!e.isTrigger) {
			gtmOpcPayment();
		}
	});
}

// default Prestashop 1.5 / 1.6 OPC
else {
	$("#order-opc").on("click", "#opc_account", function(e) {
		if(!e.isTrigger) {
			gtmOpcAddress();
		}
	});
	$("#order-opc").on("click", "#carrier_area", function(e) {
		if(!e.isTrigger) {
			gtmOpcDelivery();
		}
	});
	$("#order-opc").on("click", "#opc_payment_methods", function(e) {
		if(!e.isTrigger) {
			gtmOpcPayment();
		}
	});
}

</script>
{/if}