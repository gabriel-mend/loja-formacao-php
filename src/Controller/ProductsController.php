<?php

namespace LojaApp\Controller;

use LojaApp\Model\Product;
use LojaApp\Database\Connection;
use LojaApp\MVC\View;


class ProductsController
{
    public function view($slug)
    {
        try {
            $product = (new Product(Connection::getInstance()))->find($slug);

            return View::render('single', compact('product'));
        }catch(\Exception $e){
            die($e->getMessage());
        }   
    }
}
