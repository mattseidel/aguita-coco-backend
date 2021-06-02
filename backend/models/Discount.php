<?php

namespace backend\models;

use backend\helpers\Helpers;
use Yii;

/**
 * This is the model class for table "discount".
 *
 * @property int $id
 * @property int|null $productId
 * @property int $value
 * @property string|null $start_date
 * @property string|null $end_date
 *
 * @property Product $product
 */
class Discount extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'discount';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['productId', 'value'], 'integer'],
            [['value'], 'in', 'range' => range(0, 100), 'message' => 'El valor del porcentaje no esta dentro del rango de 0 a 100'],
            [['start_date', 'end_date'], 'date', 'format' => 'yyyy-M-d', 'message' => 'El parametro {attribute} no es una fecha valida'],
            [['value'], 'validateDiscount'],
            ['end_date', 'compare', 'compareAttribute' => 'start_date', 'operator' => '>='],
            [['productId'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['productId' => 'id']],
        ];
    }

    /**
     * Validador personalizado para verificar fechas
     * @param $attribute
     * @param $params
     */
    public function validateDiscount($attribute, $params){
        if ($this->value == 0){//Si el descuento es 0 NO deberia tener fechas
            if (Helpers::validateDate($this->start_date) || Helpers::validateDate($this->end_date)){
                $this->addError($attribute, 'El descuento de 0 no puede tener fechas');
            }
        }else{// En caso contrario SI deberia tener fechas
            if (!Helpers::validateDate($this->start_date) || !Helpers::validateDate($this->end_date)){
                $this->addError($attribute, 'Debe administrar el rango de fechas válido para descuentos superiores a 0');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'productId' => 'Product ID',
            'value' => 'Value',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
        ];
    }

    /**
     * Gets query for [[Product]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'productId']);
    }

    public static function createDiscount($start_date, $end_date, $value){
        $discount = new Discount();
        $discount->start_date = $start_date;
        $discount->end_date = $end_date;
        $discount->value = $value;
        if($discount->validate()){
            return $discount;
        }
        return ['error' =>$discount->errors];
    }

    /**
     * Funcion para anadir la llave foranea al descuento
     * @param Discount $discount
     * @param $productId
     * @return Discount
     */
    public static function addIdKey(Discount $discount, $productId){
        $discount->productId = $productId;
        return $discount;
    }
}
