<!-- -------------------------Start drawing header--------------------------- --!>
<html>
    <head>
        <title>{$g_title} - {$page_title}</title>
{literal}
        <script type="text/javascript">
        <!--
        function popup(mylink, windowname)
        {
            if (! window.focus)return true;
            var href;
            if (typeof(mylink) == 'string')
                href=mylink;
            else
                href=mylink.href;
            window.open(href, windowname, 'width=300,height=500,scrollbars=yes');
            return false;
        }
        //-->
        </script>
        
<!--[if IE]>
    <script type="text/javascript" src="templates/default/js/buttonfix.js"></script>
<![endif]-->

{/literal}
<link type="text/css" rel="stylesheet" href="templates/default/default.css">
    </head>
    <body bgcolor="white">
    <!-- --------------------------End drawing header--------------------------- --!>