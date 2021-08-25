/**
 *  @Website: apollotheme.com - prestashop template provider
 *  @author Apollotheme <apollotheme@gmail.com>
 *  @copyright  2007-2018 Apollotheme
 *  @description: 
 */
var windowWidth = $(window).width();
var currentId;
var currentConfig;
$(document).ready(function() {
    //only for product generate
    $(".adminappagebuilderdetails #nav-sidebar").trigger("click");

    $('.plist-eedit').click(function(){
        element = $(this).data('element');
        $.fancybox.open([{
                type: 'iframe', 
                href : ($('#appagebuilder_products_form').length?$('#appagebuilder_products_form').attr('action'):$('#appagebuilder_details_form').attr('action')) + '&pelement=' + element,
                afterLoad:function(){
                    if( $('body',$('.fancybox-iframe').contents()).find("#main").length  ){
                        hideSomeElement();
                        $('.fancybox-iframe').load( hideSomeElement );
                    }else { 
                        $('body',$('.fancybox-iframe').contents()).find("#psException").html('<div class="alert error">Can not find this element</div>');
                    }
                },
                afterClose: function (event, ui) { 
                }
            }], {
            padding: 10
        });
    });

    $('.element-list .plist-element').draggable({
      connectToSortable: ".product-container .content",
      revert: "true",
      helper: "clone",
      handle: ".gaction-drag",
      stop: function() {
        $( ".product-container .content" ).sortable({
            revert: false
        });
        setProFormAction();
        setSortAble();
      }
    });

    $('.show-postion').click(function(){$("#postion_layout img").show()});
    $('.postion-img-co').click(function(){$("#postion_layout img").toggle()});
    
    
    $(document).on("click", ".column-add", function () {
        createColumn(this);
        $(currentId).find('.btn-add-column').trigger( "click" );
        editcolumn();
		//DONGND:: re-call event for new element
		setProFormAction();
		setSortAble();
    });
    setProFormAction();
    setSortAble();

    $('body').on('click', function (e) {
        $('[data-toggle=popover]').each(function () {
            // hide any open popovers when the anywhere else in the body is clicked
            if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                $(this).popover('hide');
            }
        });
    });

    widthselect();
    setCssClass();
    saveData();
    thumbconfig();
    //DONGND:: add config for zoom
    zoomconfig();
        
    $('#appagebuilder_details_form').submit(function() {
        genreateForm();
    });

    //DONGND:: setup ui for element
    $('.ap_proGrid .row, .ap_proGrid .column-row').addClass('ui-widget ui-widget-content ui-helper-clearfix ui-corner-all');
    $('.ap_proGrid .gaction-drag, .ap_proGrid .caction-drag, .ap_proGrid .waction-drag').addClass('ui-widget-header ui-corner-all');	
});

function thumbconfig(){
    $('.select_thumb').change(function(){
        if($(this).val() == 'none')
            $(this).closest('.formmodal').find('.select_thumb-none').hide();
        else
            $(this).closest('.formmodal').find('.select_thumb-none').show();
    });
}

//DONGND:: add config for zoom
function zoomconfig(){
    $('.select_zoom').change(function(){
        if($(this).val() == 'none' || $(this).val() == 'in' || $(this).val() == 'in_scrooll')
            $(this).closest('.formmodal').find('.select-zoom-none').hide();
        else
            $(this).closest('.formmodal').find('.select-zoom-none').show();
    });
}

function genreateForm(){
    //generate grid first
    var ObjectFrom = {};
    ObjectFrom.gridLeft = returnObjElemnt('.ap_proGrid .gridLeft-block-content');
    ObjectFrom.class = $("#main_class").val();
    // console.log(ObjectFrom);
    $('input[name=params]').val(JSON.stringify(ObjectFrom));
}

