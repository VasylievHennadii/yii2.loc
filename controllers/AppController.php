<?php

namespace app\controllers;

use yii\web\Controller;

/**
 * Общий контроллер проекта для общей логики
 * Наследует класс Controller
 *
 * @author user
 */
class AppController extends Controller {
    
    /**
    * метод для определения метатегов    
    */
    protected function  setMeta($title = null, $keywords = null, $description = null){
        $this->view->title = $title;
        $this->view->registerMetaTag(['name' => 'keywords', 'content' => "$keywords"]);
        $this->view->registerMetaTag(['name' => 'description', 'content' => "$description"]);        
    }
    
}
