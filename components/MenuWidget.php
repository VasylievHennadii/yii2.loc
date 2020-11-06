<?php

namespace app\components;

use yii\base\Widget;
use app\models\Category;
use Yii;

class MenuWidget extends Widget {

    public $tpl;
    public $data; //хранятся все записи(массив) категорий из БД
    public $tree; //результат построения из обычного массива в массив-дерево
    public $menuHtml; //хранится готовый Html code, шаблон которого хранится в tpl     

    public function init(){
        parent::init();
        if( $this->tpl === null ){
            $this->tpl = 'menu'; 
        }
        $this->tpl .= '.php';
    }

    public function run(){
        // get cache
        $menu = Yii::$app->cache->get('menu');
        if($menu) return $menu;
        
        $this->data = Category::find()->indexBy('id')->asArray()->all();
        $this->tree = $this->getTree();
        $this->menuHtml = $this->getMenuHtml($this->tree); 
        //set cache
        Yii::$app->cache->set('menu', $this->menuHtml, 60);
        return $this->menuHtml;
    }
    
    /**
     * метод проходит по массиву и строит дерево
     */
    protected function getTree(){
        $tree = [];
        foreach ($this->data as $id=>&$node){
            if(!$node['parent_id'])
                $tree[$id] = &$node;
            else 
                $this->data[$node['parent_id']]['childs'][$node['id']] = &$node;
            
        }
        return $tree;
    }
    
    /**
     * метод принимает параметром дерево, проходит в цикле и передает параметром в catToTemplate() каждый 
     * элемент данного дерева. Возвращает нужный Html код     
     */
    protected function getMenuHtml($tree){
        $str = '';
        foreach ($tree as $category) {
            $str .= $this->catToTemplate($category);
        }
        return $str;
    }

    /**
     * метод принимает параметром каждый элемент данного дерева и помещает его в шаблон (menu||select)
     */
    protected function catToTemplate($category){
        ob_start();
        include __DIR__ . '/menu_tpl/' . $this->tpl;
        return ob_get_clean();
    }

}