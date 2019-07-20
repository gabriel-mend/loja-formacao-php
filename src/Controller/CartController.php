<?php
namespace LojaApp\Controller;

use LojaApp\Http\Session;
use LojaApp\Http\Flash;
use LojaApp\MVC\View;

use PHPSC\PagSeguro\Credentials;
use PHPSC\PagSeguro\Environments\Production;
use PHPSC\PagSeguro\Environments\Sandbox;
use PHPSC\PagSeguro\Customer\Customer;
use PHPSC\PagSeguro\Items\Item;
use PHPSC\PagSeguro\Requests\Checkout\CheckoutService;

class CartController
{
    public function add()
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $product = $_POST;

            $items = Session::get('cart');

            if(!is_null($items)) {
                array_push($items, $product);
            } else {
                $items[] = $product;
            }
            
            Session::add('cart', $items); 
            
            Flash::add('success', 'Produto adicionado no carrinho!');

            return header('Location: ' . HOME 
            . '/products/view/' . $product['slug']);
        }
    }

    public function index()
    {
        $cart = Session::get('cart');

        return View::render('cart', compact('cart'));
    }

    public function remove($item)
    {
        $cart = Session::get('cart');

        if(is_null($cart)) return header('Location: ' . HOME . '/cart');

        $cart = array_filter($cart, function ($line) use($item){
            return $line['slug'] != $item;
        });     

        $cart = count($cart) ? $cart : null;

        Session::add('cart', $cart);
        
        return header('Location: ' . HOME . '/cart');
    }

    public function checkout()
    {
        if(!Session::has('user')) {
            return header('Location: ' . HOME . '/user');
        }

        $cart = Session::get('cart');

        $enviroment = PAGSEGURO_SANDBOX ? new Sandbox() : new Production();
        
        $credentials = new Credentials(
            PAGSEGURO_EMAIL, 
            PAGSEGURO_TOKEN,
            $enviroment);

        $service = new CheckoutService(
            $credentials
        );

        $order = [
            'user_id' => Session::get('user')->id,
            'items'   => serialize($cart),
            'pagseguro_status' => 0,
            'pagseguro_code'   => 0,
        ];
        $userOrder = (new UserOrder(Connection::getInstance()))
                    ->createOrder($order);
    
        $checkout = $service->createCheckoutBuilder();

        foreach($cart as $key => $c) {           
           $checkout->addItem(new Item($userOrder, $c['name'], $c['price'], $c['qtd']));
        }
    
        $response = $service->checkout($checkout->getCheckout());

        Session::remove('cart');
        Session::remove('user');

        return header('Location: ' . $response->getRedirectionUrl());
        }
    }
