<?php

use \Hcode\PageAdmin;
use \Hcode\Model\User;

$app->get('/admin', function() {
    
	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("index");

});

$app->get('/admin/login', function() {

	$page = new PageAdmin([
		"header" => false,
		"footer" => false
	]);

	$page->setTpl("login");

});

$app->post('/admin/login', function() {

	User::login($_POST["login"], $_POST["password"]);

	header("Location: /admin");
	exit;

});

$app->get('/admin/logout', function() {

	User::logout();

	header("Location: /admin/login");
	exit;
});

$app->get("/admin/users", function() {

	User::verifyLogin();

	$search = (isset($_GET['search'])) ? $_GET['search'] : "";
	$page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;

	if($search != '') {

		$pagination = User::getPageSearch($search, $page, 10);//2 param é numero de usuarios por pagina

	} else {

		$pagination = User::getPage($page, 10);//2 param é numero de usuarios por pagina

	}
	
	$pages = [];

	for ($i = 0; $i < $pagination['pages']; $i++) { 

		array_push($pages, [
			'href' => '/admin/users?'.http_build_query([
				'page'   => $i + 1,
				'search' => $search
			]),
			'text' => $i + 1
		]);
		
	}

	$page = new PageAdmin();

	$page->setTpl("users", array(
		"users"  => $pagination['data'],
		"search" => $search,
		"pages"  => $pages
	)); 

});

$app->get("/admin/users/create", function() {

	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("users-create"); 

});

$app->get("/admin/users/:iduser/delete", function($iduser) {

	User::verifyLogin();

	$users = new User();
	
	$user->get((int) $iduser);

	$user->delete();

	header("Location: /admin/users");

});

$app->get("/admin/users/:iduser", function($iduser) {

	User::verifyLogin();

	$user = new User();

	$user->get((int) $iduser); 

	$page = new PageAdmin();

	$page->setTpl("users-update", array(
		"user" => getValues()
	)); 

});

$app->post("/admin/users/:create", function() {

	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"]))? 1 : 0; //se foi definido 1 se não 0

	$user->setData($_POST);

	$user->save();

	header("Location: /admin/users");
	exit;

});

$app->post("/admin/users/:iduser", function($iduser) {

	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"]))? 1 : 0;

	$user->get((int)$iduser);

	$user->setData($_POST);

	$user->update();

	header("Location: /admin/users");
	exit;

});

?>