<?php

namespace app\controllers;
use app\models\Product;
use app\models\Cart;
use app\models\Order;
use app\models\OrderItems;
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
        $qty = (int)Yii::$app->request->get('qty');
        $qty = !$qty ? 1 : $qty;
        $product = Product::findOne($id);
        if(empty($product)) return false;
        $session = Yii::$app->session;
        $session->open();
        $cart = new Cart();
        $cart->addToCart($product, $qty);
        if(!Yii::$app->request->isAjax){
            return $this->redirect(\Yii::$app->request->referrer);
        }
        $this->layout = false;
        return $this->render('cart-modal', compact('session'));
    }
    
    /**
     * метод очистки корзины. Удаляем все товары по ключу 'cart', отдельно 'qty' и отдельно 'sum'
     */
    public function actionClear(){
        $session = Yii::$app->session;
        $session->open();
        $session->remove('cart');
        $session->remove('cart.qty');
        $session->remove('cart.sum');
        $this->layout = false;
        return $this->render('cart-modal', compact('session'));
    }
    
    /**
     * метод удаления одного наименования товара из корзины
     */
    public function actionDelItem(){
        $id = Yii::$app->request->get('id');
        $session = Yii::$app->session;
        $session->open();
        $cart = new Cart();
        $cart->recalc($id);
        $this->layout = false;
        return $this->render('cart-modal', compact('session'));
    }

    /**
     * метод вывода корзины по кнопке из хедера 
     */
    public function actionShow(){
        $session = Yii::$app->session;
        $session->open();        
        $this->layout = false;
        return $this->render('cart-modal', compact('session'));
    }
    
    public function actionView(){
        $session = Yii::$app->session;
        $session->open();
        $this->setMeta('Корзина');
        $order = new Order();
        if($order->load(Yii::$app->request->post())){
            $order->qty = $session['cart.qty'];
            $order->sum = $session['cart.sum'];
            if($order->save()){
                $this->saveOrderItems($session['cart'], $order->id);
                Yii::$app->session->setFlash('success', 'Ваш заказ принят. Ожидайте звонка менеджера.');
                Yii::$app->mailer->compose('order', ['session' => $session])
                        ->setFrom(['petpolimer@ukr.net' => 'wigwam.com'])
                        ->setTo($order->email)
                        ->setSubject('Заказ')
                        ->send();
                Yii::$app->mailer->compose('order', ['session' => $session])
                        ->setFrom(['petpolimer@ukr.net' => 'wigwam.com'])
                        ->setTo('petpolimer@gmail.com')
                        ->setSubject('Заказ')
                        ->send();
                Yii::$app->mailer->compose('order', ['session' => $session])
                        ->setFrom(['petpolimer@ukr.net' => 'wigwam.com'])
                        ->setTo(Yii::$app->params['adminEmail'])
                        ->setSubject('Заказ')
                        ->send();
                $session->remove('cart');
                $session->remove('cart.qty');
                $session->remove('cart.sum');
                return $this->refresh();
            }else{
               Yii::$app->session->setFlash('error', 'Ошибка оформления заказа.'); 
            }
        }
        return $this->render('view', compact('session', 'order'));
    }

    /**
     * метод получает id сохраненной записи
     */
    protected function saveOrderItems($items, $order_id){
        foreach ($items as $id => $item){
            $order_items = new OrderItems();
            $order_items->order_id = $order_id;            
            $order_items->product_id = $id;            
            $order_items->name = $item['name'];            
            $order_items->price = $item['price'];            
            $order_items->qty_item = $item['qty'];            
            $order_items->sum_item = $item['qty'] * $item['price'];  
            $order_items->save();
        }
    }
    
}
