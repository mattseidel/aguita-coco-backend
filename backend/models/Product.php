<?php

namespace backend\models;

use Yii;
use yii\base\Arrayable;

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
class Product extends \yii\db\ActiveRecord implements Arrayable
{

    public $discounts;

    public function fields()
    {
        return [
            'id' => 'id',
            'title' => 'title',
            'description' => 'description',
            'price' => 'price',
            'discounts' => 'discounts',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product';
    }

    const SCENARIO_CREATE = 'create_scenario';

    public function scenarios()
    {
        $scenario = parent::scenarios();
        $scenario['create_scenario'] = ['title', 'description', 'price'];
        return $scenario;
    }

    public function updateProduct($id)
    {
        $product = Product::find($id)->one();
        $product->scenario = Product::SCENARIO_CREATE;
        $product->attributes = \Yii::$app->request->getBodyParam;

        if ($product->validate()) {
            $product->save();
            return ['message' => 'product created successfully', 'code' => 200];
        } else
            return ['message' => 'error creating product', 'code' => 400, 'data' => $product->getErrors()];
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'price'], 'required'],
            [['description'], 'string'],
            [['description'], 'default', 'value' => ''],
            [['price'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['discount'], 'safe'],
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
    public static function createProduct($title, $description, $price)
    {
        $product = new Product();
        $product->title = $title;
        $product->description = $description;
        $product->price = $price;
        if ($product->validate()) {
            return $product;
        }
        return ['error' => $product->errors];
    }
}
