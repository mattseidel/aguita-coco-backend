<?php

namespace app\models;

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

    public const SCENARIO_CREATE = 'create_product';
    public const SCENARIO_UPDATE = 'update_product';


    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['create_product'] = ['title', 'description', 'price'];
        $scenarios['update_product'] = ['title', 'description', 'price'];
        return $scenarios;
    }

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
            ['description',  'default', 'value' => ''],
            [['price'], 'integer', 'min' => 1],
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
     * function to get products from database
     * @return array products
     */
    public static function getProducts()
    {
        return self::find()->all();
    }

    public static function createProduct($id)
    {
        $product = new Product();
        $product->scenario = Product::SCENARIO_UPDATE;
        $product->attributes = \Yii::$app->request->getBodyParams();
        if ($product->validate()) {
            $newProduct = self::findOne($id);
            $newProduct->attributes = $product->attributes;
            $newProduct->save();
            return ['message' => 'product saved successfully', 'code' => 200];
        }
        return ['message' => 'failed to save product', 'code' => 400, 'data' => $product->getErrors()];
    }
}
