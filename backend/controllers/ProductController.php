<?php

namespace backend\controllers;

use app\models\Product;
use yii\rest\ActiveController;

$enableCfrsValidation = false;
class ProductController extends ActiveController
{
    public $modelClass = 'app\models\Product';
    public function behaviors()
    {

        return [
            [
                'class' => \yii\filters\ContentNegotiator::className(),
                'formats' => [
                    'application/json' => \yii\web\Response::FORMAT_JSON,
                ],
            ],
        ];
    }


    public function actions()
    {
        $actions = parent::actions();
        unset($actions['delete'], $actions['update']);
        return $actions;
    }

    public function actionUpdate($id)
    {
        return Product::createProduct($id);
    }
}
