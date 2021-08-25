/**
 *  @Website: apollotheme.com - prestashop template provider
 *  @author Apollotheme <apollotheme@gmail.com>
 *  @copyright  2007-2018 Apollotheme
 *  @description: 
 */

/**
 * Start block functions common for front-end
 */
(function ($) {
    $.LeoCustomAjax = function () {
        this.leoData = 'leoajax=1';
    };
    $.LeoCustomAjax.prototype = {
        processAjax: function () {

            var myElement = this;
            
            if (leoOption.category_qty && $(".leo-qty").length)
                myElement.getCategoryList();
            else if ($(".leo-qty").length)
                $(".leo-qty").remove();
            
            if (leoOption.product_list_image && $(".leo-more-info").length)
                myElement.getProductListImage();
            else if ($(".leo-more-info").length)
                $(".leo-more-info").remove();
            
            if (leoOption.product_one_img && $(".product-additional").length)
                myElement.getProductOneImage();
            else if ($(".product-additional").length)
                $(".product-additional").remove();
            
            if (leoOption.product_one_img && $(".product-attribute-additional").length)
                myElement.getProductAttributeOneImage();
            else if ($(".product-attribute-additional").length)
                $(".product-attribute-additional").remove();
            
            if (leoOption.product_one_img && $(".product-all-additional").length)
                myElement.getProductAllOneImage();
            else if ($(".product-all-additional").length)
                $(".product-all-additional").remove();
            
            if (leoOption.productCdown && $(".leo-more-cdown").length)
                myElement.getProductCdownInfo();
            else if ($(".leo-more-cdown").length)
                $(".leo-more-cdown").remove();
            
            if (leoOption.productCdown && $(".leo-more-color").length)
                myElement.getProductColorInfo();
            else if ($(".leo-more-color").length)
                $(".leo-more-color").remove();
            
            //find class ap-count-wishlist-compare
            if($('.ap-total-wishlist').length || $('.ap-total-compare').length)
            {
                myElement.getCountWishlistCompare();
            }
            
            if (myElement.leoData != "leoajax=1") {
                $.ajax({
                    type: 'POST',
                    headers: {"cache-control": "no-cache"},
                    url: prestashop.urls.base_url + 'modules/appagebuilder/apajax.php' + '?rand=' + new Date().getTime(),
                    async: true,
                    cache: false,
                    dataType: "json",
                    data: myElement.leoData,
                    success: function (jsonData) {
                        if (jsonData) {
                            if (jsonData.cat) {
                                for (i = 0; i < jsonData.cat.length; i++) {
                                    var str = jsonData.cat[i].total;
                                    var label = $(".leo-qty.leo-cat-" + jsonData.cat[i].id_category).data("str");
                                    if(typeof label != "undefined") {
                                        str += "<span>" + label + "</span>";
                                    }
                                    $(".leo-qty.leo-cat-" + jsonData.cat[i].id_category).html(str);
                                    $(".leo-qty.leo-cat-" + jsonData.cat[i].id_category).show();
                                }
								
                                $('.leo-qty').each(function(){
                                        if($(this).html() == '')
                                        {
                                                $(this).html('0');
                                                $(this).show();
                                        }
                                })
                            }
                            if (jsonData.product_list_image) {
                                var listProduct = new Array();
                                for (i = 0; i < jsonData.product_list_image.length; i++) {
                                    listProduct[jsonData.product_list_image[i].id] = jsonData.product_list_image[i].content;
                                }

                                $(".leo-more-info").each(function () {
                                    $(this).html(listProduct[$(this).data("idproduct")]);
                                });
                                addEffectProducts();
                            }

                            if (jsonData.pro_cdown) {
                                var listProduct = new Array();
                                for (i = 0; i < jsonData.pro_cdown.length; i++) {
                                    listProduct[jsonData.pro_cdown[i].id] = jsonData.pro_cdown[i].content;
                                }

                                $(".leo-more-cdown").each(function () {
                                    $(this).html(listProduct[$(this).data("idproduct")]);
                                });
                            }

                            if (jsonData.pro_color) {
                                var listProduct = new Array();
                                for (i = 0; i < jsonData.pro_color.length; i++) {
                                    listProduct[jsonData.pro_color[i].id] = jsonData.pro_color[i].content;
                                }

                                $(".leo-more-color").each(function () {
                                    $(this).html(listProduct[$(this).data("idproduct")]);
                                });
                            }
                                                        
                            if (jsonData.product_one_img) {
                                var listProductImg = new Array();
								var listProductName = new Array();
                                for (i = 0; i < jsonData.product_one_img.length; i++) {
                                    listProductImg[jsonData.product_one_img[i].id] = jsonData.product_one_img[i].content;
									listProductName[jsonData.product_one_img[i].id] = jsonData.product_one_img[i].name;
                                }
								
								iw = 360;
								ih = 360;
								if (typeof leoOption.homeWidth !== 'undefined') {
										iw = leoOption.homeWidth;
										ih = leoOption.homeheight;
								}else{
										iw = $('.product_img_link .img-fluid').first().attr('width');
										ih = $('.product_img_link .img-fluid').first().attr('height');
								}
                                $(".product-additional").each(function () {
                                    if (listProductImg[$(this).data("idproduct")]) {
                                        var str_image = listProductImg[$(this).data("idproduct")];
                                        if ($(this).data("image-type")) {
                                            src_image = str_image.replace('home_default',$(this).data("image-type"));
                                        }else{
                                            src_image = str_image.replace('home_default', 'home_default');
                                        }
										var name_image = listProductName[$(this).data("idproduct")];
                                        $(this).html('<img class="img-fluid" title="'+name_image+'" alt="'+name_image+'" src="' + src_image + '" width="'+iw+'" height="'+ih+'"/>');
                                    }
                                });
                                //addEffOneImg();
                            }
                                                        
                            if (jsonData.product_attribute_one_img) {
                                var listProductImg = new Array();
                                var listProductName = new Array();
                                for (i = 0; i < jsonData.product_attribute_one_img.length; i++) {
                                    listProductImg[jsonData.product_attribute_one_img[i].id] = jsonData.product_attribute_one_img[i].content;
                                    listProductName[jsonData.product_attribute_one_img[i].id] = jsonData.product_attribute_one_img[i].name;
                                }

                                iw = 360;
                                ih = 360;
                                if (typeof leoOption.homeWidth !== 'undefined') {
                                    iw = leoOption.homeWidth;
                                    ih = leoOption.homeheight;
                                }else{
                                    iw = $('.product_img_link .img-fluid').first().attr('width');
                                    ih = $('.product_img_link .img-fluid').first().attr('height');
                                }
                                $(".product-attribute-additional").each(function () {
                                    if (listProductImg[$(this).closest('.js-product-miniature').data("id-product")]) {
                                        var str_image = listProductImg[$(this).closest('.js-product-miniature').data("id-product")];
                                        if ($(this).data("image-type")) {
                                            src_image = str_image.replace('home_default',$(this).data("image-type"));
                                        }else{
                                            src_image = str_image.replace('home_default', 'home_default');
                                        }
                                            var name_image = listProductName[$(this).closest('.js-product-miniature').data("id-product")];
                                        $(this).html('<img class="img-fluid" title="'+name_image+'" alt="'+name_image+'" src="' + src_image + '" width="'+iw+'" height="'+ih+'"/>');
                                    }
                                });
                                //addEffOneImg();
                            }
                            
                            if (jsonData.product_all_one_img) {
                                var listProductImg = new Array();
                                var listProductName = new Array();
                                for (i = 0; i < jsonData.product_all_one_img.length; i++) {
                                    listProductImg[jsonData.product_all_one_img[i].id] = jsonData.product_all_one_img[i].content;
                                    listProductName[jsonData.product_all_one_img[i].id] = jsonData.product_all_one_img[i].name;
                                }

                                iw = 360;
                                ih = 360;
                                if (typeof leoOption.homeWidth !== 'undefined') {
                                    iw = leoOption.homeWidth;
                                    ih = leoOption.homeheight;
                                }else{
                                    iw = $('.product_img_link .img-fluid').first().attr('width');
                                    ih = $('.product_img_link .img-fluid').first().attr('height');
                                }
                                $(".product-all-additional").each(function () {
                                    if (listProductImg[$(this).closest('.js-product-miniature').data("id-product")]) {
                                        var str_image = listProductImg[$(this).closest('.js-product-miniature').data("id-product")];
                                        if ($(this).data("image-type")) {
                                            src_image = str_image.replace('home_default',$(this).data("image-type"));
                                        }else{
                                            src_image = str_image.replace('home_default', 'home_default');
                                        }
                                            var name_image = listProductName[$(this).closest('.js-product-miniature').data("id-product")];
                                        $(this).html('<img class="img-fluid" title="'+name_image+'" alt="'+name_image+'" src="' + src_image + '" width="'+iw+'" height="'+ih+'"/>');
                                    }
                                });
                                //addEffOneImg();
                            }
                            
                            //wishlist 
                            if (jsonData.wishlist_products)
                            {
								$('.ap-total-wishlist').data('wishlist-total',jsonData.wishlist_products);
                                $('.ap-total-wishlist').text(jsonData.wishlist_products);
                            }
                            else
                            {
								$('.ap-total-wishlist').data('wishlist-total',0);
                                $('.ap-total-wishlist').text('0');
                            }
                            
                            //compare
                            if (jsonData.compared_products)
                            {
								$('.ap-total-compare').data('compare-total',jsonData.compared_products);
                                $('.ap-total-compare').text(jsonData.compared_products);
                            }
                            else
                            {
								$('.ap-total-compare').data('compare-total',0);
                                $('.ap-total-compare').text(0);
                            }

                        }
                    },
                    error: function () {
                    }
                });
            }
        },
        
        //check get number product of wishlist compare
        getCountWishlistCompare: function()
        {
            this.leoData += '&wishlist_compare=1';
        },
        
        getCategoryList: function () {
            //get category id
            var leoCatList = "";
            $(".leo-qty").each(function () {
                if($(this).data("id")){
                    if (leoCatList)
                        leoCatList += "," + $(this).data("id");
                    else
                        leoCatList = $(this).data("id");
                }else{
                    if (leoCatList)
                        leoCatList += "," + $(this).attr("id");
                    else
                        leoCatList = $(this).attr("id");
                }
            });

            if (leoCatList) {
                leoCatList = leoCatList.replace(/leo-cat-/g, "");
                this.leoData += '&cat_list=' + leoCatList;
            }
            return false;
        },
        getProductListImage: function () {
            var leoProInfo = "";
            $(".leo-more-info").each(function () {
                if (!leoProInfo)
                    leoProInfo += $(this).data("idproduct");
                else
                    leoProInfo += "," + $(this).data("idproduct");
            });
            if (leoProInfo) {
                this.leoData += '&product_list_image=' + leoProInfo;
            }
            return false;
        },
        getProductCdownInfo: function () {
            var leoProCdown = "";
            $(".leo-more-cdown").each(function () {
                if (!leoProCdown)
                    leoProCdown += $(this).data("idproduct");
                else
                    leoProCdown += "," + $(this).data("idproduct");
            });
            if (leoProCdown) {
                this.leoData += '&pro_cdown=' + leoProCdown;
            }
            return false;
        },
        getProductColorInfo: function () {
            var leoProColor = "";
            $(".leo-more-color").each(function () {
                if (!leoProColor)
                    leoProColor += $(this).data("idproduct");
                else
                    leoProColor += "," + $(this).data("idproduct");
            });
            if (leoProColor) {
                this.leoData += '&pro_color=' + leoProColor;
            }
            return false;
        },
        getProductOneImage: function () {
            //tranditional image
            var leoAdditional = "";
            $(".product-additional").each(function () {
                if (!leoAdditional)
                    leoAdditional += $(this).data("idproduct");
                else
                    leoAdditional += "," + $(this).data("idproduct");
            });
            if (leoAdditional) {
                this.leoData += '&product_one_img=' + leoAdditional;
            }
            return false;
        },
        getProductAttributeOneImage: function () {
            //tranditional image
            var leoAdditionalattribute = "0-0";
            $(".product-attribute-additional").each(function () {
                leoAdditionalattribute += "," + $(this).closest('.js-product-miniature').data("id-product") + '-' + $(this).closest('.js-product-miniature').data("id-product-attribute");
            });
            if (leoAdditionalattribute && leoAdditionalattribute != '0-0') {
                this.leoData += '&product_attribute_one_img=' + leoAdditionalattribute;
            }
            return false;
        },
        getProductAllOneImage: function () {
            //tranditional image
            var leoAdditional = "0";
            var image_product = "0";
            $(".product-all-additional").each(function () {
                leoAdditional += "," + $(this).closest('.js-product-miniature').data("id-product");
                image_product += "," + $(this).data("id-image");
            });
            if (leoAdditional) {
                this.leoData += '&product_all_one_img=' + leoAdditional + '&image_product=' + image_product;;
            }
            return false;
        },
    };
}(jQuery));

