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
*/
function check_identify_exiting(){
    return false;
}
function ba_import(){
    $(".ba_load").css("display","block");
    $("#so_sp_da_them").css("display","block");
    $("#so_sp_da_them2").css("display","block");
    var value = $('#dir_file').val();
    var so_hang = $('#so_hang').val();
    ajaximport(value,data, product_start_import,so_hang);
}
function ajaximport(value,data,product_start_import,so_hang) {
    $.ajax({
        'url':'../modules/ba_importer/ajax_import.php',
        'data':'ajax=true&product_start_import='+product_start_import+'&'+data+'&baimporter_token='+baimporter_token+'&batoken='+batoken,
        'type':'POST',
        'success':function(result){
            setTimeout(function(){
                var number_imported = parseInt(dataResult["number_imported"]);
                if(dataResult["status"] == 0){
                    product_start_import = product_end+1;
                    var data = $('#form_import').serialize();
                    var value = $('#dir_file').val();
                    ajaximport(value,data,product_start_import,so_hang);
                }else{ 
                    $('#submitAddDb').val("Finished");
                    setTimeout(function(){
                        alert("Import Successful");
                    }, 500);
                    return;
                }
            }, 100);
        },
        error: function (xhr, ajaxOptions, thrownError) {
                var data = $('#form_import').serialize();
                var value = $('#dir_file').val();
                var so_hang = $('#so_hang').val();
                ajaximport(value,data,product_start_import,so_hang);
        }
    });
}