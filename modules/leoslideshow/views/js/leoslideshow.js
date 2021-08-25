/**
 * @copyright Commercial License By LeoTheme.Com 
 * @email leotheme.com
 * @visit http://www.leotheme.com
 */
/**
 * List functions will run when document.ready()
 */
$(document).ready(function()
{
    if(typeof (leoslideshow_list_functions) != 'undefined')
    {
        for (var i = 0; i < leoslideshow_list_functions.length; i++)
        {
            leoslideshow_list_functions[i]();
        }
    }
});
