/**
 * @copyright Commercial License By LeoTheme.Com 
 * @email leotheme.com
 * @visit http://www.leotheme.com
 */
(function($) {
	$.fn.PavMegamenuEditor = function(opts) {
		// default configuration
		var config = $.extend({}, {
			lang:null,
			opt1: null,
			action:null,
			action_menu:null,
			id_shop:null,
			text_warning_select:'Please select One to remove?',
			text_confirm_remove:'Are you sure to remove footer row?',
			JSON:null
		}, opts);

		/**
		 * active menu 
		 */
		var activeMenu = null;
	
		/**
	 	 * fill data values for  top level menu when clicked menu.
	 	 */	

		function processMenu( item , _parent, _megamenu ){
		
			$(".form-setting").hide();
		    $("#menu-form").show();
			$.each( $("#menu-form form").serializeArray(), function(i, input ){  
				var val = '';
				if( $(_parent).data( input.name.replace("menu_","")) ){
					val = $(_parent).data( input.name.replace("menu_",""));
				}
				 $('[name='+input.name+']',"#menu-form").val(  val );	
			});
		}

		/**
	 	 * fill data values for  top level menu when clicked Sub menu.
	 	 */	
		function processSubMenu( item , _parent, _megamenu ){
 			
			var pos =  $(item).offset();
		    $('#submenu-form').css('left',pos.left  - 30 );
			$('#submenu-form').css('top',pos.top - $('#submenu-form').height() );
	 		$("#submenu-form").show();
			
			$.each( $("#submenu-form form").serializeArray(), function(i, input ){ 
				 $('[name='+input.name+']',"#submenu-form").val( $(_parent).data( input.name.replace("submenu_",""))  );	 	
			} ) ;
	 	 
		}

		/**
	 	 * menu form handler
	 	 */	
		function menuForm(){
			$("input, select","#menu-form").change( function (){

			 	if( activeMenu ){
			 		if( $(this).hasClass('menu_submenu')   ) {
					 	var item = $("a",activeMenu);
					 
				 		if( $(this).val()  && $(this).val() == 1 && !$(item).hasClass( 'dropdown-toggle' ) ) {
				 			$(item).addClass( 'dropdown-toggle' );
				 			$(item).attr( 'data-toggle', 'leo-dropdown' );

				 		 	var div = '<div class="dropdown-sub dropdown-menu"><div class="dropdown-menu-inner"><div class="row active"></div></div></div>';
				 		 	$(activeMenu).addClass('parent').addClass('dropdown');
				 		 	$(activeMenu).append( div );
				 		} else {
							if($(activeMenu).find(".dropdown-menu").length != 0){
								if(!confirm('Remove Sub Menu ?')) return false;
								$(".dropdown-menu",activeMenu).remove();
								$(".caret",activeMenu).remove();
							}	
				 		}
				 		$(activeMenu).data('submenu', $(this).val() );
				 	}else if( $(this).hasClass('menu_subwidth') ){
				 		var width = parseInt( $(this).val() );
				 		if( width > 200 ){
				 			$(".dropdown-menu", activeMenu ).width( width );
				 			$(activeMenu).data('subwidth', width ); 
				 		}
				 	}	

					else if( $(this).attr('name') == 'submenu_group' ){ 
			 	 		if( $(this).val() == 1 ){
		 	 				$(activeMenu).addClass('mega-group');
					 		$(activeMenu).children(".dropdown-menu").addClass('dropdown-sub dropdown-mega').removeClass('dropdown-menu');
					 		
			 	 		}else {
					 		$(activeMenu).removeClass('mega-group');
					 		$(activeMenu).children(".dropdown-mega").addClass('dropdown-sub dropdown-menu').removeClass('dropdown-mega'); 
					 	}
					 	$( activeMenu ).data('group', $(this).val() );
			 	 	}
			 	 }
			} );
		}
		/**
	 	 * submenu handler.
	 	 */	
		/**
	 	 * listen Events to operator Elements of MegaMenu such as link, colum, row and Process Events of buttons of setting forms.
	 	 */	
		function listenEvents( $megamenu ){

			/**
			 *  Link Action Event Handler.
			 */
			$('.form-setting').hide();
			$( 'a', $megamenu ).click( function(event){
				if($(this).hasClass("has-subhtml")){
					alert("Can not add widget beacause: this menu have sub menu type is html");
					// event.stopPropagation();
					return false;
				}
				if($(this).parent().data("subwith") != 'widget'){
					alert("Can not add widget beacause: this menu have sub menu type with none or submenu");
					// event.stopPropagation();
					// console.log('aaa');
					return false;
				}
				// console.log('test');
				var $this = this;
				var  $parent = $(this).parent();
				/* remove all current row and column are actived */
				$(".row", $megamenu).removeClass('active');
				$(".mega-col", $megamenu).removeClass('active');
				
			//	if( $parent.parent().hasClass('megamenu') ){
				 	var pos =  $(this).offset();
				    $('#menu-form').css('left',pos.left  - 30 );
					$('#menu-form').css('top',pos.top - $('#menu-form').height() );
			//	}

 				activeMenu = $parent;  
					
				if($parent.data("submenu") != 1){
					$(".menu_submenu").val(0);
				}
				else{
					$(".menu_submenu").val(1);
				}
				if($parent.data("group") != 1){
					$(".submenu_group").val(0);
				}
				else{
					$(".submenu_group").val(1);
				}
				
				if( activeMenu.data("align") ){
					$(".button-alignments button").removeClass("active");
					$( '[data-option="'+activeMenu.data("align") +'"]').addClass("active");
				}
				
				$(".menu_subwidth").val($parent.data("subwidth"));
			 	if( $parent.hasClass('dropdown-submenu') ){
			 		 $( ".dropdown-submenu", $parent.parent() ).removeClass( 'open' );
					 // console.log('test');
			 		 $parent.addClass('open');
			 		 processSubMenu( $this, $parent, $megamenu );
			 	}else {   
			 		if( $parent.parent().hasClass('megamenu') ){
	                	 $("ul.navbar-nav > li" ).removeClass('open');	
	             	}
					// console.log('test1');
	                $parent.addClass('open');
	              
                 	processMenu ( $this, $parent, $megamenu );
	              
	             } 
		          if($(this).hasClass("has-category")){
                         $(".group-submenu").hide();
                  }
				  else{
                         $(".group-submenu").show();
                  }
		         event.stopPropagation();
		         return false;  
			});


			/**
			 * Row action Events Handler
			 */
			 $("#menu-form .add-row").click( function(){
			 	var row = $( '<div class="row"></div>'  );
			 	var child = $(activeMenu).children('.dropdown-menu').children('.dropdown-menu-inner');
			 	child.append( row );
			 	child.children(".row").removeClass('active');
			 	row.addClass('active');

			 });

			  $("#menu-form .remove-row").click( function(){
			  	if( activeMenu ){
			  		 var hasMenuType = false; 
			  		 $(".row.active", activeMenu).children('.mega-col').each( function(){
			  		 	if( $(this).data('type') == 'menu' ){
			  		 		hasMenuType = true;
			  		 	}
			  		 });

			  		if( hasMenuType == false ){
		  				$(".row.active", activeMenu).remove();	
		  			}else {
		  				alert( 'You can remove Row having Menu Item(s) Inside Columns' );
		  				return true;
		  			}
		  			removeRowActive();	
			  	}
			  	
			 });

			 $($megamenu).delegate( '.row', 'click', function(e){ 
		 		$(".row",$megamenu).removeClass('active');
		 		$(this).addClass('active');  
		 		e.stopPropagation();
	    	 }); 

			 /**
			  * Column action Events Handler
			  */ 
			 $("#menu-form .add-col").click( function(){
		 		if ( activeMenu ){ 
		 			var num = 6; 
		 			var col = $( '<div class="col-sm-'+num+' mega-col active"><div></div></div>'  );
		 			$(".mega-col",activeMenu).removeClass('active');
					$( ".row.active", activeMenu ).append( col );
					col.data( 'colwidth', num );
					var cols = $(".dropdown-menu .mega-col", activeMenu ).length; 
					$(activeMenu).data('cols', cols);

		 		}
			 } );

			 $(".remove-col").click( function(){
			 	if( activeMenu ){
			 		if( $(".mega-col.active", activeMenu).data('type') == 'menu' ) {
			 			alert('You could not remove this column having menu item(s)');
			 			return true;
			 		}else {
			 			$(".mega-col.active", activeMenu).remove();
			 		}
			 	}
			 	removeColumnActive();
			 } );

			
		 	$($megamenu).delegate('.leo-widget', 'mousemove', function(e){
				if($(this).data('id_widget')){
					var keywidget =  $(this).data('id_widget');
					if(keywidget)
						$(".inject_widget_name option").each(
							function(){
								var value = $(this).val();
								if(value && value == keywidget)
									$(this).attr('selected', 'selected');	
							}
						);
				}
				$(".leo-widget",$megamenu).removeClass('active');
		 		$(this).addClass('active');
				$('.inject_widget_name').prop('disabled','disabled');
			});
			$($megamenu).delegate('.leo-widget', 'mouseleave', function(e){
				$('.inject_widget_name').removeAttr('disabled');
			});
		 	$($megamenu).delegate( '.mega-col', 'click', function(e){
				
		 		$(".mega-col",$megamenu).removeClass('active');
		 		$(this).addClass('active');
		 		
	 		 	var pos =  $(this).offset();

		 		$("#column-form").css({'top':pos.top-$("#column-form").height(), 'left':pos.left}).show();
		 		
		 		if( $(this).data('type') != 'menu' ){ 
					$("#widget-form").css({'top':pos.top+$(this).height()-15, 'left':pos.left}).show();
					$('.inject_widget').removeAttr('disabled');
		 		}else{
		 			$("#widget-form").hide();
		 		}

		 		$(".row",$megamenu).removeClass('active');

		 		$(this).parent().addClass('active');
		 		$.each( $(this).data(), function( i, val ){
	 				$('[name='+i+']','#column-form').val( val );
	 			} );

		 		e.stopPropagation(); 
		 	} );


		 	/**
		 	 * Column Form Action Event Handler
		 	 */
		 	$('input, select', '#column-form').change( function(){
		 		if( activeMenu ) {
		 			var col = $( ".mega-col.active", activeMenu );
		 			if( $(this).hasClass('colwidth') ){
		 				var cls = $(col).attr('class').replace(/col-sm-\d+/,'');
		 				$(col).attr('class', cls + ' col-sm-' + $(this).val() );
						$(col).attr('data-colwidth', $(this).val() );
		 			}
		 			$(col).data( $(this).attr('name') ,$(this).val() );
		 		}	
	 		} );

		 	$(".form-setting").each( function(){
		 		var $p = $(this);
		 		$(".popover-title span",this).click( function(){
		 			if( $p.attr('id') == 'menu-form' ){
		 				removeMenuActive();
		 			}else if( $p.attr('id') == 'column-form' ){
		 				removeColumnActive();
		 			}else {
		 				$('#widget-form').hide();
		 			}
		 		} );
		 	} );
	 		
	 		$( ".form-setting" ).draggable();	

 			/**
 			 * inject widgets
 			 */
 			 $("#btn-inject-widget").click( function(){
 			 	var wid = $('select', $(this).parent() ).val();	
 				if( wid > 0 ){
 					var col = $( ".mega-col.active", activeMenu );
                                        
 					var a =  $(col).data( 'widgets') ;

 					if( $(col).data( 'widgets') ){  
 						if( $(col).data( 'widgets').indexOf("wid-"+wid ) == -1 ) { 
 							$(col).data( 'widgets', a +"|wid-"+wid );
 						}
 					}else { 
 						$(col).data( 'widgets', "wid-"+wid );
 				 	}
					 $(col).children('div').html('<div class="loading">Loading....</div>');
					var allWidgets = {};
					$("#megamenu-content #mainmenutop .mega-col").each( function() {
						var objHook = {};
						var col = $(this);

						if( $(col).data( 'widgets') && $(col).data("type") != "menu" ){
							objHook['id_widget'] = $(col).data( 'widgets');
							objHook['id_shop'] = config.id_shop;
							allWidgets[$(col).data( 'widgets')] = objHook;
						}
		  			});
 				 	$.ajax({
						url: config.action_widget,
						cache: false,
						data: {
							ajax : true,
							allWidgets : 1,
							dataForm : JSON.stringify(allWidgets),
						},
						type:'POST',
						}).done(function( jsonData ) {
							var jsonData = jQuery.parseJSON(jsonData);
				 		$(col).children('div').html( jsonData[$(col).data( 'widgets')]['html']  );
						runEventTabWidget();
				   });
				

 				}else {
 					alert( 'Please select a widget to inject' );
 				}
 			 } );

			 /**
 			 * create new widget
 			 */
 			 $("#btn-create-widget").click( function(){
				 $(".leo-modal-action").trigger('click');
			 });
			
 			 /**
 			  * unset mega menu setting
 			  */
 			  $("#unset-data-menu").click( function(){
 				 if( confirm('Are you sure to reset megamenu configuration') ){
 				    $.ajax({
						url: config.action,
						data: 'doreset=1&id_shop='+config.id_shop,
						type:'POST',
						}).done(function( data ) {
					  		 location.reload();
				    });
				}
				return false;			 	
 			  } ); 


 			  $($megamenu).delegate( '.leo-widget', 'hover', function(){ 
				//$(".row",$megamenu).removeClass('active');
				// $(this).addClass('active'); 
				 var w = $(this); 
					 var col = $(this).parent().parent(); 
				 if( $(this).find('.w-setting').length<= 0 ){
					 var _s = $('<span class="w-setting"></span>');
					 $(w).append(_s);
					 _s.click( function(){
					 
					   var dws = "|" + col.data('widgets');
					   dws = dws.replace("|wid-" + $(w).attr('data-id_widget'),'' ).substring(1);
						 col.data('widgets',dws);
						 $(w).remove();
					 } );
				 }
			   
			});  
			 
			  $(".button-aligned button").click( function(){
 				if( activeMenu ){
	 				$(".button-aligned button").removeClass( "active");
	 				$(this).addClass( 'active' );
	 				$(activeMenu).data( 'align', $(this).data("option") );
	 			 	var cls = $( activeMenu ).attr("class").replace(/aligned-\w+/,"");	
	 			  	$( activeMenu ).attr( 'class', cls );
	 				$( activeMenu ).addClass( $(this).data("option") );
 				}
 			} );
			 

		}

	 	/**
	 	 * remove active status for current row.
	 	 */
	 	function removeRowActive(){
	 		$('#column-form').hide();
 			$( "#mainmenutop .row.active" ).removeClass('active');
	 	}

	 	/**
	 	 * remove column active and hidden column form.
	 	 */
	 	function removeColumnActive(){
	 		$('#column-form').hide();$('#widget-form').hide();
	 		$( "#mainmenutop .mega-col.active" ).removeClass('active');
	 	}

	 	/**
	 	 * remove active status for current menu, row and column and hidden all setting forms.
	 	 */
	 	function removeMenuActive(){
	 		$('.form-setting').hide();
	 		$( "#mainmenutop .open" ).removeClass('open');
	 		$( "#mainmenutop .row.active" ).removeClass('active');
 			$( "#mainmenutop .mega-col.active" ).removeClass('active');
 			if( activeMenu ) {	
		 		activeMenu = null;
	 		}
	 	}

	 	/**
	 	 * process saving menu data using ajax request. Data Post is json string
	 	 */	
	 	function saveMenuData(){
	 	 	// var output = new Array();
			var output = {};	
	 	 	 $("#megamenu-content #mainmenutop li.parent.enablewidget").each( function() {
				 	var data = $(this).data();
					var id_menu = data.id;
				 	data.rows = new Array();
					//remove id property
					delete data.id;
				 	$(this).children('.dropdown-menu').children('div').children('.row').each( function(){
				 		var row =  new Object();
				 		row.cols = new Array();
			 			$(this).children(".mega-col" ).each( function(){
			 				row.cols.push( $(this).data() );
			 			} );
			 			data.rows.push(row);
				 	} );

				 	// output.push( data );  
					output[id_menu] = data;
	 	 	 }  );
			 // console.log(output);
			 // console.log(JSON.stringify( output ));
			 // return false;
 	 	 	var j = JSON.stringify( output ); 
 	 	 	var params = 'params='+j;
 	 	 	$.ajax({
				url: config.action_menu,
				data:params+'&id_shop='+config.id_shop,
				type:'POST',
				}).done(function( data ) {
		 		  location.reload();	 
		   });
	 	}

	 	/**
	 	 * Make Ajax request to fill widget content into column
	 	 */
	 	function loadWidgets(){
			$("#leo-progress").hide();
	 		var ajaxCols = new Array();
	 		$("#megamenu-content #mainmenutop .mega-col").each( function() {
	 		 	var col = $(this);		
	 		 	
	 		 	if( $(col).data( 'widgets') && $(col).data("type") != "menu" ){  
	 		 		ajaxCols.push( col );
				}		
	 		});

	 		var cnt = 0;
	 		if( ajaxCols.length > 0 ){
	 			$("#leo-progress").show();
	 			$("#megamenu-content").hide();
	 		}
			var check_end = 0;
                        
                        
                        
                        // ONE ALL WIDGETS ONE AJAX - BEGIN
                       var allWidgets = {};
	 		$("#megamenu-content #mainmenutop .mega-col").each( function() {
                           var objHook = {};
                           var col = $(this);

                           if( $(col).data( 'widgets') && $(col).data("type") != "menu" ){
                               objHook['id_widget'] = $(col).data( 'widgets');
                               objHook['id_shop'] = config.id_shop;
                               allWidgets[$(col).data( 'widgets')] = objHook;
                           }
	 		});
                       $.ajax({
						   url: config.action_widget,
						   cache: false,
						   data: {
							   ajax : true,
							   allWidgets : 1,
							   dataForm : JSON.stringify(allWidgets),
						  	 },
						   type:'POST',
                           }).done(function( jsonData ) {
							//console.log(jsonData);
							   var jsonData = jQuery.parseJSON(jsonData);
                               $.each( ajaxCols, function (i, col) {
                                   col.children('div').html( jsonData[$(col).data( 'widgets')]['html'] );
                                   cnt++;  
                                   $("#leo-progress .progress-bar").css("width", (cnt*100)/ajaxCols.length+"%" );
                                   if( ajaxCols.length == cnt ){
                                           $("#megamenu-content").delay(1000).fadeIn();
                                           $("#leo-progress").delay(1000).fadeOut();
                                   }
                                   $( "a", col ).not(".tab-link").attr( 'href', '#megamenu-content' );
                                   if (check_end === ajaxCols.length-1)
                                   {
                                           runEventTabWidget();
                                   }
                                   check_end++;
                               });

                       });
                       return;
                        // ONE ALL WIDGETS ONE AJAX - END
                        
                        
	 		// $.each( ajaxCols, function (i, col) {
	 		// 	$.ajax({
				// 	url: config.action_widget,
				// 	data:'widgets='+$(col).data( 'widgets')+'&id_shop='+config.id_shop,
				// 	type:'POST',
				// 	}).done(function( data ) {
			 // 		col.children('div').html( data );
			 // 		cnt++;  
			 // 		$("#leo-progress .progress-bar").css("width", (cnt*100)/ajaxCols.length+"%" );
			 // 		if( ajaxCols.length == cnt ){
			 // 			$("#megamenu-content").delay(1000).fadeIn();
			 // 			$("#leo-progress").delay(1000).fadeOut();
			 // 		}
		 	// 		$( "a", col ).not(".tab-link").attr( 'href', '#megamenu-content' );
				// 	if (check_end === ajaxCols.length-1)
				// 	{
				// 		runEventTabWidget();
				// 	}
				// 	check_end++;
			 //   });	
	 		// });
	 	}

	 	/**
	 	 * reload menu data using in ajax complete and add healders to process events.
	 	 */	
	 	function reloadMegamenu(){
			var megamenu = $("#megamenu-content #mainmenutop");
			$( "a", megamenu ).attr( 'href', '#' );
			$( '[data-toggle="dropdown"]', megamenu ).attr('data-toggle','leo-dropdown'); 
			listenEvents( megamenu );
			//submenuForm();
			menuForm();
			loadWidgets();
	 	}

	 	/**
	 	 * initialize every element
	 	 */
		this.each(function() {  
			var megamenu = this;
 
			$("#form-setting").hide();
			
			$.ajax({
				url: config.action,
				}).done(function( data ) {
			 		$("#megamenu-content").html( data );
			 		reloadMegamenu(  );
			 		$("#save-data-menu").click( function(){
			 			saveMenuData();
			 		} );
		   });
		});


		return this;
	};
	
})(jQuery);

