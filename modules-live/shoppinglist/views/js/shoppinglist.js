
/**
* JS File of module
* 
* @author Empty
* @copyright 2007-2016 PrestaShop SA
* @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

$(document).ready(function() {
    //Display shopping list on hover
    $(".shopping-list").hover(function() {
        $(this).find('ul').stop(true, false).slideDown();
    }, function() {
        $(this).find('ul').stop(true, false).slideUp();
    });

    $(".shopping-list > a").click(function() {
        return false;
    });
    
    //Add to shopping list action
    $(".shopping-list ul li a").click(function() {

        var $form = $(this).closest('form');
        var query = $form.serialize();

        $href = $(this).attr('data-href');
        $title = $('#title-product').html();
        if($(".product-reference span").length == 1) {
            $reference = " (" + $(".product-reference span").text() + ")";
        }
        else {
            $reference = "";
        }

        $.ajax({
            url: $href,
            type: 'POST',
            dataType: 'json',
            data: query + '&action=add-shopping-list&ajax=true&title=' + $title + $reference,
            success: function(msg){
                alert(msg.result);
            },
            error: function(msg){
                alert(msg.result);
            },
        });
    });
});