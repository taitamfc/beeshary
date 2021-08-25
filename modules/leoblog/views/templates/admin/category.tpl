{* 
* @Module Name: Leo Blog
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  Leotheme
* @description: Content Management
*}
<script type="text/javascript">
    var leoblog_del_img_txt = "{$leoblog_del_img_txt}";
    var leoblog_del_img_mess = "{$leoblog_del_img_mess}";
    var action = "{$action}";
    var addnew = "{$addnew}";
</script>

<div class="" id="megamenu">
    <div class="col-md-4">
        <div class="panel panel-default">
            <h3 class="panel-title">{$text_title}</h3>
            <div class="panel-content">
                {$text_content}
                <hr>
                <p>
                    <input type="button" value="{$text_value}" id="addcategory" data-loading-text="{$text_process}" class="btn btn-danger" name="addcategory">
                </p>
                <hr>{$tree nofilter}{* HTML form , no escape necessary *}
                <a href="javascript:void(0);" class="btn btn-danger delete_many_menus">
                    <i class="icon-trash"></i>&nbsp;Delete selected
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-8">{$generate_form nofilter}{* HTML form , no escape necessary *}</div>
</div>
{literal}
<script type="text/javascript">
    $("#content").PavMegaMenuList({action:action, addnew:addnew});
</script>
{/literal}