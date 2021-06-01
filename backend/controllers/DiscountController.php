<?php

namespace backend\controllers;

use backend\models\Discount;
use backend\models\Product;
use yii\filters\Cors;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;

class DiscountController extends ActiveController
{

    public $modelClass = 'backend\models\discount';

    /**
     * Funcion para evitar el CORS
     * @return array|array[]
     */
    public function behaviors()
    {
        return ArrayHelper::merge([
            [
                'class' => Cors::className(),
            ],
        ], parent::behaviors());
    }

    /**
     * Funcion para eliminar acciones de la api rest
     * @return array
     */
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        return $actions;
    }

    /**
     * Funcion para traer los descuentos ordenados por fecha del mas reciente al mas antiguo
     * @return array|Discount[]|\yii\db\ActiveRecord[]
     */
    public function actionIndex(){
        //Traer todos los descuentos
        $discounts = Discount::find()
            ->orderBy(['start_date' => SORT_DESC, 'end_date' => SORT_DESC])
            ->all();

        //Por cada descuento asignar su producto
        $result = array();
        foreach ($discounts as $discount){
            $object = new \stdClass();
            $object->product = $discount;
            $object->discount= $discount->getProduct()->all();
            array_push($result, $object);
        }

        return $result;
    }

    /**
     * Accion para agregar un descuento a un producto sin descuento
     * @return string|\yii\web\Response
     */
    public function actionAddDiscount(){

        $req = $this->request->getBodyParams();

        //Validacion de parametros del descuento
        if(!isset($req['productId']) || !isset($req['value']) || !isset($req['start_date']) || !isset($req['end_date'])){
            return $this->asJson([
                'ok' => false,
                'msg' => 'Faltan alguno de estos parametros en el producto (productId, value, start_date, end_date)'
            ]);
        }

        $product = Product::findOne($req['productId']);

        //Si no existe el producto
        if(!$product){
            return $this->asJson([
                'ok' => false,
                'msg' => 'No existe un producto con este id'
            ]);
        }

        //Creacion del descuento del producto
        $discount = Discount::createDiscount($req['start_date'], $req['end_date'], $req['value']);
        if(isset($discount['error'])){
            return $this->asJson([
                'ok' => false,
                'msg' => $discount['error']
            ])->statusCode(400);
        }

        //Crear la relacion y guardar
        $discount = Discount::addIdKey($discount, $req['productId']);
        $discount->save();

        return $this->asJson([
            'ok' => true,
            'msg' => 'El descuento se guardo exitosamente',
            'discount' => $discount
        ]);
    }

}
