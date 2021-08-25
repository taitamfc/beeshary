/**
 * 2010-2016 Webkul.
 *
 * NOTICE OF LICENSE
 *
 * All right is reserved,
 * Please go through this link for complete license : https://store.webkul.com/license.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
 *
 *  @author    Webkul IN <support@webkul.com>
 *  @copyright 2010-2016 Webkul IN
 *  @license   https://store.webkul.com/license.html
 */

/////////////// This code is for migrating image according to browser ///////////////////////////////////
var matched, browser;

jQuery.uaMatch = function(ua) {
    ua = ua.toLowerCase();

    var match = /(chrome)[ \/]([\w.]+)/.exec(ua) ||
        /(webkit)[ \/]([\w.]+)/.exec(ua) ||
        /(opera)(?:.*version|)[ \/]([\w.]+)/.exec(ua) ||
        /(msie)[\s?]([\w.]+)/.exec(ua) ||
        /(trident)(?:.*? rv:([\w.]+)|)/.exec(ua) ||
        ua.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec(ua) ||
        [];

    return {
        browser: match[1] || "",
        version: match[2] || "0"
    };
};

matched = jQuery.uaMatch(navigator.userAgent);
//IE 11+ fix (Trident) 
matched.browser = matched.browser == 'trident' ? 'msie' : matched.browser;
browser = {};

if (matched.browser) {
    browser[matched.browser] = true;
    browser.version = matched.version;
}

// Chrome is Webkit, but Webkit is also Safari.
if (browser.chrome) {
    browser.webkit = true;
} else if (browser.webkit) {
    browser.safari = true;
}

jQuery.browser = browser;
/////////////////////////////////////////////////////////////////////////////////////////////////


// convert bytes into friendly format
function bytesToSize(bytes) {
    var sizes = ['Bytes', 'KB', 'MB'];
    if (bytes == 0) return 'n/a';
    var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
    return (bytes / Math.pow(1024, i)).toFixed(1) + ' ' + sizes[i];
};

// check for selected crop region
function checkForm() {
    if (parseInt($('#w').val())) return true;
    //alert(noarea_error);
    return false;
};

// update info by cropping (onChange and onSelect events handler)
function updateInfo(e) {
    $('#x1').val(e.x);
    $('#y1').val(e.y);
    $('#x2').val(e.x2);
    $('#y2').val(e.y2);
    $('#w').val(e.w);
    $('#h').val(e.h);
};

// clear info by cropping (onRelease event handler)
function clearInfo() {
    $('.info #w').val('');
    $('.info #h').val('');
};

// Create variables (in this scope) to hold the Jcrop API and image size
var jcrop_api, boundx, boundy;

function closePhotoPopup() {
    $('.step2-blank').fadeOut(500);
}

$(document).ready(function() {
    $("#holder,#holder_profile,#holder_shop").on('dragenter', function(e) {
        e.preventDefault();
        $(this).css('background', '#BBD5B8');
    });

    $("#holder,#holder_profile,#holder_shop").on('dragover', function(e) {
        e.preventDefault();
    });

    // For seller request and add & edit product page
    $("#holder").on('drop', function(e) {
        $('#pick_id').val('0');
        $(this).css('background', '#D8F9D3');
        e.preventDefault();
        var image = e.originalEvent.dataTransfer.files;
        createFormData(image);

        fileSelectHandler(image[0]);
    });
    // For edit profile page (profile image)
    $("#holder_profile").on('drop', function(e) {
        $('#pick_id').val('1');
        $(this).css('background', '#D8F9D3');
        e.preventDefault();
        var image = e.originalEvent.dataTransfer.files;
        createFormData(image);

        fileSelectHandler(image[0]);
    });
    // For edit profile page (shop logo)
    $("#holder_shop").on('drop', function(e) {
        $('#pick_id').val('2');
        $(this).css('background', '#D8F9D3');
        e.preventDefault();
        var image = e.originalEvent.dataTransfer.files;
        createFormData(image);

        fileSelectHandler(image[0]);
    });
});

function createFormData(image) {
    var formImage = new FormData();
    formImage.append('dropImage', image[0]);

    $('#wholepage_div').show();
    $('#wholepage_div').html('<img class="loading-img" src=' + img_module_dir + 'loader.gif>');
    uploadFormData(formImage);
}

