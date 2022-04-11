<?php

use \Hcode\PageAdmin;
use \Hcode\Model\User;

$app->get("/admin/users/:iduser/password", function() {

	User::verifyLogin();

	$user = new User();

	$user->get((int) $iduser);

	$page = new PageAdmin();

	$page->setTpl("users-password", [
		"user" 	   	 => $user->getValues(),
		"msgError" 	 => User::getError(),
		"msgSuccess" =>	User::getSuccess()
	]);

});

$app->post("/admin/users/:iduser/password", function($iduser) {

	User::verifyLogin();

	if(!isset($_POST['despassword']) || $_POST['despassword'] === ''){

		User::setError("Preencha a nova senha.");

		header("Location: /admin/users/$iduser/password");
		exit;
	}

	if(!isset($_POST['despassword-confirm']) || $_POST['despassword-confirm'] === ''){

		User::setError("Preencha a confirmação da nova senha.");

		header("Location: /admin/users/$iduser/password");
		exit;
	}

	if($_POST['despassword'] !== $_POST['despassword-confirm']) {

		User::setError("Confirme corretamente as senhas.");

		header("Location: /admin/users/$iduser/password");
		exit;

	}

	$user = new User();

	$user->get((int) $iduser);

	$user->setPassword(User::getPasswordHash($_POST['despassword']));

	User::setSuccess("Senha alterada com sucesso!");

	header("Location: /admin/users/$iduser/password");
	exit;

});


$app->get("/admin/forgot", function() {

	$page = new PageAdmin([
		"header" => false,
		"footer" => false
	]);

	$page->setTpl("forgot");

});

$app->post("/admin/forgot", function() {

	$_POST["email"];

	$user = User::getForgot($_POST["email"]);

	header("Location: /admin/forgot/sent");
	exit;

});

$app->get("/admin/forgot/sent", function(){

	$page = new PageAdmin([
		"header" => false,
		"footer" => false
	]);

	$page->setTpl("forgot-sent");
	
});

$app->get("/admin/forgot/reset", function() {

	$user = User::validForgotDecrypt($_GET["code"]);

	$page = new PageAdmin([
		"header" => false,
		"footer" => false
	]);

	$page->setTpl("forgot-reset", array(
		"name" => $user["desperson"],
		"code" => $_GET["code"]
	));

});

$app->post("/admin/forgot/reset", function() {

	$forgot = User::validForgotDecrypt($_POST["code"]);

	User::setForgotUsed($forgot["idrecovery"]);

	$user = new User();

	$user->get((int)$forgot["iduser"]);

	$password = password_hash($_POST["password"], PASSWORD_DEFAULT, [
		"cost" => 12
	]);

	$user->setPassword($password);

	$page = new PageAdmin([
		"header" => false,
		"footer" => false
	]);

	$page->setTpl("forgot-reset-success");

    });
?>