<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Request;

/**
 * RequestSearch represents the model behind the search form about `common\models\Request`.
 */
class RequestSearch extends Request
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user', 'origin', 'destination', 'travel_period_start', 'travel_period_end', 'status', 'email', 'mailing_processed', 'route_offset', 'rate_offset'], 'integer'],
            [['create_date', 'there_start_date', 'there_end_date', 'currency'], 'safe'],
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
        $query = Request::find()->where(['user' => Yii::$app->user->identity->getId()]);

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
            'user' => $this->user,
            'origin' => $this->origin,
            'destination' => $this->destination,
            'there_start_date' => $this->there_start_date,
            'there_end_date' => $this->there_end_date,
            'travel_period_start' => $this->travel_period_start,
            'travel_period_end' => $this->travel_period_end,
            'status' => $this->status,
            'email' => $this->email,
            'mailing_processed' => $this->mailing_processed,
            'route_offset' => $this->route_offset,
            'rate_offset' => $this->rate_offset,
        ]);

        $query->andFilterWhere(['like', 'currency', $this->currency]);

        return $dataProvider;
    }
}
