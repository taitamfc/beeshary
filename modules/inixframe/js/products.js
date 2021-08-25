
var getProductIds = function()
{
    if ($('#inputProducts').val() === undefined)
        return '';
    var ids = '';//id_object + ',';
    ids += $('#inputProducts').val().split('-').join(',');
    ids = ids.replace(/\,$/,'');
	console.log(ids);
    return ids;
}
    var initProductsAutocomplete = function (){
        $('#product_autocomplete_input')
            .autocomplete(location.href, {
                minChars: 1,
                autoFill: true,
                max:20,
                matchContains: true,
                mustMatch:true,
                scroll:false,
                cacheLength:0,
                formatItem: function(item) {
                    return item[1]+' - '+item[0];
                }
            }).result(addProduct);

        $('#product_autocomplete_input').setOptions({
            extraParams: {
                excludeIds : getProductIds(),
	            ajax: 1,
	            action: 'productList'
            }
        });
    };



    var addProduct = function(event, data, formatted)
    {
        if (data == null)
            return false;
        var productId = data[1];
        var productName = data[0];

        var $divProducts = $('#divProducts');
        var $inputProducts = $('#inputProducts');
        var $nameProducts = $('#nameProducts');

        /* delete product from select + add product line to the div, input_name, input_ids elements */
        $divProducts.append(productRowTemplate(productName,productId));
        $nameProducts.val($nameProducts.val() + productName + '¤');
        $inputProducts.val($inputProducts.val() + productId + '-');
        $('#product_autocomplete_input').val('');
        $('#product_autocomplete_input').setOptions({
            extraParams: {
	            excludeIds : getProductIds(),
	            ajax: 1,
	            action: 'productList'
            }
        });
    };

    var delProduct = function(id)
    {
	    var $divProducts = $('#divProducts');
	    var $inputProducts = $('#inputProducts');
	    var $nameProducts = $('#nameProducts');

        // Cut hidden fields in array
        var inputCut = $inputProducts.val().split('-');
        var nameCut = $nameProducts.val().split('¤');

        if (inputCut.length != nameCut.length)
            return showFrameWarningMessage('Bad size');


        // Reset all hidden fields
        $inputProducts.val('');
        $nameProducts.val('');
        $divProducts.html('');
        for (i in inputCut)
        {
            // If empty, error, next
            if (!inputCut[i] || !nameCut[i])
                continue ;

            // Add to hidden fields no selected products OR add to select field selected product
            if (inputCut[i] != id)
            {
                $inputProducts.val($inputProducts.val() +  inputCut[i] + '-');
                $nameProducts.val( $nameProducts.val() +  nameCut[i] + '¤');
                $divProducts.append(productRowTemplate(nameCut[i], inputCut[i]));
            }
            else
                $('#selectProducts').append('<option selected="selected" value="' + inputCut[i] + '-' + nameCut[i] + '">' + inputCut[i] + ' - ' + nameCut[i] + '</option>');
        }

        $('#product_autocomplete_input').setOptions({
	        extraParams: {
		        excludeIds : getProductIds(),
		        ajax: 1,
		        action: 'productList'
	        }
        });
    };

	var productRowTemplate = function(name, id){
		var $li = $('<li>').addClass('list-group-item');
		$li.html(name);
		var $del = $('<span>').addClass('delProduct btn btn-danger btn-xs pull-right').attr('name',id).appendTo($li);
		var icon = $('<i>').addClass('icon-trash').appendTo($del);

		return $li;
	}
 $('document').ready(function(){
    initProductsAutocomplete();

     $('#divProducts').delegate('.delProduct', 'click', function(){
         delProduct($(this).attr('name'));
     });

     $('#ajax_choose_product').delegate('input', 'keypress', function(e){
         var code = null;
         code = (e.keyCode ? e.keyCode : e.which);
         return (code == 13) ? false : true;
     });
 });