<?php
/*
config.php - OpenDocMan main config file
Copyright (C) 2002-2004 Stephen Lawrence Jr., Khoa Nguyen
Copyright (C) 2005-2010 Stephen Lawrence Jr.

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/

// Eliminate multiple inclusion of config.php
if( !defined('config') )
{
    define('config', 'true', false);

// config.php - useful variables/functions

// Database Settings
$GLOBALS['database'] = 'opendocman'; // Enter the name of the database here
$GLOBALS['user'] = 'opendocman'; // Enter the username for the database
$GLOBALS['pass'] = 'opendocman'; // Enter the password for the username
$GLOBALS['hostname'] = 'localhost'; // Enter the hostname that is serving the database

global $CONFIG;

/**
 * Prefix to append to each table name in the database (ex. odm_ would make the tables
 * named "odm_users", "odm_data" etc. Leave this set to the default if you want to keep
 * it the way it was. If you do change this to a different value, make sure it is either
 * a clean-install, or you manually go through and re-name the database tables to match.
 * @DEFAULT 'odm_'
 * @ARG String
 */
$CONFIG['db_prefix'] = 'odm_';

$CONFIG['debug'] = '0';

// This setting is for a demo installation, where random people will be
// all loggging in as the same username/password like 'demo/demo'. This will
// keep users from removing files, users, etc.
$CONFIG['demo'] = 'false';

// Currently only MySQL authentication is supported
$CONFIG['authen'] = 'mysql';

// Set the number of files that show up on each page
$CONFIG['page_limit'] = '15';

// Set the number of page links that show up on each page
$CONFIG['num_page_limit'] = '10';

// Set the maximum displayable length of text field
$CONFIG['displayable_len'] = '15';

// Set this to the url of the site
// No need for trailing "/" here
$CONFIG['base_url'] = 'http://localhost/opendocman';

// This is the browser window title
$CONFIG['title'] = 'Document Repository';

// The email address of the administrator of this site
$CONFIG['site_mail'] = 'root@localhost';

//This variable sets the root username.  The root user will be able to access
//all files and have authority for everything.
$CONFIG['root_username']  = 'admin';

// location of file repository
// This should ideally be outside the Web server root.
// Make sure the server has permissions to read/write files to this folder!

// Uncomment this one for WINDOWS - Don't forget the trailing slash "/"
//$CONFIG['dataDir'] = 'c:/document_repository/';

// Uncomment this one for LINUX - Don't forget the trailing slash "/"
$CONFIG['dataDir'] = '/var/www/document_repository/';

// Set the maximum file upload size
$CONFIG['max_filesize'] = '5000000';

//This var sets the amount of days until each file needs to be revised,
//assuming that there are 30 days in a month for all months.
$CONFIG['revision_expiration'] = '90';

/* Choose an action option when a file is found to be expired
The first two options also result in sending email to reviewer
 	(1) Remove from file list until renewed
	(2) Show in file list but non-checkoutable
	(3) Send email to reviewer only
	(4) Do Nothing
*/
$CONFIG['file_expired_action'] = '1';

//Authorization control: On or Off (case sensitive)
//If set On, every document added or checked back must be reviewed by an admin
//before it can go public.  To disable this review queue, set this variable to Off.
//When set to Off, all newly added or checked back in documents will immediately go public
$CONFIG['authorization'] = 'On';

//Secure URL control: On or Off (case sensitive)
//When set to 'On', all urls will be secured
//When set to 'Off', all urls are normal and readable
$CONFIG['secureurl'] = 'On';

// should we display document listings in the normal way or in a tree view
// this must be 'On' to change the display
$CONFIG['treeview'] = 'Off';

// should we display the signup link?
$CONFIG['allow_signup'] = 'On';

// should we allow users to reset their forgotten password?
$CONFIG['allow_password_reset'] = 'Off';

// Attempt NIS password lookups?
$CONFIG['try_nis'] = 'Off';

// Which theme to use?
$CONFIG['theme'] = 'default';

// Set the default language (english, spanish, turkish, etc.).
// Local users may override this setting
// check include/language folder for languages available
$CONFIG['language'] = 'english';

// List of allowed file types
// Pay attention to the "Last Message:" in the status bar if your file is being rejected
// because of its file type. It should display the proper MIME type there, and you can
// then add that string to this list to allow it
$GLOBALS['allowedFileTypes'] = array('image/gif', 'text/html', 'text/plain', 'application/pdf', 'application/x-pdf', 'application/x-lyx', 'application/msword', 'image/jpeg', 'image/pjpeg', 'image/png', 'application/msexcel', 'application/msaccess', 'text/richtxt', 'application/mspowerpoint', 'application/octet-stream', 'application/x-zip-compressed','image/tiff','image/tif','application/vnd.ms-powerpoint','application/vnd.ms-excel','applicatiion/vnd.openxmlformats-officedocument.presenationml.presentation','application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

// <----- No need to edit below here ---->
//
// Encourage end-users to put local configuration in config_local.php, so
// we can overwrite (config.php) in the future
// without danger of overwriting site specific information.
if (is_file('config_local.php'))
{
    include('config_local.php');
}
elseif (is_file('../config_local.php'))
{
    include('../config_local.php');
}
elseif (is_file('../../config_local.php'))
{
    include('../../config_local.php');
}


// Set the revision directory. (relative to $dataDir)
$CONFIG['revisionDir'] = $GLOBALS['CONFIG']['dataDir'] . 'revisionDir/';

// Set the revision directory. (relative to $dataDir)
$CONFIG['archiveDir'] = $GLOBALS['CONFIG']['dataDir'] . 'archiveDir/';

$GLOBALS['connection'] = mysql_connect($GLOBALS['hostname'], $GLOBALS['user'], $GLOBALS['pass']) or die ("Unable to connect: " . mysql_error());
$db = mysql_select_db($GLOBALS['database'], $GLOBALS['connection']);

// All functions and includes are in functions.php
include_once('functions.php');

$_GET = sanitizeme($_GET);
$_REQUEST = sanitizeme($_REQUEST);
$_POST = sanitizeme($_POST);
$_SERVER = sanitizeme($_SERVER);
}