function addJSProduct(currentProduct) {
// http://demos.flesler.com/jquery/serialScroll/
//    if (typeof serialScroll == 'function') { 
        $('.thumbs_list_' + currentProduct).serialScroll({
            items: 'li:visible',
            prev: '.view_scroll_left_' + currentProduct,
            next: '.view_scroll_right_' + currentProduct,
            axis: 'y',
            offset: 0,
            start: 0,
            stop: true,
            duration: 700,
            step: 1,
            lazy: true,
            lock: false,
            force: false,
            cycle: false,
			onBefore: function( e, elem, $pane, $items, pos ){
				//DONGND:: update status for button
				if( pos == 0 )
				{
					$('.view_scroll_left_' + currentProduct).addClass('disable');					
				}
                else if( pos == $items.length -1 )
				{
					$('.view_scroll_right_' + currentProduct).addClass('disable');
				}
				else
				{
					$('.view_scroll_left_' + currentProduct).removeClass('disable');
					$('.view_scroll_right_' + currentProduct).removeClass('disable');
				}
			},
        });
        $('.thumbs_list_' + currentProduct).trigger('goto', 1);// SerialScroll Bug on goto 0 ?
        $('.thumbs_list_' + currentProduct).trigger('goto', 0);
 //   }   
}
function addEffectProducts(){
    
    if(typeof(leoOption) != 'undefined' && leoOption.product_list_image){
        $(".leo-more-info").each(function() {
            addJSProduct($(this).data("idproduct"));
        });
        addEffectProduct();
    }
}

