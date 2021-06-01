<?php

namespace backend\controllers;

$enableCfrsValidation = false;

use backend\models\Discount;
use backend\models\Product;
use yii\filters\Cors;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;

class ProductController extends ActiveController
{

    public $modelClass = 'backend\models\product';



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
        unset($actions['delete']);
        return $actions;
    }

    public function actionAllProducts()
    {
        // Todos los productos con ofertas vigentes
        $products = \Yii::$app->db->createCommand('CALL traerOfertasVigentes();')->queryAll();

        return $this->asJson([
            'ok' => true,
            'products' => $products
        ]);
    }

    public function actioUpdate($id)
    {
    }

    /**
     * Funcion para crear un producto con su respectivo descuento
     * @return \yii\web\Response Retorna un objeto en notacion JSON segun las peticiones
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreateWithDiscount()
    {
        $req = $this->request->getBodyParams();

        //Validacion de parametros del producto
        if (!isset($req['title']) || !isset($req['description']) || !isset($req['price'])) {
            return $this->asJson([
                'ok' => false,
                'msg' => 'Faltan alguno de estos parametros en el producto (title, description, price)'
            ])->setStatusCode(400);
        }

        //Validacion de parametros del descuento del producto
        if (!isset($req['value']) || !isset($req['start_date']) || !isset($req['end_date'])) {
            return $this->asJson([
                'ok' => false,
                'msg' => 'Faltan alguno de estos parametros en el descuento del producto (value, start_date, end_date)'
            ])->setStatusCode(400);
        }

        //Creacion del producto
        $product = Product::createProduct($req['title'], $req['description'], $req['price']);

        //Creacion del descuento
        $discount = Discount::createDiscount($req['start_date'], $req['end_date'], $req['value']);

        //Validar que no hayan errores
        if (!isset($product['error']) && !isset($discount['error'])) {
            $product->save(); //Guardar producto en la base de datos
            $discount = Discount::addIdKey($discount, $product->id); //Anadir la llave de relacion
            $discount->save(); //Guardar descuento en la base de datos
            $product->discounts = $discount;
            return $this->asJson([
                'ok' => true,
                'msg' => 'Producto guardado exitosamente!',
                'product' => $product
            ])->setStatusCode(200);
        }

        //Si llega a esta parte es porque hay errores
        $errors = [];

        if (isset($discount['error'])) {
            array_push($errors, $discount['error']);
        }

        if (isset($product['error'])) {
            array_push($errors, $product['error']);
        }

        return $this->asJson([
            'ok' => false,
            'error' => $errors
        ])->setStatusCode(400);
    }
    public function actionUpdate($id)
    {
        return Product::createProduct($id);
    }

    public function actionRecentToOld()
    {
        // Todos los productos vigentes del mas reciente al mas antiguo
        $products = \Yii::$app->db->createCommand('CALL traerRecienteAntiguo();')->queryAll();

        return $this->asJson([
            'ok' => true,
            'products' => $products
        ]);
    }

    public function actionOldToRecent()
    {
        // Todos los productos vigentes del mas antiguo al mas reciente
        $products = \Yii::$app->db->createCommand('CALL traerAntiguoReciente();')->queryAll();

        return $this->asJson([
            'ok' => true,
            'products' => $products
        ]);
    }
}