function uploadFormData(formData) {
    var forproduct = $("#forproduct").val();
    if (forproduct) {
        var prod_upload = 1;
    } else {
        var prod_upload = 0;
    }

    formData.append('prod_upload', prod_upload);
    if (typeof adminupload != 'undefined') {
        formData.append('ajax', "1");
        formData.append('action', "UploadCroppedImage");
    }

    $.ajax({
        url: path_uploader,
        type: "POST",
        data: formData,
        contentType: false,
        cache: false,
        processData: false,
        success: function(data) {
            $('#wholepage_div').hide();
            $('#wholepage_div').html('');
            if (data == '1') {
                alert(imgformat_error);
                $('.step2-blank').hide();
            } else if (data == '2') {
                alert(imgsize_error);
                $('.step2-blank').hide();
            } else {
                $('#dropImage_file').val(data);
            }
        }
    });
}

function fileSelectHandler(image) {
    if (image == '')
        var oFile = $('#image_file')[0].files[0];
    else
        var oFile = image;

    // hide all errors
    $('.upload-error').hide();

    // check for image type (jpg and png are allowed)
    var rFilter = /^(image\/jpeg|image\/png|image\/gif)$/i;

    // check for image type (jpg and png are allowed)
    var rFilter = /^(image\/jpeg|image\/png|image\/gif)$/i;
    if (!rFilter.test(oFile.type)) {
        //('.upload-error').html('Please select a valid image file (jpg, png and gif are allowed)').show();
        alert(imgformat_error);
        return;
    }

    // check for file size
    if (oFile.size > 8000 * 1024) {
        //$('.upload-error').html('You have selected too big file, please select a one smaller image file less than 8MB)').show();
        alert(imgbigsize_error);
        return;
    }

    // preview element
    var oImage = document.getElementById('preview');

    // prepare HTML5 FileReader
    var oReader = new FileReader();

    oReader.onload = function(e) {
        // e.target.result contains the DataURL which we can use as a source of the image
        oImage.src = e.target.result;
        oImage.onload = function() { // onload event handler

            // display step 2
            $('.step2-blank').fadeIn(500);

            // display some basic image info
            var sResultFileSize = bytesToSize(oFile.size);
            $('#filesize').val(sResultFileSize);
            $('#filetype').val(oFile.type);
            $('#filedim').val(oImage.naturalWidth + ' x ' + oImage.naturalHeight);

            // destroy Jcrop if it is existed
            if (typeof jcrop_api != 'undefined') {
                jcrop_api.destroy();
                jcrop_api = null;
                $('#preview').width(oImage.naturalWidth);
                $('#preview').height(oImage.naturalHeight);
            }

            if ($(window).width() < 480) {
                var boxwidthval = 200;
                var boxHeightval = 200;
            } else if ($(window).width() < 720) {
                var boxwidthval = 350;
                var boxHeightval = 350;
            } else {
                var boxwidthval = 500;
                var boxHeightval = 500;
            }

            setTimeout(function() {
                // initialize Jcrop
                $('#preview').Jcrop({
                    boxWidth: boxwidthval,
                    boxHeight: boxHeightval,
                    minSize: [32, 32], // min crop size                    
                    bgFade: true, // use fade effect
                    bgOpacity: .3, // fade opacity
                    trueSize: [oImage.naturalWidth, oImage.naturalHeight],
                    setSelect: [0, oImage.naturalHeight, oImage.naturalWidth, 0],
                    onChange: updateInfo,
                    onSelect: updateInfo,
                    onRelease: clearInfo
                }, function() {

                    // use the Jcrop API to get the real image size
                    var bounds = this.getBounds();
                    boundx = bounds[0];
                    boundy = bounds[1];

                    // Store the Jcrop API in the jcrop_api variable
                    jcrop_api = this;
                });
            }, 200);

        };
    };

    // read selected file as DataURL
    oReader.readAsDataURL(oFile);
}

function chooseFile(pick_id) {
    var total_image = getTotalImage(pick_id);

    var forproduct = $("#forproduct").val();
    if (forproduct == '1') {
        var numberofimg = 100000; // for no limit
    } else {
        var numberofimg = 1;
    }

    if (total_image < numberofimg) {
        $("#image_file").click();
    } else {
        alert(stop_img_upload);
        return false;
    }
}

function getTotalImage(pick_id) {
    $('#pick_id').val(pick_id);

    if (pick_id == '0') {
        var total_image = $("#total_image").val(); //for Shop logo on seller request page & add product page
    } else if (pick_id == '1') {
        var total_image = $("#total_profileimage").val(); //for Profile image on edit profile page
    } else if (pick_id == '2') {
        var total_image = $("#total_shopimage").val(); //for Shop logo on edit profile page
    }

    return total_image;
}