function addEffectProduct() {
    var speed = 800;
    var effect = "easeInOutQuad";

    //$(".products_block .carousel-inner .ajax_block_product:first-child").mouseenter(function() {
        //$(".products_block .carousel-inner").css("overflow", "inherit");
    //});
    //$(".carousel-inner").mouseleave(function() {
        //$(".carousel-inner").css("overflow", "hidden");
    //});

    $(".leo-more-info").each(function() {
        var leo_preview = this;
        $(leo_preview).find(".leo-hover-image").each(function() {
            $(this).mouseover(function() {
                var big_image = $(this).attr("rel");
                var imgElement = $(leo_preview).parent().find(".product-thumbnail img").first();
                if (!imgElement.length) {
                    imgElement = $(leo_preview).parent().find(".product_image img").first();
                }

                if (imgElement.length) {
                    $(imgElement).stop().animate({opacity: 0}, {duration: speed, easing: effect});
                    $(imgElement).first().attr("src", big_image);
                    $(imgElement).first().attr("data-rel", big_image);
                    $(imgElement).stop().animate({opacity: 1}, {duration: speed, easing: effect});
                }
				//DONGND:: change class when hover another image
				if (!$(this).hasClass('shown'))
				{
					$(leo_preview).find('.shown').removeClass('shown');
					$(this).parent().addClass('shown');
				}
            });
        });

        $('.thickbox-ajax-'+$(this).data("idproduct")).fancybox({
            helpers: {
                overlay: {
                    locked: false
                }
            },
            'hideOnContentClick': true,
            'transitionIn'  : 'elastic',
            'transitionOut' : 'elastic'
        });
    });
}