function returnObjElemnt(element){
    var Object = {};
    // console.log(element);
    $(element).children().each(function(iElement){
        var Obj = {};
        Obj.name = $(this).data('element');
        if($(this).data("form") == undefined || $(this).data("form") == "" || $(this).data("form") == '\"\"')
            Obj.form = "";
        else
            Obj.form = $(this).data('form');

        if(Obj.name=='product_image_with_thumb'){
            if (Obj.form.templateview == 'left' || Obj.form.templateview == 'right' || Obj.form.templateview == 'bottom')
                $("#main_class").val('product-image-thumbs product-thumbs-'+Obj.form.templateview);
            else $("#main_class").val('product-image-thumbs no-thumbs');
        }

        if(Obj.name=='product_image_show_all'){
            $("#main_class").val('product-image-gallery');
        }

        if($(this).hasClass('functional_buttons')){
            
            Obj.columns = {};
            $(this).find('.column-row').each(function(yElement){
                var ObjectColumn = {};
                ObjectColumn.form = $(this).data('form');
                ObjectColumn.element = 'column';
                ObjectColumn.sub = returnObjElemnt($('.content', $(this)));
                Obj.columns[yElement] = ObjectColumn;
            });
        }
        if($(this).hasClass('code')){
            Obj.code = replaceSpecialString($('textarea', $(this)).val());
        }
        
        Object[iElement] = Obj;
    });
    //console.log($(element).children());
    //console.log(Object);

    return Object;
}
function replaceSpecialString(str){
    return str.replace(/\t/g, "_APTAB_").replace(/\r/g, "_APNEWLINE_").replace(/\n/g, "_APENTER_").replace(/"/g, "_APQUOT_").replace(/'/g, "_APAPOST_");
}
function saveData(){
    $(".btn-savewidget").click(function(){
		// console.log($(".formmodal"));
        var data = getFormData($(".formmodal"));
		// console.log(data);
        $("#modal_form .close").trigger('click');
        $(currentConfig).data('form', data);
    });
}

function getFormData($form){
    var unindexed_array = $form.serializeArray();
    var indexed_array = {};

    if($(currentConfig).hasClass('column-row'))
    {
        $(currentConfig).attr('class', 'column-row plist-element');
    }

    $.map(unindexed_array, function(n, i){
        if(n['name']!='hidden_from[]')
            indexed_array[n['name']] = n['value'];
        //process class of column
        if($(currentConfig).hasClass('column-row') && (n['name'] == 'xl' || n['name'] == 'lg' || n['name'] == 'md' || n['name'] == 'sm' || n['name'] == 'xs' || n['name'] == 'sp'))
            $(currentConfig).addClass('col-'+n['name']+'-'+n['value']);
    
    });

    return indexed_array;
}

function hideSomeElement(){
    $('body',$('.fancybox-iframe').contents()).addClass("page-sidebar-closed");
}

function setSortAble(){
    $( ".product-container .content" ).sortable({
      connectWith: ".content",
    });
}

function setCssClass(){
    if ($(".select-class").length) {
        $(".select-class").click(function () {
            if ($(this).is(':checked')) {
                
                $('.select-class').each(function() {
                    // REMOVE ALL CHECKBOX VALUE IN TEXT
                    var classChk = $(this).data("value");
                    input_text = $(this).closest('.well').find('.element_class').first().val();
                    // trim string
                    var input_text = input_text.replace(classChk, "");
                    input_text = input_text.split("  ").join(" ");
                    input_text = $.trim(input_text);

                    $(this).closest('.well').find('.element_class').first().val(input_text);
                });

                var classChk = $(this).data("value");
                var elementText = $(this).closest('.well').find('.element_class').first();

                // SET VALUE CHECKBOX TO TEXT
                if ($(elementText).val().indexOf(classChk) == -1) {
                  if ($(elementText).val() != "") {
                    $(elementText).val($(elementText).val() + " " + classChk);
                  } else {
                    $(elementText).val(classChk);
                  }
                }
            } 
        });
        
        $(".chk-row").click(function () {
            var classChk = $(this).data("value");
            var elementText = $(this).closest('.well').find('.element_class').first();
            if ($(elementText).val().indexOf(classChk) == -1) {
                // NOT EXIST AND ADD
                  if ($(elementText).val() != "") {
                    $(elementText).val($(elementText).val() + " " + classChk);
                  } else {
                    $(elementText).val(classChk);
                  }
            }else{
                // EXIST AND REMOVE
                var find = classChk;
                var re = new RegExp(find, 'g');
                var text = $(elementText).val();
                text = text.replace(re, '');
                $(elementText).val(text);
            }
        });

        $(".element_class").change(function () {
            elementChk = $(this).closest('.well').find('input[type=checkbox]');
            classText = $(this).val();
            $(elementChk).each(function () {
                classChk = $(this).data("value");
                if (classText.indexOf(classChk) != -1) {
                    if (!$(this).is(':checked'))
                        $(this).prop("checked", true);
                } else {
                    $(this).prop("checked", false);
                }
            });
        });
        $(".element_class").trigger("change");
    }
}

//set action when c
function setProFormAction(){
    $('#home_wrapper .plist-code').each(function(){
        if(!$(this).hasClass('setaction')){
            $(this).click(function(){
                textAre = $(this).closest('.plist-element').find('textarea').first();
                if(textAre.attr('rows') == 20)
                    $(textAre).attr('rows',5);
                else
                    $(textAre).attr('rows',20);
            });
            $(this).addClass('setaction');
        }

    });

    $('#home_wrapper .btn-add-column').each(function(){

        if(!$(this).hasClass('setaction')){
            $(this).popover({
                html: true,
                content: function () {
                    currentId = $(this).closest('.plist-element');
                    return $('#addnew-column-form').html();
                }
            });
            $(this).mousedown(function(){
              // toggle popover when link is clicked
              $(this).popover('toggle');
            });

            $(this).addClass('setaction');
        }

    });
    
    $('#home_wrapper .plist-eremove').each(function(){
        if(!$(this).hasClass('setaction')){
            $(this).click(function(){
                if(!confirm("Are you sure to remove?")) return false;
                $(this).closest('.plist-element').remove();
                $(this).addClass('setaction');
            });
        }
    });

    $('#home_wrapper .btn-edit-group').each(function(){
        if(!$(this).hasClass('setaction')){
            $(this).click(function(){
                currentConfig = $(this).closest('.plist-element');
                $('#modal_form .modal-footer').show();

                $('#modal_form .modal-body').html('');

                column_config = $("#group_config").clone(true);
                
                //load config
                data = $(currentConfig).data('form');
                column_config = setData(data, column_config);
                $('#modal_form .modal-body').append('<form class="formmodal"></form>');
                $('#modal_form .formmodal').append(column_config);

                $('#modal_form').removeClass('modal-new').addClass('modal-edit');
                $("#modal_form").modal({
                    "backdrop": "static"
                });
            });
            $(this).addClass('setaction');
        }
    });

    $('#home_wrapper .element-config').each(function(){
        if(!$(this).hasClass('setaction')){
            $(this).click(function(){
                currentConfig = $(this).closest('.plist-element');
                $('#modal_form .modal-footer').show();

                $('#modal_form .modal-body').html('');

                dataconfig = $(this).data('config');

                column_config = $("#"+dataconfig).clone(true);
                //load config
                
                data = $(currentConfig).data('form');
                
                column_config = setData(data, column_config);

                if($(column_config).find('.select_thumb').val() == 'none')
                    $(column_config).find('.select_thumb-none').hide();
                else
                    $(column_config).find('.select_thumb-none').show();
				
				//DONGND:: add zoom config
				if($(column_config).find('.select_zoom').val() == 'none' || $(column_config).find('.select_zoom').val() == 'in' || $(column_config).find('.select_zoom').val() == 'in_scrooll')
                    $(column_config).find('.select-zoom-none').hide();
                else
                    $(column_config).find('.select-zoom-none').show();

                $('#modal_form .modal-body').append('<form class="formmodal"></form>');
                $('#modal_form .formmodal').append(column_config);

                $('#modal_form').removeClass('modal-new').addClass('modal-edit');
                $("#modal_form").modal({
                    "backdrop": "static"
                });
            });
            $(this).addClass('setaction');
        }
    });

    editcolumn();
}
function setData(data, column_config){
    if(!data || data == 'undefined' || data == 'undefined') return column_config;
    
    Object.keys(data).forEach(function (key) {
        //$('.' + key).data('form', dataObj[key]);
        $(column_config).find('input[name='+key+']').val(data[key]);

        $(column_config).find('select[name='+key+']').val(data[key]);

        if(key=="class" && $(column_config).find('.select-class').length)
            $(column_config).find('.select-class').each(function(){
                if(data[key].indexOf($(this).data('value'))>-1) $(this).prop("checked", true);
            });
        if(key=="xl" || key=="lg" || key=="md" || key=="sm" || key=="xs" || key=="sp"){
            classcss = 'width-select-'+key+'-'+data[key];
            classcss = classcss.replace(".", "-");
            $(column_config).find('.'+classcss).trigger('click');
        }
    });

    return column_config;
}
function editcolumn()
{
    $('#home_wrapper .btn-edit-column').each(function(){
        if(!$(this).hasClass('setaction')){
            $(this).click(function(){
                currentConfig = $(this).closest('.plist-element');

                $('#modal_form .modal-body').html('');

                $('#modal_form .modal-footer').show();

                data = $(currentConfig).data('form');
                //console.log(data);
                column_config = $("#column_config").clone(true);
                column_config = setData(data, column_config);

                $('#modal_form .modal-body').append('<form class="formmodal"></form>');
                $('#modal_form .formmodal').append(column_config);

                $('#modal_form').removeClass('modal-new').addClass('modal-edit');
                $("#modal_form").modal({
                    "backdrop": "static"
                });
                //widthselect();
            });
            $(this).addClass('setaction');
        }
    });
}
function widthselect(){
    $('.width-select').each(function(){
        if(!$(this).hasClass('setaction')){
            $('.width-select').click(function () {
                btnGroup = $(this).closest('.btn-group');
                spanObj = $('.width-val', $(this));
                width = $(spanObj).data('width');
                $('.col-val', $(btnGroup)).val(width);
                $('.apbtn-width .width-val', $(btnGroup)).html($(spanObj).html());
                $('.apbtn-width .width-val', $(btnGroup)).attr('class', $(spanObj).attr('class'));
            });
            $(this).addClass('setaction');
        }
    });
}
function createColumn(obj) {
    var widthCol = $(obj).data('width');
    var classActive = returnWidthClass();
    var col = $(obj).data('col');
    var realValue = widthCol.toString().replace('.', '-');
    for (var i = 1; i <= col; i++) {
        wrapper = currentId.find('.group');
        
        column = $('#default_column').clone();
        var cls = $(column).attr("class");
        //column-row col-sp-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 ui-widget ui-widget-content ui-helper-clearfix ui-corner-all
        cls = cls.replace("col-xl-12", "col-xl-" + realValue);
        cls = cls.replace("col-lg-12", "col-lg-" + realValue);
        cls = cls.replace("col-md-12", "col-md-" + realValue);
        cls = cls.replace("col-sm-12", "col-sm-" + realValue);
        cls = cls.replace("col-xs-12", "col-xs-" + realValue);
        cls = cls.replace("col-sp-12", "col-sp-" + realValue);
        $(column).attr("class", cls);
        objColumn = {form_id: "form_" + getRandomNumber()};
        if (classActive == "md" || classActive == "lg" || classActive == "xl") {
            objColumn.md = widthCol;
            objColumn.lg = widthCol;
            objColumn.xl = widthCol;
        }
		//DONGND:: set default for sm, xs, sp
		objColumn.sm = 12;
		objColumn.xs = 12;
		objColumn.sp = 12;
        //jQuery.extend(objColumn, $globalthis.getColDefault());
        $(column).data("form", objColumn);

        column.removeAttr('id');
        wrapper.append(column);
        getNumberColumnInClass(column, classActive);
        $(".label-tooltip").tooltip();
    }
}

function getNumberColumnInClass(obj, type) {
    var cls = $(obj).attr("class").split(" ");
    var len = cls.length;
    var result = "";
    for (var i = 0; i < len; i++) {
        if (cls[i].search("col-" + type) >= 0) {
            result = cls[i];
            break;
        }
    }
    var temp = result.replace("col-" + type + "-", "");
    $(obj).find(".pull-right .btn-group .btn span:first-child").attr("class", "width-val ap-w-" + temp);
    var group = $(obj).find("ul.dropdown-menu-right");
    $(group).find("li").removeClass("selected");
    $(group).find(".col-" + temp).addClass("selected");
}

function getRandomNumber()
{
    return (+new Date() + (Math.random() * 10000000000000000)).toString().replace('.', '');
}
function returnWidthClass(width) {
    if (!width)
        width = windowWidth;

    if (parseInt(width) >= 1200)
        return 'xl';
    if (parseInt(width) >= 992)
        return 'lg';
    if (parseInt(width) >= 768)
        return 'md';
    if (parseInt(width) >= 576)
        return 'sm';
    if (parseInt(width) >= 480)
        return 'xs';
    if (parseInt(width) < 480)
        return 'sp';
};
$(document).on("click", ".btn-fwidth", function () {
    $('#home_wrapper').css('width', $(this).data('width'));

    btnElement = $(this);
    $('.btn-fwidth').removeClass('active');
    $(this).addClass('active');
    //reset    
    if ($(this).hasClass('width-default')) {
        windowWidth = $(window).width();
        $('#home_wrapper').attr('class', 'default');
    } else {
        $('#home_wrapper').attr('class', 'col-' + returnWidthClass(parseInt($(this).data('width'))));
        windowWidth = $(this).data('width');
    }
    classVal = returnWidthClass();
    $(".column-row", $('#home_wrapper')).each(function () {
        valueFra = $(this).data("form")[classVal];
        $(".apbtn-width .width-val", $(this)).attr("class", "width-val ap-w-" + valueFra.toString().replace(".", "-"));
    });
    initColumnSetting();
});

function initColumnSetting() {
    var classActive = returnWidthClass();
    $(".column-row").each(function () {
        getNumberColumnInClass(this, classActive);
    });
}

function getNumberColumnInClass(obj, type) {
    var cls = $(obj).attr("class").split(" ");
    var len = cls.length;
    var result = "";
    for (var i = 0; i < len; i++) {
        if (cls[i].search("col-" + type) >= 0) {
            result = cls[i];
            break;
        }
    }
    var temp = result.replace("col-" + type + "-", "");
    $(obj).find(".pull-right .btn-group .btn span:first-child").attr("class", "width-val ap-w-" + temp);
    var group = $(obj).find("ul.dropdown-menu-right");
    $(group).find("li").removeClass("selected");
    $(group).find(".col-" + temp).addClass("selected");
}