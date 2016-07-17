<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Rate;

/**
 * RateSearch represents the model behind the search form about `common\models\Rate`.
 */
class RateSearch extends Rate
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'route', 'airline'], 'integer'],
            [['create_date', 'origin_city', 'destination_city', 'service', 'there_date', 'back_date', 'flight_number', 'currency'], 'safe'],
            [['price'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Rate::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'create_date' => $this->create_date,
            'route' => $this->route,
            'there_date' => $this->there_date,
            'back_date' => $this->back_date,
            'airline' => $this->airline,
            'price' => $this->price,
        ]);

        $query->andFilterWhere(['like', 'origin_city', $this->origin_city])
            ->andFilterWhere(['like', 'destination_city', $this->destination_city])
            ->andFilterWhere(['like', 'service', $this->service])
            ->andFilterWhere(['like', 'flight_number', $this->flight_number])
            ->andFilterWhere(['like', 'currency', $this->currency]);

        return $dataProvider;
    }
}