function addEffOneImg() {
    var speed = 800;
    var effect = "easeInOutQuad";

    $(".product-additional").each(function() {
        if ($(this).find("img").length) {
            var leo_hover_image = $(this).parent().find("img").first();
            var leo_preview = $(this);
            $(this).parent().mouseenter(function() {
                $(this).find("img").first().stop().animate({opacity: 0}, {duration: speed, easing: effect});
                $(leo_preview).stop().animate({opacity: 1}, {duration: speed, easing: effect});
            });
            $(this).parent().mouseleave(function() {
                $(this).find("img").first().stop().animate({opacity: 1}, {duration: speed, easing: effect});
                $(leo_preview).stop().animate({opacity: 0}, {duration: speed, easing: effect});
            });
        }
    });
}
function log(message) {
    console.log(message);
}

//DONGND:: action animation
function activeAnimation()
{
	$(".has-animation").each(function() {
		onScrollInit($(this));
	});
}

function onScrollInit(items) {
    items.each(function() {
        var osElement = $(this);
        var animation = $(osElement).data("animation");
        var osAnimationDelay = $(osElement).data("animation-delay");
		var osAnimationDuration = $(osElement).data("animation-duration");
		var osAnimationIterationCount = $(osElement).data("animation-iteration-count");
		var osAnimationInfinite = $(osElement).data("animation-infinite");
		if (osAnimationInfinite == 1)
		{
			var loop_animation = 'infinite';
		}
		else
		{
			var loop_animation = osAnimationIterationCount;
		}
        osElement.css({
            "-webkit-animation-delay": osAnimationDelay,
            "-moz-animation-delay": osAnimationDelay,
            "animation-delay": osAnimationDelay,
			"-webkit-animation-duration": osAnimationDuration,
            "-moz-animation-duration": osAnimationDuration,
            "animation-duration": osAnimationDuration,
			"-webkit-animation-iteration-count": loop_animation,
            "-moz-animation-iteration-count": loop_animation,
            "animation-iteration-count": loop_animation,
        });
        
        osElement.waypoint(function() {		
			if (osElement.hasClass('has-animation'))
			{					
				osElement.addClass('animated '+ animation).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){					
					$(this).removeClass('has-animation animated ' +animation);					
				});			
			}            
	
			this.destroy();
        }, {
            triggerOnce: true,
            offset: '100%'
        });
    });
}
/**
 * End block functions common for front-end
 */


/**
 * End block for module ap_gmap
 */
function synSize(name) {
    var obj = $("#" + name);
    var div = $(obj).closest(".gmap-cover");
    var gmap = $(div).find(".gmap");
    $(obj).height($(gmap).height());
    //console.log($(gmap).height());
}

