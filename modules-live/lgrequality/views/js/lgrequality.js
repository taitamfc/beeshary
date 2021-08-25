/**
 *  Please read the terms of the CLUF license attached to this module(cf "licences" folder)
 *
 *  @author    Línea Gráfica E.C.E. S.L.
 *  @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 *  @license   https://www.lineagrafica.es/licenses/license_en.pdf https://www.lineagrafica.es/licenses/license_es.pdf https://www.lineagrafica.es/licenses/license_fr.pdf
 */

function scanImages(scandirs, scanimages, force)
{
    if (scandirs) {
        $('.rate').html('0%');
        $("[name='lgrequality_requality']").hide();
        $("[name='lgrequality_recover']").hide();
        $.post('index.php', {ajax: 1, action: 'dirs', controller : 'AdminModules', fc: 'module',configure: 'lgrequality', module: 'lgrequaliry', token: lgrequality_token, force: force}).done(function (data) {
            data = $.parseJSON(data);
            $(".dirs").html(parseInt(data.directories));
            $(".images").html("0");
            if (!data.finish) {
                scanImages(true, false, 0);
            } else {
                showLoader(message_images);
                scanImages(false, true, 0);
            }
        });
    }
    if (scanimages) {
        $("[name='lgrequality_recover']").hide();
        $.post('index.php', {ajax: 1, action: 'scan', controller : 'AdminModules', configure: 'lgrequality', module: 'lgrequaliry', token: lgrequality_token}).done(function (data) {
            data = $.parseJSON(data);
            var percent = parseFloat(data.percent).toFixed(2);
            var images = parseInt(data.images);
            if (100 <= percent) {
                percent = parseInt(percent);
            }
            var size = parseFloat(data.size);
            $(".bar-original").css("width", percent + "%");
            $(".bar-original").html(percent + "%");
            $("#size_original").html(size);
            $(".images").html(images);
            if (100 > percent) {
                scanImages(false, true, 0);
            } else {
                $("[name='lgrequality_requality']").show();
                $("[name='lgrequality_recover']").show();
                hideLoader();
            }
        });
    }
    return true;
}

function requalityImages(force)
{
    $.post('index.php', {ajax: 1, action: 'requality', controller : 'AdminModules', configure: 'lgrequality', module: 'lgrequaliry', token: lgrequality_token, force: force}).done(function (data) {
        data = $.parseJSON( data );
        var percent = parseFloat(data.percent).toFixed(2);
        /*
        if (100<=percent) {
            percent = parseInt(percent);
            var orig = parseFloat($('#size_original').html()).toFixed(2);
            var comp = parseFloat($('#size_compressed').html()).toFixed(2);
            var rate = parseFloat(100-((comp * 100) / orig)).toFixed(2);
            $('.rate').html(String(rate)+'%');
        }
        */
        var size = parseFloat(data.size).toFixed(2);
        $(".bar-compressed").css("width", percent + "%");
        $(".bar-compressed").html(percent + "%");
        $("#size_compressed").html(size);
        if( 100 > percent ) {
            requalityImages(0);
        } else {
            var orig = parseFloat($('#size_original').html()).toFixed(2);
            var comp = parseFloat($('#size_compressed').html()).toFixed(2);
            var rate = parseFloat(100-((comp * 100) / orig)).toFixed(2);
            $('.rate').html(String(rate)+'%');
            $("[name='lgrequality_recover']").show();
            hideLoader();
        }
    });
    return true;
}

function recoverImages()
{
    $.post('index.php', {ajax: 1, action: 'recover', controller : 'AdminModules', configure: 'lgrequality', module: 'lgrequaliry', token: lgrequality_token}).done(function (data) {
        data = $.parseJSON( data );
        var percent = parseFloat(data.percent).toFixed(2);
        if (100<=percent) {
            percent = parseInt(percent);
        }
        $(".bar-recover").css("width", percent + "%");
        $(".bar-recover").html(percent + "%");
        if( 100 > percent ) {
            recoverImages();
        } else {
            $("[name='lgrequality_recover']").hide();
            $("[name='lgrequality_requality']").hide();
            $(".rate").html("0%");
            $("#size_compressed").html($("#size_original").html());
            $(".bar-compressed").css("width","100%");
            $(".bar-compressed").html("100%");
            hideLoader();
        }
    });
    return true;
}

function showLoader(message)
{
    $("#display-message").html(message);
    $("#display-container").css("visibility","visible");
    $("#display-container").fadeIn();
}

function hideLoader()
{
    $("#display-container").css("visibility","hidden");
    $("#display-message").html('');
    $("#display-container").fadeOut();
}

$(document).ready(function () {

    $("[name='lgrequality_scan']").click(function(e){
        $(".bar-original").css("width", "0%");
        $(".bar-original").html("0%");
        e.preventDefault();
        showLoader(message_directories);
        scanImages(true,false,1);
    });

    $("[name='lgrequality_requality']").click(function(e){
        $(".bar-compressed").css("width", "0%");
        $(".bar-compressed").html("0%");
        e.preventDefault();
        showLoader(message_requality);
        requalityImages(1);
    });

    $("[name='lgrequality_recover']").click(function(e){
        $("#bar-recover").css("display","block");
        $(".bar-recover").css("width", "0%");
        $(".bar-recover").html("0%");
        e.preventDefault();
        showLoader(message_recover);
        recoverImages();
    });

});