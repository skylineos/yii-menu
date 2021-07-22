<?php

namespace skylineos\yii\menu\models\search;

use yii\data\ActiveDataProvider;
use skylineos\yii\menu\models\Menu;

/**
 * MenuSearch represents the model behind the search form of `skylineos\yii\menu\models\Menu`.
 */
class MenuSearch extends Menu
{
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [
                [
                    'id',
                    'status',
                    'createdBy',
                    'modifiedBy'
                ],
                'integer'
            ],
            [
                [
                    'title',
                    'template',
                    'dateCreated',
                    'lastModified'
                ],
                'safe'
            ],
        ];
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
        $query = Menu::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['lastModified' => SORT_ASC]
            ],
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
            'status' => $this->status,
            'createdBy' => $this->createdBy,
            'modifiedBy' => $this->modifiedBy,
            'dateCreated' => $this->dateCreated,
            'lastModified' => $this->lastModified,
        ]);


        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'template', $this->template]);

        return $dataProvider;
    }
}
