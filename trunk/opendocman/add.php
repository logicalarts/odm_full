<?php
/*
							ADD.PHP DOCUMENTATION
This page will allow user to set rights to every department.  It uses javascript to handle client-side data-storing and data-swapping.  Each time the data is stored, it is stored onto an array of objects of class Deparments.  It is also stored onto hidden form field in the page for php to access since php and javascript do not communicate (server-side and client-side share different environment).
As the user choose a deparment from the drop box named dept_drop_box, loadData(_selectedIndex) function is invoked.
After the data is loaded for the chosen deparment, if the user changes the right setting (right radio button e.g. "view", "read")
setData(selected_rb_name) is invoked.  This function will set the data in the appropriate deparment[] and it will set the hidden field as wel.  The connection between hidden field and department[] is the hidden field's name and the deparment[].getName().  The department names in the array is populated with the correct department names from the database.  This will lead to problems.  There will be deparment names of more than one word eg. "Information Systems".  The hidden field's accessible name cannot be more than one word.  PHP cannot access multiple word variables.  Therefore, javascript spTo_(string) (space to underscore) will go through and subtitude all the spaces with the underscore character. */

session_start();

if (!session_is_registered('SESSION_UID'))
{
	header('Location:error.php?ec=1');
	exit;
}
include('config.php');
// connect to DB
$connection = mysql_connect($hostname, $user, $pass) or die ('Unable to connect!');
if(!$submit) //un_submitted form
{
	draw_header('Add New File');
	draw_menu($SESSION_UID);
	draw_status_bar('Add new document', $last_message);
	echo '<body bgcolor="white">';
	echo '<center>'."\n".'<table border="0" cellspacing="5" cellpadding="5">'."\n";
	//////////////////////////Get Current User's department id///////////////////
	$query ='SELECT user.department from user where user.id=' . $SESSION_UID;
	$result = mysql_db_query($database, $query, $connection) or die ("Error in query: $query. " . mysql_error());
	if(mysql_num_rows($result) != 1) /////////////If somehow this user belongs to many departments, then error out.
	{
		header('Location:error.php?ec=14');
		ob_end_flush();
		ob_end_clean();
		exit; //non-unique error
	}
	list($current_user_dept) = mysql_fetch_row($result);
	//Get a list of department names and id to populate javascript obj//
	$query = "SELECT name, id FROM department ORDER by name";
	$result = mysql_db_query ($database, $query, $connection) or die ("Error in query: $query. " . mysql_error());
	$dept_data = $result;
	$index = 0;
	///////Define a class that hold Department information (id, name, and rights)/////////
	//this class will be used to temporarily hold department information client-side wise//
	?>
	<Script Language="JavaScript">
	
		 //define a class like structure to hold multiple data
    		function Department(name, id, rights)
    		{
       			this.name = name;
        		this.id = id;
        		this.rights = rights;
        		this.isset_flag = false;
        		if (typeof(_department_prototype_called) == "undefined")
        		{
             			_department_prototype_called = true;
            		 	Department.prototype.getName = getName;
            			Department.prototype.getId = getId;
            			Department.prototype.getRights = getRights;
             			Department.prototype.setName = setName;
            	 		Department.prototype.setId = setId;
             			Department.prototype.setRights = setRights;
             			Department.prototype.issetFlag = issetFlag;
             			Department.prototype.setFlag = setFlag;

        		}
	    		function setFlag(set_boolean)
	    		{	this.isset_flag = set_boolean;	}

       			function getName()
        		{       return this.name;		}

       			function getId()
        		{       return this.id;	                }
			
				function getRights()
				{	return parseInt(this.rights);		}

				function setRights(rights)
        		{       this.rights = parseInt(rights); }

       	 		function setName(name)
        		{       this.name = name;               }

				function setId(id)
            	{       this.id = id;         }

				function issetFlag()
            	{       return this.isset_flag;         }
    		}
	
	///Create default_Setting and all_Setting obj for mass department setting/////
	var default_Setting_pos = 0;
	var all_Setting_pos = 1;
	var departments = new Array();
	var default_Setting = new Department("Default Setting for Unset Department", "0", "0");
	var all_Setting = new Department("All", "0", "0");
	departments[all_Setting_pos] = all_Setting; 
	departments[default_Setting_pos] = default_Setting;
	/////////////////////////Populate Department obj////////////////////////////////
<?php
	while( list($dept_name, $dept_id) = mysql_fetch_row($result) )
	{
		if($dept_id == $current_user_dept)
		{         
			echo 'departments[' . ($index+2) . '] = new Department("' . $dept_name . '", "' . $dept_id . '", "1")' . "\n";
		}
		else
		{
            echo 'departments[' . ($index+2) . '] = new Department("' . $dept_name . '", "' . $dept_id . '", "0")' . "\n";
        }
		$index++;
	}
?>
	</Script>
	<!-- file upload formu using ENCTYPE -->
	<form name="main" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
	<input type="hidden" name="MAX_FILE_SIZE" value="50000000">
	<tr>
	<td>
	<a class="body" tabindex=1 href="help.html#Add_File_-_File_Location" onClick="return popup(this, 'Help')" style="text-decoration:none">File Location</a>
	</td>
	<td colspan=3><input tabindex="0" name="file" type="file">
	</td>
	</tr>
	<tr>
	<td>
	<a class="body" tabindex= href="help.html#Add_File_-_Category"  onClick="return popup(this, 'Help')" style="text-decoration:none">Category</a>
	</td>
	<td colspan=3><select tabindex=2 name="category" >
<?php
	/////////////// Populate category drop down list//////////////
	$query = "SELECT id, name FROM category ORDER BY name";
	$result = mysql_db_query($database, $query, $connection) or die ("Error in query: $query. " . mysql_error());
	while(list($id, $name) = mysql_fetch_row($result)) 
	{ 
		echo '<option value="' . $id . '">' . $name . '</option>'; 
	}
	mysql_free_result ($result);
?>
	</select>
	</td>
	</tr>
	<!-- Set Department rights on the file -->
        <TR>
	<TD>
	<a class="body" href="help.html#Add_File_-_Department" onClick="return popup(this, 'Help')" style="text-decoration:none">Department</a>
	</TD>
     		<TD COLSPAN=3><SELECT tabindex=3 NAME="dept_drop_box" onChange ="loadDeptData(this.selectedIndex)">
				<option value=0> Select a Department</option>
				<option value=1> Default Setting for Unset Department</option>
				<option value=2> All Departments</option>
<?php
	//////Populate department drop down list/////////////////
   	$query = "SELECT id, name FROM department ORDER BY name";
   	$result = mysql_db_query($database, $query, $connection) or die ("Error in query: $query. " . mysql_error());
    //since we want value to corepodant to group id, 2 must be added to compesate for the first two none group related options.
  	while(list($id, $name) = mysql_fetch_row($result))
    {
    	$id+=2;
    	//don't put quotes around values.  javascript might not work
		echo '	<option value ="' . $id . '" name="' . $name . '">'. $name . '</option>' . "\n";  
    }
	mysql_free_result ($result);
?>
    </SELECT>
	</TD>
    </TR>
    <TR>
	<!-- Loading Authority radio_button group -->
	<TD><a ndex="24" class="body" href="help.html#Add_File_-_Authority" onClick="return popup(this, 'Help')" style="text-decoration:none">Authority</a></td>
	<!-- <TD><a href="help.html" onClick="return popup(this, 'Help')">Authority</a></TD> -->
	<TD>
<?php
      	$query = "SELECT RightId, Description FROM rights order by RightId";
      	$result = mysql_db_query($database, $query, $connection) or die("Error in querry: $query. " . mysql_error());
      	while(list($RightId, $Description) = mysql_fetch_row($result))
      	{	
      		echo $Description.'<input type ="radio" name ="'.$Description.'" value="' . $RightId . '" onClick="setData(this.name)"> |'."\n";
		}     
?>
	</TD>
	</TR>
	<tr>
	<td>
        <a class="body" href="help.html#Add_File_-_Description" onClick="return popup(this, 'Help')" style="text-decoration:none">Description</a>
        </td>
	<td colspan="3"><input tabindex="5" type="Text" name="description" size="50"></td>
	</tr>
	
	<tr>
	<td>
        <a class="body" href="help.html#Add_File_-_Comment" onClick="return popup(this, 'Help')" style="text-decoration:none">Comment</a>
        </td>
	<td colspan="3"><textarea tabindex="6" name="comment" rows="4"></textarea></td>
	</tr>
	
	<TABLE border="0" cellspacing="0" cellpadding="3" NOWRAP>
	<tr nowrap>
	<td NOWRAP><b>Specific Permissions Settings</b>
        </td>
	</TR>
	<TR><TD></TD>
	<td valign="top" align="center"><a class="body" href="help.html#Rights_-_Forbidden" onClick="return popup(this, 'Help')" style="text-decoration:none">Forbidden</a></td>
	<td valign="top" align="center"><a class="body" href="help.html#Rights_-_View" onClick="return popup(this, 'Help')" style="text-decoration:none">View</a></td>
	<td valign="top" align="center"><a class="body" href="help.html#Rights_-_Read" onClick="return popup(this, 'Help')" style="text-decoration:none">Read</a></td>
	<td valign="top" align="center"><a class="body" href="help.html#Rights_-_Modify" onClick="return popup(this, 'Help')" style="text-decoration:none">Modify</a></td>
	<td valign="top" align="center"><a class="body" href="help.html#Rights_-_Admin" onClick="return popup(this, 'Help')" style="text-decoration:none">Admin</a></td>
	</tr>
	<tr>
	<TD></TD>
	<td><select tabindex="8" name="forbidden[]" multiple size="10">
<?php
	
	// query to get a list of available users
		$query = "SELECT id, last_name, first_name FROM user ORDER BY last_name";
		$result = mysql_db_query($database, $query, $connection) or die ("Error in query: $query. " . mysql_error());
		//////////////////Forbidden////////////////////
		while(list($id, $last_name, $first_name) = mysql_fetch_row($result))
		{
			$str = '<option value="' . $id . '"';
			// select current user's name
			$str .= '>'.$last_name.', '.$first_name.'</option>';
			echo $str;
		}
		mysql_free_result ($result);
?>
	</select></td>
	<td><select tabindex="9" name="view[]" multiple size="10">
<?php 
		////////////////////View//////////////////////////
		$query = "SELECT id, last_name, first_name FROM user ORDER BY last_name";
		$result = mysql_db_query($database, $query, $connection) or die ("Error in query: $query. " . mysql_error());
		//////////////////Forbidden////////////////////
		while(list($id, $last_name, $first_name) = mysql_fetch_row($result))
		{
			$str = '<option value="' . $id . '"';
			// select current user's name
			if($id == $SESSION_UID) {$str .= ' selected';}
			$str .= '>'.$last_name.', '.$first_name.'</option>';
			echo $str;
		}
		mysql_free_result ($result);
?>
	</SELECT>
	<td><select tabindex="10"  name="read[]" multiple size="10">
<?php
	////////////////////Read//////////////////////////
	$query = "SELECT id, last_name, first_name FROM user ORDER BY last_name";
		$result = mysql_db_query($database, $query, $connection) or die ("Error in query: $query. " . mysql_error());
		//////////////////Forbidden////////////////////
		while(list($id, $last_name, $first_name) = mysql_fetch_row($result))
		{
			$str = '<option value="' . $id . '"';
			// select current user's name
			
			if($id == $SESSION_UID) {$str .= ' selected';}
			$str .= '>'.$last_name.', '.$first_name.'</option>';
			echo $str;
		}
		mysql_free_result ($result);
?>
	</SELECT>
	<td><select tabindex="11" name="modify[]" multiple size="10">
<?php
	////////////////////Read//////////////////////////
		$query = "SELECT id, last_name, first_name FROM user ORDER BY last_name";
		$result = mysql_db_query($database, $query, $connection) or die ("Error in query: $query. " . mysql_error());
		//////////////////Forbidden////////////////////
		while(list($id, $last_name, $first_name) = mysql_fetch_row($result))
		{
			$str = '<option value="' . $id . '"';
			// select current user's name
			if($id == $SESSION_UID) {$str .= ' selected';}
			$str .= '>'.$last_name.', '.$first_name.'</option>';
			echo $str;
		}
		mysql_free_result ($result);
?>
	</SELECT>
	<td><select tabindex="12" name="admin[]" multiple size="10">
<?php
	////////////////////Read//////////////////////////
		$query = "SELECT id, last_name, first_name FROM user ORDER BY last_name";
		$result = mysql_db_query($database, $query, $connection) or die ("Error in query: $query. " . mysql_error());
		//////////////////Forbidden////////////////////
		while(list($id, $last_name, $first_name) = mysql_fetch_row($result))
		{
			$str = '<option value="' . $id . '"';
			// select current user's name
			if($id == $SESSION_UID) {$str .= ' selected';}
			$str .= '>'.$last_name.', '.$first_name.'</option>';
			echo $str;
		}
		mysql_free_result ($result);
?>	</SELECT>
	
	</TR>
	</TABLE>
	<tr>
	<td colspan="4" align="center"><input tabindex=7 type="Submit" name="submit" value="Add Document"></td>
	</tr>
<?php	
		$query = "SELECT name, id FROM department ORDER BY name";
		$result = mysql_db_query($database, $query, $connection) or die ("Error in query: $query. " . mysql_error());
		while( list($dept_name, $dept_id) = mysql_fetch_row($result) )
		{		
			if($dept_id == $current_user_dept)
				echo "\n\t".'<input type="hidden" name="'. space_to_underscore($dept_name).'" value="1"> '."\n";
			else
				echo "\n\t".'<input type="hidden" name="'.space_to_underscore($dept_name).'" value="0"> '."\n";
		}
		echo "\n\t".'<input type="hidden" name="default_Setting" value="0"> '."\n";
		mysql_free_result ($result);
		mysql_close($connection);
?>
	</form>
	</table>
	</center>
<?php
draw_footer();
}
else //submited form
{
	//get user's department
	$query ="SELECT user.department from user where user.id=$SESSION_UID";
	$result = mysql_db_query($database, $query, $connection) or die ("Error in query: $query. " . mysql_error());
	if(mysql_num_rows($result) != 1)
	{
		header('Location:error.php?ec=14');
		exit; //non-unique error
	}
	list($current_user_dept) = mysql_fetch_row($result);
	//can't upload empty file
	if ($file_size <= 0) 
	{ 
		header('Location:error.php?ec=11'); 
		exit; 
	}
	// check file type.  refer to config.php to see which file types are allowed
	foreach($allowedFileTypes as $this)
	{
		if ($file_type == $this) 
		{ 
		$allowedFile = 1;
		break; 
		} 
	}	
	// for non_allowed file types
	if ($allowedFile != 1) 
	{ 
		header('Location:error.php?ec=13'); 
		exit; 
	}
	// all checks completed, proceed!
	// INSERT file info into data table
	$query = "INSERT INTO data (category, owner, realname, created, description, department, comment, default_rights, publishable) VALUES('" . addslashes($category) . "', '" . addslashes($SESSION_UID) . "', '" . addslashes($file_name) . "', NOW(), '" . addslashes($description) . "','" . addslashes($current_user_dept) . "', '" . addslashes($comment) . "','" . addslashes($default_Setting) . "', 0)";
	$result = mysql_db_query($database, $query, $connection) or die ("Error in query: $query. " . mysql_error());
	
	// get id from INSERT operation 
	$fileId = mysql_insert_id($connection);
	
	//Find out the owners' username to add to log
	$query = "SELECT username from user where id='$SESSION_UID'";
	$result = mysql_db_query($database, $query, $connection) or die ("Error in query: $query. " . mysql_error());
	while ( list($username) = mysql_fetch_row($result) )
	{
		$lusername=$username;
	}
	
	// Add a log entry
	$query = "INSERT INTO log (id,modified_on, modified_by, note) VALUES ( '$fileId', NOW(), '" . addslashes($lusername) . "', 'Initial import')";
	$result = mysql_db_query($database, $query, $connection) or die ("Error in query: $query. " . mysql_error());
	

	//Insert Department Rights into dept_perms
	$query = "SELECT name, id FROM department ORDER BY name";
	$result = mysql_db_query($database, $query, $connection) or die("Error in query: $query. " . mysql_error() );
	while( list($dept_name, $id) = mysql_fetch_row($result) )
	{
	//echo "Dept is $dept_name";
		$query = "INSERT INTO dept_perms (fid, rights, dept_id) VALUES($fileId, " . addslashes(${ space_to_underscore($dept_name) }) . ", '$id')";		
		$result2 = mysql_db_query($database, $query, $connection) or die("Error in query: $query. " . mysql_error() );
	}
	// Search for simular names in the two array (merge the array.  repetitions are deleted)
	// In case of repetitions, higher priority ones stay.  
	// Priority is in this order (admin, modify, read, view)
	$filedata = new FileData($fileId, $connection, $database);	
	$result_array = advanceCombineArrays($admin, $filedata->ADMIN_RIGHT, $write, $filedata->WRITE_RIGHT);
	$result_array = advanceCombineArrays($result_array, 'NULL', $read, $filedata->READ_RIGHT);
	$result_array = advanceCombineArrays($result_array, 'NULL', $view, $filedata->VIEW_RIGHT);
	$result_array = advanceCombineArrays($result_array, 'NULL', $forbidden, $filedata->FORBIDDEN_RIGHT);
	// INSERT user permissions - view
	for($i = 0; $i<sizeof($result_array); $i++)
	{
		$query = "INSERT INTO user_perms (fid, uid, rights) VALUES('$fileId', '".$result_array[$i][0]."','". $result_array[$i][1]."')";
		$result = mysql_db_query($database, $query, $connection) or die("Error in query: $query" .mysql_error());;
	}

	// use id to generate a file name
	// save uploaded file with new name
	$newFileName = $fileId . '.dat';
	copy($file, $GLOBALS['CONFIG']['dataDir'] .'/' . $newFileName);
	if (!is_uploaded_file ($file))
	{
		header('Location: error.php?ec=18');
		exit;
	}
	// back to main page
	$message = urlencode('Document successfully added');
	header('Location: out.php?message=' . $message);
	mysql_close($connection);

}
?>
<SCRIPT LANGUAGE="JavaScript">

	var index = 0;
    var index2 = 0;
	var begin_Authority;
    var end_Authority;
    var frm_main = document.main;
    var dept_drop_box = frm_main.dept_drop_box;
    //Find init position of Authority
    while(frm_main.elements[index].name != "forbidden")
    {       index++;        }
	index2 = index;         //continue the search from index to avoid unnessary iteration
	// Now index contains the position of the view radio button
    //Next search for the position of the admin radio button
    while(frm_main.elements[index2].name != "admin")
    {       index2++;       }
    //Now index2 contains the position of the admin radio button
    //Set the size of the array
    begin_Authority = index;
    end_Authority = index2;