$(function() {
    /**
     * Start block for module ap_product_list
     */
    $(".btn-show-more").click(function() {
        var page = parseInt($(this).data('page'));
		var use_animation = parseInt($(this).data('use-animation'));
        var btn = $(this);
        var config = $(this).closest(".ApProductList").find(".apconfig").val();

        // FIX 1.7
        btn.data('reset-text', btn.html() );
        btn.html(  btn.data('loading-text') );
		
        $.ajax({
            headers: {"cache-control": "no-cache"},
            url: prestashop.urls.base_url + 'modules/appagebuilder/apajax.php',
            async: true,
            cache: false,
            dataType: "Json",
            data: {"config": config, "p": page, "use_animation": use_animation},
            success: function(response) {
                var boxCover = $(btn).closest(".box-show-more");
                if(!response.is_more) {
                    $(boxCover).removeClass("open").fadeOut();
                }
                if(response.html) {
                    $(boxCover).prev().append(response.html);
                }
                $(btn).data("page", (page + 1));
				
				if (typeof $.LeoCustomAjax !== "undefined" && $.isFunction($.LeoCustomAjax)) {
					var leoCustomAjax = new $.LeoCustomAjax();
					leoCustomAjax.processAjax();
				}
				
				//DONGND:: class function of leofeature
				callLeoFeature();
				
				//DONGND:: re call run animation
				activeAnimation();
				
            }
        }).always(function () {
            // FIX 1.7
            btn.html(  btn.data('reset-text') );
        });
    });
    /**
     * End block for module ap_product_list
     */
    /**
     * Start block for module ap_image
     */
	 //DONGND:: call active anmation
    activeAnimation();
    /**
     * End block for module ap_image
     */
});
(function ($) {

    window.addRule = function (selector, styles, sheet) {

        styles = (function (styles) {
            if (typeof styles === "string") return styles;
            var clone = "";
            for (var prop in styles) {
                if (styles.hasOwnProperty(prop)) {
                    var val = styles[prop];
                    prop = prop.replace(/([A-Z])/g, "-$1").toLowerCase(); // convert to dash-case
                    clone += prop + ":" + (prop === "content" ? '"' + val + '"' : val) + "; ";
                }
            }
            return clone;
        }(styles));
        


        try {
            sheet = sheet || document.styleSheets[document.styleSheets.length - 1];
            if (sheet.insertRule)
            {
                // if(!(navigator.userAgent.toLowerCase().indexOf('firefox') > -1))		// FIX work on firefox
                            // {
                    if(sheet.cssRules!==null && sheet.cssRules.length!== 0)
                        sheet.insertRule(selector + " {" + styles + "}", sheet.cssRules.length);
                // }
            }
            else if (sheet.addRule) sheet.addRule(selector, styles);
        }
        catch(err) {
            // way 2 : https://stackoverflow.com/questions/28930846/how-to-use-cssstylesheet-insertrule-properly
            var style = (function() {
                var style = document.createElement("style");
                style.appendChild(document.createTextNode(""));
                document.head.appendChild(style);
                return style;
            })();
            style.sheet.insertRule(selector + " {" + styles + "}", 0);
            
            
        }
//        window.console.log(sheet);
//        window.console.log(selector);
//        window.console.log(styles);
        return this;

    };

    if ($) $.fn.addRule = function (styles, sheet) {
        addRule(this.selector, styles, sheet);
        return this;
    };

}(this.jQuery || this.Zepto));
function apPopupForm(){
    if($.cookie('apnewletter')) return;
    $.fancybox({
            'content' : $(".ap-popup").html(),
            'wrapCSS' : 'ap-popup-clone',
            afterClose: function (event, ui) {
				
            },
			beforeShow: function (event, ui) {
                this.inner.append("<div class='turnoff-popup-wrapper text-center'><input id='turnoff-popup-bt' name='turnoff-popup-bt' class='turnoff-popup' type='checkbox'><label for='turnoff-popup-bt'>"+turnoff_popup_text+"</label></div>");			
				$('.turnoff-popup').click(function(){
					if (!$(this).hasClass('active'))
					{
						$.cookie('apnewletter', '1');
						$(this).addClass('active');
					}
					else
					{
						$.cookie('apnewletter', null);
						$(this).removeClass('active');
					}
				})
            }
    });
}

