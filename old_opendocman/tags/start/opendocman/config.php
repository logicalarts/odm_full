<?php
// Eliminate multiple inclusion of config.php
if( !defined('config') )
{
  	define('config', 'true', false);

// config.php - useful variables/functions

// database parameters
include 'functions.php';
include 'classHeaders.php';
include 'mimetypes.php';

// Database Settings - Change these to match your database
$database = 'root';
$user = '';
$pass = '';
$hostname = '';


global $CONFIG;      $CONFIG = array(
'debug' => '22',

// This is useful if you have a web-based kerberos authenticatio site
// Set to either kerbauth or mysql
//'authen' => 'kerbauth',
'authen' => 'mysql',

// Set the number of files that show up on each page
'page_limit' => '10',

// Set the maximum displayable length of text field
'displayable_len' => '15',

// Set this to the url of the site
'base_url' => 'http://www.foo.com/opendocman',

// This is the browser window title
'title' => 'Document Repository',

// This is the program version for window title
'current_version' => ' OpenDocMan v1.0  ',

// The email address of the administrator of this site
'site_mail' => 'unixadmin@mydomainxaname.com',

//This variable sets the root username.  The root user will be able to access
//all files and have authority for everything.
'root_username'  => 'admin',

// location of file repository
// this should ideally be outside the Web server root
// make sure the server has permissions to read/write files!
'dataDir' => '/var/www/document_repository/'
);

//global $site_mail; 
global $hostname;
global $database;
global $user;
global $pass;

$connection = mysql_connect($hostname, $user, $pass) or die ("Unable to connect!");

// list of allowed file types
$allowedFileTypes = array('image/gif', 'text/html', 'text/plain', 'application/x-pdf', 'application/x-lyx', 'application/msword', 'image/jpeg', 'image/pjpeg', 'image/png', 'application/vnd.ms-excel', 'application/msaccess', 'text/richtxt', 'application/vnd.ms-powerpoint', 'application/octet-stream', 'application/x-zip-compressed');
// All functions are in functions.php
}
?>
