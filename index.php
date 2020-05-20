<?php 
session_start();
require_once("vendor/autoload.php");

//require_once("vendor/hcodebr/php-classes/src/DB/Sql.php");
//require_once("vendor/slim/slim/index.php");
//require_once("vendor/hcodebr/php-classes/src/Page.php");

use Hcode\Model\Category;
use \Slim\Slim;
use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\User;

$app = new Slim();

$app->config('debug', true);

require_once("functions.php");
require_once("site.php");
require_once("admin.php");
require_once("admin-user.php");
require_once("admin-categories.php");
require_once("admin-products.php");


/*
$app->get('/sql', function() {
    
	$sql = new Hcode\DB\Sql();

	$results = $sql->select("SELECT * FROM tb_users");

	echo json_encode($results); 
	
});
*/

$app->run();

?> 