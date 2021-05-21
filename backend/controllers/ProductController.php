<?php

namespace backend\controllers;

$enableCfrsValidation = false;

use backend\models\Discount;
use backend\models\Product;
use yii\rest\ActiveController;

class ProductController extends ActiveController
{

    public $modelClass = 'backend\models\product';

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
            ]);
        }

        //Validacion de parametros del descuento del producto
        if (!isset($req['value']) || !isset($req['start_date']) || !isset($req['end_date'])) {
            return $this->asJson([
                'ok' => false,
                'msg' => 'Faltan alguno de estos parametros en el descuento del producto (value, start_date, end_date)'
            ]);
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
            return $this->asJson([
                'ok' => true,
                'msg' => 'Producto guardado con su descuento correspondiente exitosamente!',
                'product' => $product,
                'discount' => $discount
            ]);
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
        ]);
    }
    public function actionUpdate($id)
    {
        return Product::createProduct($id);
    }
}
