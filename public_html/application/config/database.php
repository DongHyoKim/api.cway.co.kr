<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database type. ie: mysql.  Currently supported:
				 mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Active Record class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|				 NOTE: For MySQL and MySQLi databases, this setting is only used
| 				 as a backup if your server is running PHP < 5.2.3 or MySQL < 5.0.7
|				 (and in table creation queries made with DB Forge).
| 				 There is an incompatibility in PHP with mysql_real_escape_string() which
| 				 can make your site vulnerable to SQL injection if you are using a
| 				 multi-byte character set and are running versions lower than these.
| 				 Sites using Latin-1 or UTF-8 database character set and collation are unaffected.
|	['swap_pre'] A default table prefix that should be swapped with the dbprefix
|	['autoinit'] Whether or not to automatically initialize the database.
|	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
|							- good for ensuring strict SQL while developing
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the 'default' group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/


$active_group = 'default';
$active_record = TRUE;


/*
$db['default']['hostname'] = 'localhost';
$db['default']['username'] = '';
$db['default']['password'] = '';
$db['default']['database'] = '';
$db['default']['dbdriver'] = 'mysql';
$db['default']['dbprefix'] = '';
$db['default']['pconnect'] = TRUE;
$db['default']['db_debug'] = TRUE;
$db['default']['cache_on'] = FALSE;
$db['default']['cachedir'] = '';
$db['default']['char_set'] = 'utf8';
$db['default']['dbcollat'] = 'utf8_general_ci';
$db['default']['swap_pre'] = '';
$db['default']['autoinit'] = TRUE;
$db['default']['stricton'] = FALSE;
*/

$json_env_array = array();

// $_POST['Univcode']와 $receiveHeader['univcode']
$receiveHeader = apache_request_headers();
if(isset($receiveHeader['univcode'])){
    $_POST['Univcode'] = $receiveHeader['univcode'];
}

if(!isset($_POST['Univcode'])) {
   $json_env_array['status'] = -10;    
   $json_env_array['message'] = "대학교코드가 존재하지 않습니다.";
   echo json_encode($json_env_array);      
   exit;  
}

$DB_CONNECTION = array(
    "00106"=> array("hostname"=>"203.252.73.155,8433", "username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00106001") //KangWon =>연결됨
  , "00114"=> array("hostname"=>"203.255.23.244,8433", "username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00114001") //KyungSang
  , "00121"=> array("hostname"=>"203.250.126.175,8433","username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00121001") //BuKyung
  , "00117"=> array("hostname"=>"220.67.177.229,8433", "username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00117001") //SangJi
  , "00113"=> array("hostname"=>"219.255.132.117,8433","username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00113001") //Ewha =>연결됨
  , "00116"=> array("hostname"=>"220.71.99.157,8433",  "username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00116001") //JeonNam =>연결됨
  , "00103"=> array("hostname"=>"203.254.129.67,8433", "username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00103001") //JeonBuk
  , "00111"=> array("hostname"=>"203.253.209.174,8433","username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00111001") //Jeju
  , "00123"=> array("hostname"=>"168.188.72.211,8433", "username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00123001") //ChungNam
  , "00120"=> array("hostname"=>"210.115.160.44,8433", "username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00120001") //ChungBuk
  , "00112"=> array("hostname"=>"203.253.68.97,8433",  "username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00112001") //Hufs
  , "00100"=> array("hostname"=>"0010001.CWAY.KR,8433","username"=>"dmk","password"=>"!@dmk8191","database"=>"VENDINGM")    //CwayTest
);

if(! array_key_exists ($_POST['Univcode'] , $DB_CONNECTION) ){
   $json_env_array['status'] = -1;    
   $json_env_array['message'] = "존재하지 않는 대학교코드입니다.";
   echo json_encode($json_env_array);      
   exit;
}


$db['default']['hostname'] = $DB_CONNECTION[$_POST['Univcode']]['hostname'];
$db['default']['username'] = $DB_CONNECTION[$_POST['Univcode']]['username'];
$db['default']['password'] = $DB_CONNECTION[$_POST['Univcode']]['password'];
$db['default']['database'] = $DB_CONNECTION[$_POST['Univcode']]['database'];
$db['default']['dbdriver'] = 'sqlsrv';
$db['default']['dbprefix'] = '';
$db['default']['pconnect'] = FALSE;
$db['default']['db_debug'] = TRUE;
$db['default']['cache_on'] = FALSE;
$db['default']['cachedir'] = '';
$db['default']['char_set'] = 'utf8';
$db['default']['dbcollat'] = 'utf8_general_ci';
$db['default']['swap_pre'] = '';
$db['default']['autoinit'] = TRUE;
$db['default']['stricton'] = FALSE;

/* End of file database.php */
/* Location: ./application/config/database.php */