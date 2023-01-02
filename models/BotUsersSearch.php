<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\BotUsers;

/**
 * BotUserSearch represents the model behind the search form of `app\models\BotUsers`.
 */
class BotUsersSearch extends BotUsers
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'id_bot', 'is_subscribe', 'current_type', 'current_menu'], 'integer'],
            [['chat_id', 'phone', 'current_dialog', 'params', 'dt_coordinate', 'name', 'language', 'select_language', 'avatar', 'dt_subscribe', 'dt_last_action', 'dt_ban'], 'safe'],
            [['lat', 'lon'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = BotUsers::find();

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
            'id_bot' => $this->id_bot,
            'is_subscribe' => $this->is_subscribe,
            'current_type' => $this->current_type,
            'current_menu' => $this->current_menu,
            'lat' => $this->lat,
            'lon' => $this->lon,
            'dt_coordinate' => $this->dt_coordinate,
            'dt_subscribe' => $this->dt_subscribe,
            'dt_last_action' => $this->dt_last_action,
            'dt_ban' => $this->dt_ban,
        ]);

        $query->andFilterWhere(['like', 'chat_id', $this->chat_id])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'current_dialog', $this->current_dialog])
            ->andFilterWhere(['like', 'params', $this->params])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'language', $this->language])
            ->andFilterWhere(['like', 'select_language', $this->select_language])
            ->andFilterWhere(['like', 'avatar', $this->avatar]);

        return $dataProvider;
    }
}
