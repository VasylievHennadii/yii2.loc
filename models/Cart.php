<?php

namespace app\models;
use yii\db\ActiveRecord;

/**
 * Модель корзины 
 *
 * @author user
 */
class Cart extends ActiveRecord{
    
    public function addToCart($product, $qty = 1){
        echo 'Worked!';
    }

}
