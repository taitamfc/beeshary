/*
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author Buy-addons <contact@buy-addons.com>
*  @copyright  2007-2020 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/var product_start_import = 2;var import_counter = 0;
function check_identify_exiting(){
    return false;
}
function ba_import(){    var data = $('#form_import').serialize();    var array = data.split('&');    var val;    var ktAddNew = 0;    var ktUpdate = 0;    var ktUpdate_combi = 0;    var identify_existing_items = $('#identify_existing_items').val();    var identify_existing_items_combi = $('#identify_existing_items_combi').val();    var multi_lang = $('#multi_lang').val();    for (index = 0; index < array.length; ++index) {        val = decodeURI(array[index]);        var n = val.indexOf("select");        if(n == 0){            var data_selected = val.split('=');            if(identify_existing_items =="- None -"){                if(data_selected[1] == 1){                    ktAddNew = 1;                }                if(multi_lang =="0"){                    ktAddNew = 1;                }            }else{                if(identify_existing_items =="Product Name"){                    if(data_selected[1] == 1 || data_selected[1].indexOf('product_name') != '-1'){                        ktUpdate = 1;                    }                }                if(identify_existing_items =="Product ID"){                    if(data_selected[1] == 4){                        ktUpdate = 1;                    }                }                if(identify_existing_items =="Reference code"){                    if(data_selected[1] == 2){                        ktUpdate = 1;                    }                }                if(identify_existing_items =="EAN-13 or JAN barcode"){                    if(data_selected[1] == 15){                        ktUpdate = 1;                    }                }                if(identify_existing_items =="UPC barcode"){                    if(data_selected[1] == 16){                        ktUpdate = 1;                    }                }                if(identify_existing_items == "Supplier reference"){                    if(data_selected[1] == 'supplier_reference'){                        ktUpdate = 1;                    }                }                // since 1.1.2                if(identify_existing_items == "Combination Reference"){                    if(data_selected[1] == 'ba_combination_reference'){                        ktUpdate = 1;                    }                }            }            if(identify_existing_items_combi =="Attributes"){                ktUpdate_combi = 1;            }            if(identify_existing_items_combi =="Combi Reference code"){                if(data_selected[1] == 'ba_combination_reference'){                    ktUpdate_combi = 1;                }            }            if(identify_existing_items_combi =="Combi EAN-13 or JAN barcode"){                if(data_selected[1] == 'ba_combination_ean13'){                    ktUpdate_combi = 1;                }            }            if(identify_existing_items_combi =="Combi UPC barcode"){                if(data_selected[1] == 'ba_combination_upc'){                    ktUpdate_combi = 1;                }            }            // since 1.1.5            if(identify_existing_items_combi =="Combination ID (Attribute ID)"){                if(data_selected[1] == 'ba_combination_id'){                    ktUpdate_combi = 1;                }            }        }    }    if(identify_existing_items == "- None -"){        if(ktAddNew == 0){            alert(alert_add);            return;        }    }else{        if(ktUpdate == 0){            alert(alert_update);            return;        }    }    if(ktUpdate_combi == 0){        alert(alert_update_combi);        return;    }    
    $(".ba_load").css("display","block");        $("#result").css("display","block");        $("#btnAddProduct").css("display","none");        $("#or").fadeOut("slow");
    $("#so_sp_da_them").css("display","block");
    $("#so_sp_da_them2").css("display","block");
    var value = $('#dir_file').val();
    var so_hang = $('#so_hang').val();    product_start_import = pro_start_import;
    ajaximport(value,data, product_start_import,so_hang);
}function ba_import_auto(){    var data = $('#form_import').serialize();    var array = data.split('&');    var val;    var ktAddNew = 0;    var ktUpdate = 0;    var ktUpdate_combi = 0;    var identify_existing_items = $('#identify_existing_items').val();    var identify_existing_items_combi = $('#identify_existing_items_combi').val();    var multi_lang = $('#multi_lang').val();    for (index = 0; index < array.length; ++index) {        val = decodeURI(array[index]);        var n = val.indexOf("select");        if(n == 0){            var data_selected = val.split('=');            if(identify_existing_items =="- None -"){                if(data_selected[1] == 1){                    ktAddNew = 1;                }                if(multi_lang =="0"){                    ktAddNew = 1;                }            }else{                if(identify_existing_items =="Product Name"){                    if(data_selected[1] == 1 || data_selected[1].indexOf('product_name') != '-1'){                        ktUpdate = 1;                    }                }                if(identify_existing_items =="Product ID"){                    if(data_selected[1] == 4){                        ktUpdate = 1;                    }                }                if(identify_existing_items =="Reference code"){                    if(data_selected[1] == 2){                        ktUpdate = 1;                    }                }                if(identify_existing_items =="EAN-13 or JAN barcode"){                    if(data_selected[1] == 15){                        ktUpdate = 1;                    }                }                if(identify_existing_items =="UPC barcode"){                    if(data_selected[1] == 16){                        ktUpdate = 1;                    }                }                if(identify_existing_items =="Supplier reference"){                    if(data_selected[1] == 'supplier_reference'){                        ktUpdate = 1;                    }                }                // since 1.1.2                if(identify_existing_items == "Combination Reference"){                    if(data_selected[1] == 'ba_combination_reference'){                        ktUpdate = 1;                    }                }            }            if(identify_existing_items_combi =="Attributes"){                ktUpdate_combi = 1;            }            if(identify_existing_items_combi =="Combi Reference code"){                if(data_selected[1] == 'ba_combination_reference'){                    ktUpdate_combi = 1;                }            }            if(identify_existing_items_combi =="Combi EAN-13 or JAN barcode"){                if(data_selected[1] == 'ba_combination_ean13'){                    ktUpdate_combi = 1;                }            }            if(identify_existing_items_combi =="Combi UPC barcode"){                if(data_selected[1] == 'ba_combination_upc'){                    ktUpdate_combi = 1;                }            }            // since 1.1.5            if(identify_existing_items_combi =="Combination ID (Attribute ID)"){                if(data_selected[1] == 'ba_combination_id'){                    ktUpdate_combi = 1;                }            }        }    }    if(identify_existing_items == "- None -"){        if(ktAddNew == 0){            alert(alert_add);            event.preventDefault();            return;        }    }else{        if(ktUpdate == 0){            alert(alert_update);            event.preventDefault();            return;        }    }    if(ktUpdate_combi == 0){        alert(alert_update_combi);        event.preventDefault();        return;    }}
function ajaximport(value,data,product_start_import,so_hang) {
    $.ajax({
        'url':'../modules/ba_importer/ajax_import.php',        crossDomain: true,
        'data':'ajax=true&product_start_import='+product_start_import+'&'+data+'&baimporter_token='+baimporter_token+'&batoken='+batoken,
        'type':'POST',
        'success':function(result){            var dataResult = JSON.parse(result);
            setTimeout(function(){
                var number_imported = parseInt(dataResult["number_imported"]);                var product_end = parseInt(dataResult["product_end"]);                //var start_import = product_end;                import_counter += number_imported;                                var import_header = $('#import_header').val();                if(import_header==1){                    number_imported = dataResult["number_imported"];                }else{                    number_imported = dataResult["number_imported"];                }                if (import_counter < 0) {                    import_counter = 0;                }                $('#so_sp_da_them').html(import_counter);                for(var i=0; i<dataResult["array_result"].length;i++){                    $('#result_ul').append(dataResult["array_result"][i]);                }                var so_hang = $('#so_hang').val();                $('#so_sp_da_them2').html("/" + so_hang);                var demo = $('#demo_mode').val();                if(demo==1){                    if(so_hang>20){                        so_hang = 20;                    }                    $("#so_sp_da_them3").css("display","block");                }                if (number_imported!=so_hang) {                    $('#result_ul').append(products_imported+import_counter+"/"+so_hang);                } else {                    $('#result_ul').append(total_imported+import_counter+"/"+so_hang);                }
                if(dataResult["status"] == 0){
                    product_start_import = product_end+1;                    console.log(product_start_import);
                    var data = $('#form_import').serialize();
                    var value = $('#dir_file').val();
                    ajaximport(value,data,product_start_import,so_hang);
                }else{ 
                    $('#submitAddDb').val("Finished");
                    setTimeout(function(){
                        alert("Import Successful");                        $(".ba_load").fadeOut("slow");
                    }, 500);
                    return;
                }
            }, 100);
        },
        error: function (xhr, ajaxOptions, thrownError) {
                var data = $('#form_import').serialize();
                var value = $('#dir_file').val();
                var so_hang = $('#so_hang').val();                product_start_import++;
                ajaximport(value,data,product_start_import,so_hang);
        }
    });
}
