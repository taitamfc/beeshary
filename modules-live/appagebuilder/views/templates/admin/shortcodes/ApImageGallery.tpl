{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<div class="ap-info" style="display: none;"></div>
{literal}
<script>
$(document).off("click", ".btn-create-folder");
$(document).on("click", ".btn-create-folder", function(){

    var data = "&ajax=1&action=callmethod&type_shortcode=ApImageGallery&method_name=CreateDir&path=" + $("#path").val();
    var url = $globalthis.ajaxShortCodeUrl;
    var html = '';

    $(".ap-info").html("");
    $(".ap-info").removeClass("alert-success alert-danger");
    $(".ap-info").hide();

    $("#ap_loading").show();
    $.ajax({
        type: "POST",
        headers: {"cache-control": "no-cache"},
        url: url,
        async: true,
        cache: false,
        data: data,
        dataType: "json",
        success: function (jsonData) {
            $("#ap_loading").hide();
            if (jsonData.success) {
                if(typeof jsonData.img_dir == "undefined")
                    jsonData.img_dir = "";
                html = jsonData.information + "<p style=\"font-weight: bold;\">" + jsonData.img_dir + "</p>";

                $(".ap-info").addClass("alert alert-success").html(html).slideDown();
            }
            if (jsonData.hasError) {
                if(typeof jsonData.img_dir == "undefined")
                    jsonData.img_dir = "";
                html = jsonData.error + "<p style=\"font-weight: bold;\">" + jsonData.img_dir + "</p>";

                $(".ap-info").addClass("alert alert-danger").html(html).slideDown();
            }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            $("#ap_loading").hide();
        }
    });

});
</script>
{/literal}