<?php

namespace backend\models;

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
            [['value'], 'required'],
            [['start_date', 'end_date'], 'date', 'format' => 'yyyy-M-d', 'message' => 'El parametro {attribute} no es una fecha valida'],
            ['end_date', 'compare', 'compareAttribute' => 'start_date', 'operator' => '>'],
            [['productId'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['productId' => 'id']],
        ];
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
