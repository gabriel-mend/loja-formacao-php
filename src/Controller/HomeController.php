<?php
namespace LojaApp\Controller;

use LojaApp\MVC\View;
use LojaApp\Model\Product;
use LojaApp\Database\Connection;

class HomeController
{
    public function index()
    {
        try {
            $products = (new Product(Connection::getInstance()))->findAll();
            return View::render('index', compact('products'));
        }catch(\Exception $e){
            die($e->getMessage());
        }
        
        return View::render('index', compact('products'));
    }
}