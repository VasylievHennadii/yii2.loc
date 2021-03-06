<?php

namespace app\controllers;
use app\models\Category;
use app\models\Product;
use Yii;
use yii\web\HttpException;

/**
 * Description of ProductController
 *
 * @author user
 */
class ProductController extends AppController{
    
    public function actionView($id){
//        $id = Yii::$app->request->get('id');
        $product = Product::findOne($id); //ленивая загрузка
        if(empty($product))
            throw new HttpException(404, 'Такого товара нет.');
//        $product = Product::find()->with('category')->where(['id' => $id])->limit(1)->one(); //жадная загрузка
        $hits = Product::find()->where(['hit' => '1'])->limit(6)->all();
        $this->setMeta('E-SHOPPER | ' . $product->name, $product->keywords, $product->description);
        return $this->render('view', compact('product', 'hits'));        
    }
    
}
