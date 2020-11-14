<?php

namespace app\controllers;
use app\models\Product;
use app\models\Cart;
use Yii;

/*Array два товара с id = 1 и id = 10, количество QTY и сумма SUM
(
    [1] => Array
    (
        [qty] => QTY
        [name] => NAME
        [price] => PRICE
        [img] => IMG
    )
    [10] => Array
    (
        [qty] => QTY
        [name] => NAME
        [price] => PRICE
        [img] => IMG
    )
)
    [qty] => QTY,
    [sum] => SUM
);*/



/**
 * Контроллер корзины
 *
 * @author user
 */
class CartController extends AppController{
   
    public function actionAdd(){
        $id = Yii::$app->request->get('id');
        $product = Product::findOne($id);
        if(empty($product)) return false;
        $session = Yii::$app->session;
        $session->open();
        $cart = new Cart();
        $cart->addToCart($product);
    }
    
}