//set background and parallax
$(document).ready(function(){
    //show popup
    if($('.ap-popup').length){
        if($('.ap-popup').hasClass('index-only') && $('body').attr('id') == 'index')
            apPopupForm();
        else
            apPopupForm();
    }

    $(".has-bg.bg-fullwidth").each(function(){
        id = "#"+$(this).attr("id");
        bg = $(this).data("bg");
        $(id + ":before").addRule({
            background: bg
        });
    });
    //stela
    if (typeof stellar !== 'undefined' && stellar)
            $.stellar({horizontalScrolling:false});

    //mouse
    currentPosX = [];
    currentPosY = [];
    $("div[data-mouse-parallax-strength]").each(function(){
        currentPos = $(this).css("background-position");
        if (typeof currentPos == "string")
        {
            currentPosArray = currentPos.split(" ");
        }else
        {
            currentPosArray = [$(this).css("background-position-x"),$(this).css("background-position-y")];
        }
        currentPosX[$(this).data("mouse-parallax-rid")] = parseFloat(currentPosArray[0]);
        currentPosY[$(this).data("mouse-parallax-rid")] = parseFloat(currentPosArray[1]);
        $(this).mousemove(function(e){
            newPosX = currentPosX[$(this).data("mouse-parallax-rid")];
            newPosY = currentPosY[$(this).data("mouse-parallax-rid")];
            if($(this).data("mouse-parallax-axis") != "axis-y"){
                mparallaxPageX = e.pageX - $(this).offset().left;
                if($(this).hasClass("full-bg-screen"))
                {
                    mparallaxPageX = mparallaxPageX - 1000;
                }
                newPosX = (mparallaxPageX * $(this).data("mouse-parallax-strength") * -1) + newPosX;
            }
            if($(this).data("mouse-parallax-axis") !="axis-x"){
                mparallaxPageY = e.pageY - $(this).offset().top;
                newPosY = mparallaxPageY * $(this).data("mouse-parallax-strength") * -1;
            }
            $(this).css("background-position",newPosX+"px "+newPosY+"px");
        });
    });

    var ytIframeId; var ytVideoId;
    function onYouTubeIframeAPIReady() {
        $("div.iframe-youtube-api-tag").each(function(){
            ytIframeId = $(this).attr("id");
            ytVideoId = $(this).data("youtube-video-id");

            new YT.Player(ytIframeId, {
                videoId: ytVideoId,
                width: "100%",
                height: "100%",
                playerVars :{autoplay:1,controls:0,disablekb:1,fs:0,cc_load_policy:0,
                            iv_load_policy:3,modestbranding:0,rel:0,showinfo:0,start:0},
                events: {
                    "onReady": function(event){
                        event.target.mute();
                        setInterval(
                            function(){event.target.seekTo(0);},
                            (event.target.getDuration() - 1) * 1000
                        );
                    }
                }
            });
        });
    }
    onYouTubeIframeAPIReady();
    
    if (typeof MediaElementPlayer !== 'undefined') {
        //add function for html5 youtube video
        var player1 = new MediaElementPlayer('#special-youtube-video1');
        var player2 = new MediaElementPlayer('#special-youtube-video2');
        if(player1)
        {
            var auto_find = setInterval(function(){
                if($('#video-1 .mejs-overlay-play').html())
                {
                    $('#video-1 .mejs-overlay-play>.mejs-overlay-button').before('<div class="video-name">'+$('#special-youtube-video1').data('name')+'</div>');
                    $('#video-1 .mejs-overlay-play').append('<div class="video-description">Watch video and <span>subscribe us<span></div>');   
                    clearInterval(auto_find);
                }
            }, 500);
        }
        
        if(player2)
        {
            var auto_find1 = setInterval(function(){        
                if($('#video-2 .mejs-overlay-play').html())
                {
                    $('#video-2 .mejs-overlay-play>.mejs-overlay-button').before('<div class="video-name">'+$('#special-youtube-video2').data('name')+'</div>');
                    $('#video-2 .mejs-overlay-play').append('<div class="video-description">Watch video and <span>subscribe us<span></div>');   
                    clearInterval(auto_find1);              
                }
            }, 500);
        }
    }
    //js for select header, footer, content in demo
    current_url = window.location.href;
    $('.apconfig').each(function(){
        var enableJS = $(this).data('enablejs');
        if(enableJS == false){
            return;
        }
        var leochange = '&leopanelchange';
        var current_url = $(this).data('url');
        if( !current_url ){
            current_url = window.location.href;
            current_url = current_url.replace(leochange, "");
        }
        
        var param = $(this).data('type');
        var value = $(this).data('id');
        var re = new RegExp("([?|&])" + param + "=.*?(&|$)","i");
        if (current_url.match(re))
            $(this).attr('href', current_url.replace(re,'$1' + param + "=" + value + '$2') + leochange);
        else{
            if(current_url.indexOf('?') == -1)
                $(this).attr('href', current_url + '?' + param + "=" + value + leochange);
            else
                 $(this).attr('href', current_url + '&' + param + "=" + value + leochange);
                
        }
    });
	
	//DONGND:: fix owl carousel in tab load delay when resize.
	$(window).resize(function(){
		if($('.tab-pane .owl-carousel').length)
		{
			$('.tab-pane .owl-carousel').each(function(index, element){
				if(!$(element).parents('.tab-pane').hasClass('active') && typeof ($(element).data('owlCarousel')) !== "undefined")
				{
					var w_owl_active_tab = $(element).parents('.tab-pane').siblings('.active').find('.owl-carousel').width();
			
					$(element).width(w_owl_active_tab);
					$(element).data('owlCarousel').updateVars();
					$(element).width('100%');
				}
			});
		}
	})
	
	//DONGND:: loading owl carousel fake item
	var check_window_w = parseInt($(window).width());
	
	if (check_window_w >= 992 && check_window_w < 1200)
	{	   
	   addClassLoading('lg');
	}
	else if (check_window_w >= 768 && check_window_w < 992)
	{	  
	   addClassLoading('md');
	}
	else if (check_window_w >= 576 && check_window_w < 768)
	{	   
	   addClassLoading('sm');
	}
	else if (check_window_w < 576)
	{	   
	   addClassLoading('m');
	}
	else
	{		
		addClassLoading('xl');
	}
	

    // // Slick Carousel
    // $( ".slick-carousel-parent" ).each(function( index ) {
    //     var slick_config = $(this).data("slick-config");
    //     if(slick_config){

    //         $(this).on('init', function() {
    //             $(this).css("display", "block");
    //         });
            
    //         $(this).slick({
    //             dots: parseInt(slick_config.dot)?true:false,
    //             //infinite: parseInt(slick_config.dot)?false:true,
    //             infinite: parseInt(slick_config.infinite)?true:false,
    //             vertical: parseInt(slick_config.vertical)?true:false,
    //             autoplay: parseInt(slick_config.autoplay)?true:false,
    //             pauseonhover: parseInt(slick_config.autoplay)?true:false,
    //             autoplaySpeed: 2000,
    //             arrows: parseInt(slick_config.arrows)?true:false,
    //             row: parseInt(slick_config.row),
    //             slidesToShow: parseInt(slick_config.slidestoshow),
    //             slidesToScroll: parseInt(slick_config.slidestoscroll),
    //             responsive: [
    //                 {
    //                   breakpoint: 1024,
    //                   settings: {
    //                     row: parseInt(slick_config.row_1024),
    //                     slidesToShow: parseInt(slick_config.slidestoshow_1024),
    //                   }
    //                 },
    //                 {
    //                   breakpoint: 768,
    //                   settings: {
    //                     row: parseInt(slick_config.row_768),
    //                     slidesToShow: parseInt(slick_config.slidestoshow_768),
    //                   }
    //                 },
    //                 {
    //                   breakpoint: 600,
    //                   settings: {
    //                     row: parseInt(slick_config.row_600),
    //                     slidesToShow: parseInt(slick_config.slidestoshow_600),
    //                   }
    //                 },
    //                 {
    //                   breakpoint: 450,
    //                   settings: {
    //                     row: parseInt(slick_config.row_450),
    //                     slidesToShow: parseInt(slick_config.slidestoshow_450),
    //                   }
    //                 }
    //             ]
    //         });
            
    //         $(this).on('init', function(slick){
    //             console.log("123");
    //         });

    //     }
    // });

});