/////////////////////////////Defining event-handling functions///////////////////////////////////////////////////////
    var num_of_authorities = 4;
    function showData()
	{
		alert(frm_main.elements["Information_Systems"].value);
		alert(frm_main.elements["Test"].value);
		alert(frm_main.elements["Toxicology"].value);
	}
	function test()
	{
		alert(frm_main.elements["default_Setting"].value);
	}
	
	//loadData(_selectedIndex) load department data array
	//loadData(_selectedIndes) will only load data at index=_selectedIndex-1 of the array since
	//since _selectedIndex=0 is the "Please choose a department" option
	//when _selectedIndex=0, all radio button will be cleared.  No department[] will be set
	function loadDeptData(_selectedIndex)
    {
        if(_selectedIndex > 0)  //does not load data for option 0
        {
	        switch(departments[(_selectedIndex-1)].getRights())
	        {
	        	case -1: 
		        	frm_main.forbidden.checked = true;
					deselectOthers("forbidden");
					break;
				case 0:
		            frm_main.none.checked = true;
					deselectOthers("none");
					break;
				case 1:
		            frm_main.view.checked = true;
					deselectOthers("view");
		            break;
		        case 2:
					frm_main.read.checked = true;
					deselectOthers("read");
		        	break;
		        case 3:
					frm_main.write.checked = true;
		            deselectOthers("write");
		            break;
		        case 4:
					frm_main.admin.checked = true;
					deselectOthers("admin");
		           	break;				
				default: break;
			}
	    }
	    else
	    {
			index = begin_Authority;
	        while(index <= end_Authority)
	        {
				frm_main.elements[index++].checked = false;
	        }
	   	}
    }
	
	//Deselect other button except the button with the name stored in selected_rb_name
	//Design to control the rights radio buttons
	function deselectOthers(selected_rb_name)
    {
		var index = begin_Authority;
        while(index <= end_Authority)
        {
			if(frm_main.elements[index].name != selected_rb_name)
           	{
            	frm_main.elements[index].checked = false;
           	}
			index++;
        }
    }
	
	function spTo_(string)
	{
		return string.replace(" ", "_");
	}
	
	function setData(selected_rb_name)
    {
		var index = 0;
		var current_selected_dept =  dept_drop_box.selectedIndex - 1;
		var current_dept = departments[current_selected_dept];
		deselectOthers(selected_rb_name);
		//set right into departments
		departments[current_selected_dept].setRights(frm_main.elements[selected_rb_name].value); 
		//Since the All and Defualt department are abstractive departments, hidden fields do not exists for them.
		if(current_selected_dept-2 >= 0) // -1 from above and -2 now will set the first real field being 0
		{
			//set department data into hidden field
			frm_main.elements[spTo_( current_dept.getName() )].value = current_dept.getRights();		
		}
		departments[current_selected_dept].setFlag("true");
		if(  current_selected_dept == default_Setting_pos )  //for default user option
        {
        	frm_main.elements['default_Setting'].value = frm_main.elements[selected_rb_name].value
        	while (index< dept_drop_box.length)
            {
            	//do not need to set "All Department" and "Default Department"  they are only abstracts
				if(departments[index].issetFlag() == false && index != all_Setting_pos && index != default_Setting_pos)
                {
                	//set right radio buton's value into all Department that is available on the database
					departments[index].setRights(frm_main.elements[selected_rb_name].value); 
					//set right onto hidden valid hidden fields to communicate with php
					frm_main.elements[spTo_(departments[index].getName())].value = frm_main.elements[selected_rb_name].value;
				}
                index++;
            }
			index = 0;
        }
		if( current_selected_dept == all_Setting_pos) //for all user option. linked with predefine value above.
		{
			index = 0;
			while(index < dept_drop_box.length)
			{
				if(index != default_Setting_pos && index != all_Setting_pos) //Don't set default and All
				{
					//All setting acts like the user actually setting the right for all the department. -->setFlag=true
					departments[index].setFlag(true);
					//Set rights into department array
					departments[index].setRights(frm_main.elements[selected_rb_name].value );
					//Set rights into hidden fields for php
					frm_main.elements[spTo_(departments[index].getName())].value = frm_main.elements[selected_rb_name].value;
				}
				index++;
			}
		} 
	}

</SCRIPT>

