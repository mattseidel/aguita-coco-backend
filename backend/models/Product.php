<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "product".
 *
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property int $price
 *
 * @property Discount[] $discounts
 */
class Product extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'price'], 'required'],
            [['description'], 'string'],
            [['price'], 'integer'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'description' => 'Description',
            'price' => 'Price',
        ];
    }

    /**
     * Gets query for [[Discounts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDiscounts()
    {
        return $this->hasMany(Discount::className(), ['productId' => 'id']);
    }

    /**
     * Funcion para crear un producto
     * @param $req
     */
    public static function createProduct($title, $description, $price){
        $product = new Product();
        $product->title = $title;
        $product->description = $description;
        $product->price = $price;
        if($product->validate()){
            return $product;
        }
        return ['error' => $product->errors];
    }
}