//DONGND:: add class loading by width of window
function addClassLoading($type)
{
	$('.timeline-wrapper').each(function(){		
		if ($(this).data($type) <= $(this).data('item'))
		{			
			var num_remove_item = $(this).data('item') - $(this).data($type);
			if ($(this).data($type) < $(this).data('item'))
			{
				var num_remove_item = $(this).data('item') - $(this).data($type);
				$(this).find('.timeline-parent').slice(-num_remove_item).remove();
			}
			
			if (12%$(this).data($type) == 0)
			{
				if ($type == 'm')
				{
					$(this).find('.timeline-parent').addClass('col-xs-'+12/$(this).data($type));
				}
				else
				{
					$(this).find('.timeline-parent').addClass('col-'+$type+'-'+12/$(this).data($type));
				}
			}
			else
			{				
				$(this).find('.timeline-parent').css({'width': 100/$(this).data($type)+'%', 'float': 'left'})
			}
				
			$('.timeline-wrapper').removeClass('prepare');
		}
	})
}

//DONGND:: call function from leofeature
function callLeoFeature()
{
	if(typeof (leoBtCart) != 'undefined')
	{		
		leoBtCart();
	}
	if(typeof (leoSelectAttr) != 'undefined')
	{
		leoSelectAttr();		
	}
	if(typeof (LeoWishlistButtonAction) != 'undefined')
	{	
		LeoWishlistButtonAction();		
	}
	if(typeof (LeoCompareButtonAction) != 'undefined')
	{		
		LeoCompareButtonAction();
	}
	if(typeof (actionQuickViewLoading) != 'undefined')
	{
		actionQuickViewLoading();
	}
}

function SetOwlCarouselFirstLast(el){
    el.find(".owl-item").removeClass("first");
    el.find(".owl-item.active").first().addClass("first");

    el.find(".owl-item").removeClass("last");
    el.find(".owl-item.active").last().addClass("last");
}
/**
 * List functions will run when document.ready()
 */
$(document).ready(function()
{
    if(typeof (ap_list_functions) != 'undefined')
    {
        for (var i = 0; i < ap_list_functions.length; i++) {
            ap_list_functions[i]();
        }
    }
});

