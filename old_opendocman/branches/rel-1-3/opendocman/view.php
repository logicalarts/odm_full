<?php
// view.php - performs download without updating database

// check for session and $id
session_start();
ob_end_clean();		//Make sure there are no garbage in buffer.
ob_start("callback");  	//Buffer oupt so there won't be accidental header problems
if (!session_is_registered('uid'))
{
	header('Location:index.php?redirection=' . urlencode( $_SERVER['PHP_SELF'] . '?' . $HTTP_SERVER_VARS['QUERY_STRING'] ) );
	exit;
}
include_once('config.php');

if (!isset($id) || $id == '')
{
	header('Location:error.php?ec=2');
	exit;
}

// includes
// in case file is accessed directly
// verify again that user has view rights

/*
   $query = "SELECT id, realname FROM data, perms WHERE id = '$id' AND perms.rights = '1' AND perms.uid = '$_SESSION[uid]' AND perms.fid = data.id";
   $result = mysql_query($query, $connection) or die ("Error in query: $query. " . mysql_error());
 */
//if (mysql_num_rows($result) <= 0)
$filedata = new FileData($GLOBALS['connection'], $GLOBALS['database'], 'data');
$filedata->setId($id);

if ($filedata->getError() != '')
{
	header('Location:error.php?ec=2');
	ob_end_flush();		// Flush buffer onto screens
	ob_end_clean();		// Clean up buffer
	exit;
}
else
{
	// all checks completed

	/* to avoid problems with some browsers, 
	   download script should not include parameters on the URL
	   so let's use a form and pass the parameters via POST
	 */ 

	// form not yet submitted
	// display information on how to initiate download
	if (!isset($submit))
	{
		draw_header();
		draw_menu($_SESSION['uid']);
		draw_status_bar('Add New User', $message);
		?>
			<p>

			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			<input type="hidden" name="id" value="<?php echo $_POST['id']; ?>">
			<input type="submit" name="submit" value="Click here"> to begin downloading the selected document to your local workstation.
			</form>
			Once the document has completed downloading, you may <a href="out.php">continue browsing</a> The Vault.
			<?php	

			draw_footer();

	}
	// form submitted - begin download
	else
	{
		//list($id, $realname) = mysql_fetch_row($result);
		$id = $filedata->getId();
		$realname = $filedata->getName();
		//mysql_free_result($result);

		// get the filename
		$filename = $GLOBALS['CONFIG']['dataDir'] . $_POST['id'] . '.dat';

		// send headers to browser to initiate file download
		header ('Content-Type: application/octet-stream'); 
		header ('Content-Disposition: attachment; filename='.$realname); 
		readfile($filename); 

		ob_end_flush();		//Flush buffer;
		ob_end_clean();		//Clean up
	}
}
// clean up
?>