$(document).ready(function(){
	//js for widget image gallery product
	$(".fancybox").fancybox({
		openEffect	: 'none',
		closeEffect	: 'none'
	});
	
	//js for widget newsletter
	if ( typeof placeholder !== 'undefined')
	{
		$('#newsletter-input-footer').on({
			focus: function() {
				if ($(this).val() == placeholder) {
					$(this).val('');
				}
			},
			blur: function() {
				if ($(this).val() == '') {
					$(this).val(placeholder);
				}
			}
		});

		$("#newsletter_block_footer form").submit( function(){  
			if ( $('#newsletter-input-footer').val() == placeholder) {
				$("#newsletter_block_footer .alert").removeClass("hide");
				return false;
			}else {
				 $("#newsletter_block_footer .alert").addClass("hide");
				 return true;
			}
		} );
	}
	
	//js for tab html
	// if ( typeof list_tab_live_editor !== 'undefined' && list_tab_live_editor.length > 0)
	// {	
		// $.each(list_tab_live_editor,function(key, val){

			// $('#tabhtml'+val+' .nav a').click(function (e) {
				// e.preventDefault();
				// $(this).tab('show');
			// })
		// });	
	// }
	
})

//call event for tab widget at live editor
function runEventTabWidget()
{
	if ( typeof list_tab_live_editor !== 'undefined' && list_tab_live_editor.length > 0)
	{	
		$.each(list_tab_live_editor,function(key, val){
			$('#tabhtml'+val+' .nav a').click(function (e) {
				e.preventDefault();
				$(this).tab('show');
			})	
		});		
	}
	
	//js for widget image gallery category
	if ( typeof level !== 'undefined' && typeof limit !== 'undefined')
	{
		$('.widget-category_image ul.level0').each(function(){				
			$(this).find('ul').removeClass('dropdown-sub dropdown-menu');
		});	
		
		$(".widget-category_image ul.level0").each(function() {
			var check_level = $(this).parents('.widget-category_image').data('level');
			var check_limit = $(this).parents('.widget-category_image').data('limit');
			//remove .caret by check level
			$(this).find("ul.level" + check_level).parent().find('.caret').remove();
			//remove ul by check level
			$(this).find("ul.level" + check_level + " li").remove();
			var element = $(this).find("ul.level" + (check_level - 1) + " li").length;
			var count = 0;
			if(check_level > 0) {
				$(this).find("ul.level" + (check_level - 1) + " >li").each(function(){
					count = count + 1;
					if(count > check_limit){
						$(this).remove();
					}
				});
			}
		});
	}
}