/**
 * List functions will run when window.load()
 */
$(window).load(function(){
	if(typeof (ap_list_functions_loaded) != 'undefined')
    {
        for (var i = 0; i < ap_list_functions_loaded.length; i++) {
            ap_list_functions_loaded[i]();
        }
    }
})

$(document).ready(function(){
    // REPLACE URL IN BLOCKLANGUAGES
    if(typeof approfile_multilang_url != "undefined") {
         $.each(approfile_multilang_url, function(index, profile){
            var url_search = prestashop.urls.base_url + profile.iso_code;
            var url_change = prestashop.urls.base_url + profile.iso_code + '/' + profile.friendly_url + '.html';
            
            //console.log(url_change);
                        
			//DONGND:: update for module blockgrouptop and default
			if ($('#leo_block_top').length)
			{
				var parent_o = $('#leo_block_top .language-selector');
			}
			else
			{
				var parent_o = $('.language-selector-wrapper');
			}
			
			parent_o.find('li a').each(function(){
				
				var lang_href = $(this).attr('href');
				
				if(lang_href.indexOf(url_search) > -1 )
				{
					//window.console.log('--' + url_change);
					$(this).attr('href', url_change);
					//window.console.log(url_change);
				}
			});
			
        });
    }
    
	//DONGND:: update for module blockgrouptop and default
	if ($('#leo_block_top').length)
	{
		var parent_o_currency = $('#leo_block_top .currency-selector');
	}
	else
	{
		var parent_o_currency = $('.currency-selector');
	}
    
    // REPLACE URL IN BLOCKLANGUAGES
	parent_o_currency.find('li a').each(function(){
		
        var url_link = $(this).attr('href');
        var id_currency = getParamFromURL("id_currency", url_link);
        var SubmitCurrency = getParamFromURL("SubmitCurrency", url_link);
        
        var current_url = window.location.href;
		//DONGND:: fix for only product page, url has #
		if (prestashop.page.page_name == 'product')
		{			
			var current_url = prestashop.urls.current_url;		
		}
        var current_url = removeParamFromURL('SubmitCurrency',current_url);
        var current_url = removeParamFromURL('id_currency',current_url);
        
        if(current_url.indexOf('?') == -1){
            var new_url = current_url + '?SubmitCurrency=' + SubmitCurrency + "&id_currency=" +id_currency;
            $(this).attr('href', new_url);
        }
        else{
            var new_url = current_url + '&SubmitCurrency=' + SubmitCurrency + "&id_currency=" +id_currency;
            $(this).attr('href', new_url);
        }
    });
    
    
    prestashop.on('updateProductList', function() {
        // FIX BUG : FILTER PRODUCT NOT SHOW MORE IMAGE
        if (typeof $.LeoCustomAjax !== "undefined" && $.isFunction($.LeoCustomAjax)) {
            var leoCustomAjax = new $.LeoCustomAjax();
            leoCustomAjax.processAjax();
        }
    });
	
    // auto get link product and add to demo link on megamenu to show multi product detail
    if ($('.demo-product-detail[data-menu-type=url] a').length && prestashop.page.page_name != 'product')
    {
        $.ajax({
            type: 'POST',
            headers: {"cache-control": "no-cache"},
            url: prestashop.urls.base_url + 'modules/appagebuilder/apajax.php' + '?rand=' + new Date().getTime(),
            async: true,
            cache: false,
            dataType: "json",
            data: {"action": "get-product-link"},
            success: function (jsonData) {				
                if (jsonData)
                {
                    $('.demo-product-detail[data-menu-type=url] a').each(function(){						
                        var original_url = $(this).attr('href');
                        var layout_key = original_url.substring(original_url.indexOf('?layout='));

                        var new_url = jsonData.replace('.html', '.html'+layout_key);
                        $(this).attr('href',new_url).addClass('updated');
                    });
                }

            },
            error: function () {
            }
        });
    }
});

function removeParamFromURL(key, sourceURL) {

    var rtn = sourceURL.split("?")[0],
        param,
        params_arr = [],
        queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";

    
    if (queryString !== "") {
        params_arr = queryString.split("&");
        for (var i = params_arr.length - 1; i >= 0; i -= 1) {
            param = params_arr[i].split("=")[0];
            if (param === key) {
                params_arr.splice(i, 1);
            }
        }
        if(params_arr.length > 0){
            rtn = rtn + "?" + params_arr.join("&");
        }
    }
    return rtn;
}

function getParamFromURL(key, sourceURL) {

    var rtn = sourceURL.split("?")[0],
        param,
        params_arr = [],
        queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";

    if (queryString !== "") {
        params_arr = queryString.split("&");
        
        for (var i = params_arr.length - 1; i >= 0; i -= 1) {
            param = params_arr[i].split("=")[0];
            if (param === key) {
                return params_arr[i].split("=")[1];
            }
        }
    }
    return false;
}