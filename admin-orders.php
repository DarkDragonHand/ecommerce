<?php

use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Order;
use \Hcode\Model\OrderStatus;

$app->get("/admin/orders/:idorder/status", function($idorder){

    User::verifyLogin();

    $order = new Order();

    $order->get((int) $inorder);

    $page = new PageAdmin();

    $page->setTpl("order", [
        'order'     => $order->getValues(),
        'status'    => OrderStatus::listAll(),
        'msgSuccess' => Order::getSuccess(),
        'msgError' => Order::getError()
    ]);

    header("Location: /admin/orders");
    exit;
});

$app->post("/admin/orders/:idorder/status", function($idorder){

    User::verifyLogin();

    if(!isset($_POST['idstatus']) || !(int)$_POST['idstatus'] > 0) {

        Order::setError("Informe o status atual do pedido.");

        header("Location: /admin/orders/".$idorder."/status");
        exit;

    }

    $order = new Order();

    $order->get((int) $inorder);

    $order->setidstatus((int) $_POST['idstatus']);

    $order->save();

    $order->setSuccess("Status atualizado.");

    header("Location: /admin/orders");
    exit;
});

$app->get("/admin/orders/:idorder/delete", function($idorder) {

    User::verifyLogin();

    $order = new Order();

    $order->get((int) $inorder);

    $order->delete();

    header("Location: /admin/orders");
    exit;

});

$app->get("/admin/orders/:idorder", function($idorder){

    User::verifyLogin();

    $order = new Order();

    $order->get((int) $idorder);

    $cart = $order->getCart();

    $page = new PageAdmin();

    $page->setTpl("order", [
        'order'    => $order->getValues(),
        'cart'     => $cart->getValues(),
        'products' => $cart->getProducts()
    ]);

});

$app->get("/admin/orders", function() {
    
    User::verifyLogin();

    $search = (isset($_GET['search'])) ? $_GET['search'] : "";
	$page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;

	if($search != '') {

		$pagination = Order::getPageSearch($search, $page, 10);//2 param é numero de usuarios por pagina

	} else {

		$pagination = Order::getPage($page, 10);//2 param é numero de usuarios por pagina

	}
	
	$pages = [];

	for ($i = 0; $i < $pagination['pages']; $i++) { 

		array_push($pages, [
				'href' => '/admin/orders?'.http_build_query([
				'page'   => $i + 1,
				'search' => $search
			]),
			'text' => $i + 1
		]);
	}

    $page = new PageAdmin();

    $page->setTpl("orders", [
        "orders" => $pagination['data'],
        "search" => $search,
        "pages"  => $pages
        

    ]);
});

?>