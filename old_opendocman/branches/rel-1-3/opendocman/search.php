<?php
session_start();
include('config.php');

if(!isset($_GET['starting_index']))
{
        $_GET['starting_index'] = 0;
}
if(!isset($_GET['stoping_index']))
{
        $_GET['stoping_index'] = $_GET['starting_index']+$GLOBALS['CONFIG']['page_limit']-1;
}
if(!isset($_GET['sort_by']))
{
        $_GET['sort_by'] = 'id';
}
if(!isset($_GET['sort_order']))
{
        $_GET['sort_order'] = 'asc';
}
if(!isset($_GET['page']))
{
        $_GET['page'] = 0;
}
if(!isset($_GET['submit']))
{
        if (!session_is_registered('uid'))
		{
			   header('Location:index.php?redirection=' . urlencode( $_SERVER['PHP_SELF'] . '?' . $HTTP_SERVER_VARS['QUERY_
			   STRING'] ) );
			                       exit;
		}

		/*$_GET['where']='department_only';
		$_GET['keyword']='Information Systems';
		$_SESSION['uid']=102;
		$_GET['submit']='submit';
		$_GET['exact_phrase']='on';
		$_GET['case_sensitivity']='';
		*/
		/// includes
		$start_time = time();
		draw_header('Search');
		draw_menu($_SESSION['uid']);
		draw_status_bar('Search', "");
		echo '<body bgcolor="white">';
		?>
                <center>
                <p>

                 <table border="0" cellspacing="5" cellpadding="5">
                  <form action=<?php echo $_SERVER['PHP_SELF']; ?> method="get">

                   <tr>
                    <td valign="top"><b>Search term</b></td>
                    <td><input type="Text" name="keyword" size="50"></td>
                    <td>Exact Phrase: <input type="checkbox" name="exact_phrase"></td>
                    <td>Case Sensitivity <input type="checkbox" name="case_sensitivity"></td>
                   </tr>
                   <tr>
                    <td valign="top"><b>Search</b></td>
                    <td><select name="where">
                      <option value="author_only">Author (Last_name  First_name)</option>
                      <option value="department_only">Department only</option>
                      <option value="category_only">Category only</option>
                      <option value="descriptions_only">Descriptions only</option>
                      <option value="filenames_only">Filenames only</option>
                      <option value="comments_only">Comments only</option>
                      <option value="all" selected>All</option>
                    </select></td>
                 </tr>

                <tr>
                  <td colspan="2" align="center">
                    <input type="Submit" name="submit" value="Search">
                    <input type="hidden" name="submit" value="Search">
                  </td>
                </tr>

               </form>
              </table>
            </center>

<?php
echo '<br><b>Load Time: ' . time() - $start_time;
draw_footer();

}
else
{
	$start_time = time();
	draw_header('Search');
	if(@$_REQUEST['anonymous'] != 'true')
		draw_menu($_SESSION['uid']);
	else
	{
		echo '<HTML><TITLE>OpenDocMan Anonymous Page: List All</TITLE></HTML>' . "\n\n";
		echo "<H1><CENTER>OpenDocMan Anonymous Page</CENTER></H1>";
	}
	draw_status_bar('Search', "");
	echo '<body bgcolor="white">';

	function search($lwhere, $lkeyword, $lexact_phrase, $lcase_sensitivity, $lsearch_array)    
    {
		$lequate = '=';
		$l_remain ='';
    	if( $lexact_phrase!='on' )
		{	
			$lkeyword2 = $lkeyword;
			$l_remain = strstr($lkeyword, ' ');
			if($l_remain!='')
				$lkeyword = strtok($lkeyword, ' ');
			$lkeyword = '%' . $lkeyword . '%';
    	}
    	if($lcase_sensitivity!='on')
    	{		
    		$lequate = ' LIKE ';
    	}
    	else 
    	{
    		$lequate = ' REGEXP BINARY';
    	}
    	$lquery = 'SELECT ' . $GLOBALS['CONFIG']['table_prefix'] . 'data.id FROM ' . $GLOBALS['CONFIG']['table_prefix'] . 'data, ' . $GLOBALS['CONFIG']['table_prefix'] . 'user, ' . $GLOBALS['CONFIG']['table_prefix'] . 'department, ' . $GLOBALS['CONFIG']['table_prefix'] . 'category WHERE ' . $GLOBALS['CONFIG']['table_prefix'] . 'data.owner = ' . $GLOBALS['CONFIG']['table_prefix'] . 'user.id AND ' . $GLOBALS['CONFIG']['table_prefix'] . 'data.department=' . $GLOBALS['CONFIG']['table_prefix'] . 'department.id AND ' . $GLOBALS['CONFIG']['table_prefix'] . 'data.category = ' . $GLOBALS['CONFIG']['table_prefix'] . 'category.id AND (';
    	$larray_len = sizeof($lsearch_array);
		switch($lwhere)
        {
        	// Put all the category for each of the OBJ in the OBJ array into an array
            // Notice, the index of the OBJ_array and the category array are synchronized.
            case 'author_locked_files':
                $lquery .= $GLOBALS['CONFIG']['table_prefix'] . 'data.status' . $lequate  . '\'' . $lkeyword . '\' AND data.owner=\'' . $_SESSION['uid'] . '\'';
                break;
        	
        	// Put all the category for each of the OBJ in the OBJ array into an array
            // Notice, the index of the OBJ_array and the category array are synchronized.
            case 'category_only':
                $lquery .= $GLOBALS['CONFIG']['table_prefix'] . 'category.name' . $lequate  . '\'' . $lkeyword . '\'';
                break;
                // Put all the author name for each of the OBJ in the OBJ array into an array
                // Notice, the index of the OBJ_array and the author name array are synchronized.
            case 'author_only':
        		if( $lexact_phrase=='on' )
				{ 
					$lquery .= $GLOBALS['CONFIG']['table_prefix'] . 'user.first_name' . $lequate . '\'' . substr($lkeyword, strpos($lkeyword, ' ')+1 ) . '\' AND ' . $GLOBALS['CONFIG']['table_prefix'] . 'user.last_name' . $lequate . '\'' . substr($lkeyword, 0, strpos($lkeyword, ' ')) . '\'';
				}
				else
				{
					$lquery .= $GLOBALS['CONFIG']['table_prefix'] . 'user.first_name' . $lequate  . '\'' . $lkeyword . '\' OR ' . $GLOBALS['CONFIG']['table_prefix'] . 'user.last_name' . $lequate . '\'' . $lkeyword . '\'';
				}
				break;
                // Put all the department name for each of the OBJ in the OBJ array into an array
                // Notice, the index of the OBJ_array and the department name array are synchronized.case 'department_only':
            case 'department_only':
                $lquery .= $GLOBALS['CONFIG']['table_prefix'] . 'department.name' . $lequate  . '\'' . $lkeyword . '\'';
                break;
                // Put all the description for each of the OBJ in the OBJ array into an array
                // Notice, the index of the OBJ_array and the description array are synchronized.
            case 'descriptions_only':
                $lquery .= $GLOBALS['CONFIG']['table_prefix'] . 'data.description' . $lequate  . '\'' . $lkeyword . '\'';
                break;
                // Put all the file name for each of the OBJ in the OBJ array into an array
                // Notice, the index of the OBJ_array and the file name array are synchronized.
            case 'filenames_only':
                $lquery .= $GLOBALS['CONFIG']['table_prefix'] . 'data.realname= \'' . $lkeyword . '\'';
                break;
                // Put all the comments for each of the OBJ in the OBJ array into an array
                // Notice, the index of the OBJ_array and the comments array are synchronized.
            case 'comments_only':
                $lquery .= $GLOBALS['CONFIG']['table_prefix'] . 'data.comment' . $lequate  . '\'' . $lkeyword . '\'';
                break;
            case 'all':
            	$lquery .= $GLOBALS['CONFIG']['table_prefix'] . 'category.name' . $lequate  . '\'' . $lkeyword . '\' OR ' . $GLOBALS['CONFIG']['table_prefix'] . 'user.first_name' . $lequate  . '\'' . $lkeyword . '\' OR ' . $GLOBALS['CONFIG']['table_prefix'] . 'user.last_name ' . $lequate  . '\'' . $lkeyword . '\' OR ' . $GLOBALS['CONFIG']['table_prefix'] . 'department.name' . $lequate  . '\'' . $lkeyword . '\' OR ' . $GLOBALS['CONFIG']['table_prefix'] . 'data.description' . $lequate  . '\'' . $lkeyword . '\' OR ' . $GLOBALS['CONFIG']['table_prefix'] . 'data.realname' . $lequate  . '\'' . $lkeyword . '\' OR ' . $GLOBALS['CONFIG']['table_prefix'] . 'data.comment' . $lequate  . '\'' . $lkeyword . '\'';
            	break;
            default : break;
        }
  	 	$lquery .= ') ORDER BY data.id ASC';
  	 	$lresult = mysql_query($lquery) or die("Error in query: $lquery" . mysql_error() );
  	 	$lindex = 0;//echo '----' . $lquery;
  	 	$lid_array = array();
  	 	$llen = mysql_num_rows($lresult);
  	 	while( $lindex < $llen )
  	 	{ 	list($lid_array[$lindex++]) = mysql_fetch_row($lresult);	} 
		if(@$l_remain != '' && $lexact_phrase != "on")
		{
			 return array_values( array_unique( array_merge($lid_array, search($lwhere, substr($l_remain, 1), $lexact_phrase, $lcase_sensitivity, $lsearch_array) ) ) );
		}
		
		return array_values( array_intersect($lsearch_array, $lid_array) );
    }
	$view_able_files_id = array();
	if(@$_REQUEST['anonymous'] != 'true')
	{
		$current_user = new User($_SESSION['uid'], $GLOBALS['connection'], $GLOBALS['database']);
		$user_perms = new User_Perms($_SESSION['uid'], $GLOBALS['connection'], $GLOBALS['database']);
		$current_user_permission = new UserPermission($_SESSION['uid'], $GLOBALS['connection'], $GLOBALS['database']);
		//$s_getFTime = getmicrotime();
		if($_GET['where'] == 'author_locked_files')
		{	$view_able_files_id = $current_user->getExpiredFileIds();}
		else
		{	$view_able_files_id = $current_user_permission->getViewableFileIds();}
	}
	else
	{
		$current_user_permission = 'ANONYMOUS';
		$l_query = 'SELECT id FROM ' . $GLOBALS['CONFIG']['table_prefix'] . 'data WHERE anonymous = 1';
		$l_result = mysql_query($l_query) or die(mysql_error());
		for($i = 0; $i<mysql_num_rows($l_result); $i++)
			list($view_able_files_id[$i]) = mysql_fetch_row($l_result);
	}
    //$e_getFTime = getmicrotime();
    $id_array_len = sizeof($view_able_files_id);
    $query_array = array();
    $search_result = search(@$_GET['where'], @$_GET['keyword'], @$_GET['exact_phrase'], @$_GET['case_sensitivity'], $view_able_files_id);
    //echo 'khoa' . sizeof($search_result);
    $page_url = $_SERVER['PHP_SELF'].'?keyword='.$_GET['keyword'].'&where='.$_GET['where'].'&submit='.$_GET['submit'];
	$sorted_result = my_sort($search_result, $_GET['sort_order'], $_GET['sort_by']);
    if(@$_REQUEST['anonymous'] == 'true')
	{	list_files($sorted_result,  $current_user_permission, $page_url . '&anonymous=true',  $GLOBALS['CONFIG']['dataDir'], $_GET['sort_order'], $_GET['sort_by'], $_GET['starting_index'], $_GET['stoping_index']);	
	}
	else
		list_files($sorted_result,  $current_user_permission, $page_url,  $GLOBALS['CONFIG']['dataDir'], $_GET['sort_order'], $_GET['sort_by'], $_GET['starting_index'], $_GET['stoping_index']);
    echo '<BR>';
    list_nav_generator(sizeof($search_result), $GLOBALS['CONFIG']['page_limit'], $GLOBALS['CONFIG']['num_page_limit'], $page_url,$_GET['page'], $_GET['sort_by'], $_GET['sort_order'] );
    draw_footer();
    //echo '<br> <b> Load Page Time: ' . (getmicrotime() - $start_time) . ' </b>';
    //echo '<br> <b> Load Permission Time: ' . ($e_getFTime - $s_getFTime) . ' </b>';
}
?>
