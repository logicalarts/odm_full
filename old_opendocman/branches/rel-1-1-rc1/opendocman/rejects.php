<?php
session_start();
if (!session_is_registered('uid'))
{
        header('Location:error.php?ec=1');
        exit;
}
include ('./config.php');
// includes
if(!isset($_POST['starting_index']))
{
        $_POST['starting_index'] = 0;
}

if(!isset($_POST['stoping_index']))
{
        $_POST['stoping_index'] = $_POST['starting_index']+$GLOBALS['CONFIG']['page_limit']-1;
}

if(!isset($_POST['sort_by']))
{
        $_POST['sort_by'] = 'id';
}

if(!isset($_POST['sort_order']))
{
        $_POST['sort_order'] = 'a-z';
}

if(!isset($_POST['page']))
{
        $_POST['page'] = 0;
}

$with_caption = false;

if(!isset($_POST['submit']))
{
        draw_menu($_SESSION['uid']);
        draw_header('Rejected Files');
        @draw_status_bar('Rejected Document Listing', $_POST['last_message']);
        $page_url = $_SERVER['PHP_SELF'] . '?';

        $user_obj = new User($_SESSION['uid'], $GLOBALS['connection'], $GLOBALS['database']);
        $userperms = new UserPermission($_SESSION['uid'], $GLOBALS['connection'], $GLOBALS['database']);
        $fileobj_array = $user_obj->getRejectedFiles();
        $sorted_obj_array = obj_array_sort_interface($fileobj_array, $_POST['sort_order'], $_POST['sort_by']);
        echo '<FORM name="table" method="POST" action="' . $_SERVER['PHP_SELF'] . '">' . "\n";

?>	
                <TABLE border="1"><TR><TD>

<?php

                list_files($sorted_obj_array, $userperms, $page_url, $GLOBALS['CONFIG']['dataDir'], $_POST['sort_order'],  $_POST['sort_by'], $_POST['starting_index'], $_POST['stoping_index'], true, $with_caption);
        list_nav_generator(sizeof($sorted_obj_array), $GLOBALS['CONFIG']['page_limit'], $page_url, $_POST['page'], $_POST['sort_by'], $_POST['sort_order']);

?>

                </TD></TR><TR><TD><CENTER><INPUT type="SUBMIT" name="submit" value="Re-Submit For Review"><INPUT type="submit" name="submit" value="Delete file(s)">
                </TABLE></FORM>

<?php

}
elseif($_POST['submit'] == 'Re-Submit For Review')
{
        for($i = 0; $i < $_POST['num_checkboxes'];$i++)
                if(isset($_POST["checkbox$i"]))
                {
                        $fileid = $_POST["checkbox$i"];
                        $file_obj = new FileData($fileid, $GLOBALS['connection'], $GLOBALS['database']);
                        //$user_obj = new User($file_obj->getOwner(), $connection, $GLOBALS['database']);
                        //$mail_to = $user_obj->getEmailAddress();									
                        //mail($mail_to, $mail_subject. $file_obj->getName(), ($mail_greeting.$file_obj->getName().' '.$mail_body.$mail_salute), $mail_headers);
                        $file_obj->Publishable(0);
                }
        header('Location:' . $_SERVER['PHP_SELF'] . '?last_message=File authorization completed successfully');
}
elseif($_POST['submit']=='Delete file(s)')
{
        $url = 'delete.php?';
        $id = 0;
        for($i = 0; $i<$_POST['num_checkboxes']; $i++)
                if(isset($_POST["checkbox$i"]))
                {
                        $fileid = $_POST["checkbox$i"];
                        $url .= 'id'.$id.'='.$fileid.'&';
                        $id ++;
                }
        $url = substr($url, 0, strlen($url)-1);
        header('Location:'.$url.'&num_checkboxes='.$_POST['num_checkboxes']);
}
?>
