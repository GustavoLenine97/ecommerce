<?php

use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\User;

$app->get('/admin', function() {
    /*
	$sql = new Hcode\DB\Sql();

	$results = $sql->select("SELECT * FROM tb_users");

	echo json_encode($results); 
	*/

	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("index");
});

$app->get('/admin/teste', function() {
    /*
	$sql = new Hcode\DB\Sql();

	$results = $sql->select("SELECT * FROM tb_users");

	echo json_encode($results); 
	*/

	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("teste");
});

$app->get('/admin/login', function() {
    /*
	$sql = new Hcode\DB\Sql();

	$results = $sql->select("SELECT * FROM tb_users");

	echo json_encode($results); 
	*/

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("login");

});

$app->post('/admin/login',function(){
	
	User::login($_POST["login"], $_POST["password"]);

	header("Location: /ecommerce/admin");
	exit;

});

/*
$app->get('/teste',function(){
	User::teste('Gustavo');
});
*/

$app->get('/admin/logout',function(){
	
	User::logout();

	header("Location: /ecommerce/admin/login");

});

?>