<?php

$routes = [
    '/'                    => ['ProdutoController', 'index'],
    '/produto/new'         => ['ProdutoController', 'create'],
    '/produto/store'       => ['ProdutoController', 'store'],
    '/produto/show'        => ['ProdutoController', 'show'],
    '/produto/edit'        => ['ProdutoController', 'edit'],
    '/produto/update'      => ['ProdutoController', 'update'],
    '/produto/add-to-cart' => ['ProdutoController', 'addToCart'],
];

return $routes;
