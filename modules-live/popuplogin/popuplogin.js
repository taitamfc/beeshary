/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-9999 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */
var fancyPopupInstance;
$(document).ready(function () {
    $('a[href="'+PopAuthUrl+'"]').click(function (evt) {
        evt.preventDefault();
        displayLoginPopup();
    });

    $('.curious_btn').on('click', function() {
        $('#signUpModal').hide('fast');
        $('.modal-backdrop:first').remove();
    });

    $('.remove_signup_form').on('click', function() {
        $('#accountSetupModal').hide('fast');
        $('.modal-backdrop:first').remove();
    });

    $('.mp_create_account').on('click', function(e) {
        e.preventDefault();
        $('.display_mp_errors').html("").hide();

        if ($('.mp_cg_chbx').prop('checked') == false) {
            let ul = $('<ul />');
            ul.append($('<li />', {text: signUpCgMsg}));
            $('.display_mp_errors').html(ul).fadeIn('slow');
            return false;
        }

        $.ajax({
            url: $('#customerSignUpForm').attr('action'),
            type: 'POST',
            dataType: 'JSON',
            data: {
                ajax: 1,
                signupForm: 1,
                firstname: $('#firstname').val(),
                lastname: $('#lastname').val(),
                email: $('#customerSignUpForm #email').val(),
                password: $('#password').val(),
                password_conf: $('#password_conf').val(),
                subscribed: $('.mp_signup_chbx:checked').val(),
                'g-recaptcha-response': $('.g-recaptcha-response').val()
            },
            async: false,
            success: function(resp) {
                if (resp.status == false) {
                    let ul = $('<ul />');

                    for(var i=0; i < resp._errors.length; i++) {
                       ul.append($('<li />', {text: resp._errors[i]}));
                    }

                    $('.display_mp_errors').html(ul).fadeIn('slow');
                } else {
                    document.location.href = resp.redirect_url;
                }
            }
        });
    });

    $('.close-signup-btn').on('click', function() {
        $('#signInModal').hide();
        $('.modal-backdrop:first').remove();
    });
    $('.signup-btn').on('click', function() {
        $('#signInModal').hide();
        $('.modal-backdrop:first').remove();
        $('.header-top-nav ul li:nth-child(2) a').trigger('click');
    });
    $('.close-signup-btn').on('click', function() {
        $('#signUpModal').hide();
        $('.modal-backdrop:first').remove();
    });
    $('.close-signup-btn').on('click', function() {
        $('#accountSetupModal').hide();
        $('.modal-backdrop:first').remove();
    });
});

function displayLoginPopup() {
    $.fancybox({
        'transitionIn': 'none',
        'transitionOut': 'none',
        'autoScale': false,
        'type': 'iframe',
        'width': 600,
        'height': '800px',
        'scrolling': 'no',
        'titleshow': false,
        'href': baseDir + 'modules/popuplogin/loginbox.php?back=' + window.location.href,
        'padding': 0,
        'wrapCSS': 'popuplogin_fb_wrapper',
        'tpl':{
            closeBtn : '<a title="Fermer" class="fancybox-item fancybox-close popuplogin_close" href="javascript:;"><i class="fa fa-times"></i></a>',
        }
    });
}