$(document).ready(function() {
    var forproduct = $("#forproduct").val();
    if (forproduct == '1') {
        var numberofimg = 100000; // for no limit
    } else {
        var numberofimg = 1;
    }

    $("#uploadimage").on("click", function() {
        var total_image = getTotalImage($('#pick_id').val());
        if (total_image < numberofimg)
            $("#upload_form").submit();
        else {
            alert(stop_img_upload);
            return false;
        }
    });

    $("#upload_form").on('submit', (function(e) {
        e.preventDefault();
        if (parseInt($('#w').val()) == '') {
            alert(noarea_error);
            return false;
        } else {
            var editprofile = $('#editprofile').val();
            var pick_id = $('#pick_id').val();
            $('#wholepage_div').show();
            $('#wholepage_div').html('<img class="loading-img" src=' + img_module_dir + 'loader.gif>');

            var forproduct = $("#forproduct").val();
            if (forproduct) {
                var prod_upload = 1;
            } else {
                var prod_upload = 0;
            }

            var formData = new FormData(this);
            formData.append('prod_upload', prod_upload);
            if (typeof adminupload != 'undefined') {
                formData.append('ajax', "1");
                formData.append('action', "UploadCroppedImage");
            }

            $.ajax({
                url: path_uploader,
                type: "POST",
                data: formData,
                contentType: false,
                cache: false,
                processData: false,
                success: function(imagename) {
                    $('#wholepage_div').hide();
                    $('#wholepage_div').html('');
                    if (imagename == '1') {
                        //$('.upload-error').show();
                        //$('.upload-error').html(imgformat_error);
                        alert(imgformat_error);
                    } else if (imagename == '2') {
                        alert(imgsize_error);
                    } else {
                        var next_img_div = parseInt($("#total_image").val()) + 1;

                        if (editprofile == '1') {
                            $('#shop_hover_' + pick_id).append("<div id='editpic_" + pick_id + "' class='col-md-12'><input type='hidden' name='image_name_" + pick_id + "' id='image_name_" + pick_id + "' value='" + imagename + "'><img class='img-thumbnail cropped_img' width='110' height='110' src='" + product_img_path + "" + imagename + "' style='padding:10px 15px 10px 7px;'><div class='productimg_close' data-image-close='" + pick_id + "' title='Delete' style='right:18px;'><i class='material-icons'>&#xE872;</i></div></div>");
                            if (pick_id == '1')
                                $("#total_profileimage").val(parseInt($("#total_profileimage").val()) + 1);
                            else if (pick_id == '2')
                                $("#total_shopimage").val(parseInt($("#total_shopimage").val()) + 1);
                        } else {
                            if ($("#total_image").val() == '0')
                                $('.productimg_blank').html('');

                            $('.cleardiv').remove();
                            $('.productimg_blank').append("<div id='collection_" + next_img_div + "' class='collectiondiv'><input type='hidden' name='image_name[]' id='image_name_" + next_img_div + "' value='" + imagename + "'><img src='" + product_img_path + "" + imagename + "' class='img-thumbnail cropped_img' width='120' height='120'><div class='productimg_close' data-image-close='" + next_img_div + "' title='Delete'><i class='material-icons'>&#xE872;</i></div></div><div class='cleardiv'></div>");
                            $("#total_image").val(parseInt($("#total_image").val()) + 1);
                        }
                    }
                    $('.step2-blank').fadeOut();
                },
                error: function() {}
            });
        }

    }));

    $(document).on('click', '.productimg_close', function() {
        var editprofile = $('#editprofile').val();
        var imagediv_id = $(this).data('image-close');
        var imagename = $('#image_name_' + imagediv_id).val();

        $('#wholepage_div').show();
        $('#wholepage_div').html('<img class="loading-img" src=' + img_module_dir + 'loader.gif>');
        $.ajax({
            url: path_uploader,
            type: 'POST',
            data: {
                field: 'deleteimg',
                imagename: imagename,
                ajax: "1",
                action: "DeleteCroppedImage"
            },
            success: function(data) {
                $('#wholepage_div').hide();
                $('#wholepage_div').html('');
                if (data == '1') {
                    if (editprofile == '1') {
                        $("#editpic_" + imagediv_id).remove();
                        if (imagediv_id == '1')
                            $("#total_profileimage").val(parseInt($("#total_profileimage").val()) - 1);
                        else if (imagediv_id == '2')
                            $("#total_shopimage").val(parseInt($("#total_shopimage").val()) - 1);
                    } else {
                        $("#collection_" + imagediv_id).remove();
                        $('#total_image').val(parseInt($('#total_image').val()) - 1);
                    }
                }
            }
        });
    });

    $(document).on('click', '#uploadprofileimg', function() {
        if ($('#profileuploader').css('display') == 'none') {
            $('#profileuploader').show('slow');
        } else {
            $('#profileuploader').hide('slow');
        }
    });

    $(document).on('click', '#uploadshoplogo', function() {
        if ($('#shopuploader').css('display') == 'none') {
            $('#shopuploader').show('slow');
        } else {
            $('#shopuploader').hide('slow');
        }
    });
});