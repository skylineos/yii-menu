<?php

namespace skylineos\yii\menu\models\search;

use skylineos\yii\menu\models\MenuItem;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * MenuItemSearch represents the model behind the search form of `skyline\yii\cms\models\MenuItem`.
 */
class MenuItemSearch extends MenuItem
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
                    'sortOrder',
                    'parentItemId',
                    'createdBy',
                    'modifiedBy',
                    'menuId'
                ],
                'integer'
            ],
            [
                [
                    'title',
                    'linkTo',
                    'linkTarget',
                    'dateCreated',
                    'lastModified',
                    'template',
                ],
                'safe'
            ],
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
     * @param array|null $params
     * @param bool $topLevelOnly whether or not to only load top level items
     * @param int|null $menuId if provided, only return items in the given menuId
     * @param int|null the parentItemId, if provided, for which we want to find sub items
     *
     * @return ActiveDataProvider
     */
    public function search(?array $params, bool $topLevelOnly = false, ?int $menuId = null, ?int $oarentItemId = null)
    {
        $query = MenuItem::find()->orderBy('sortOrder ASC');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
        ]);

        $this->load($params);

        if ($topLevelOnly === true) {
            $query->andWhere(['parentItemId' => null]);
        }

        if ($menuId !== null) {
            $query->andWhere(['menuId' => $menuId]);
        }

        if ($parentItemId !== null) {
            $query->andWhere(['parentItemId' => $parentItemId]);
        }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'sortOrder' => $this->sortOrder,
            'parentItemId' => $this->parentItemId,
            'createdBy' => $this->createdBy,
            'modifiedBy' => $this->modifiedBy,
            'dateCreated' => $this->dateCreated,
            'lastModified' => $this->lastModified,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'template', $this->template])
            ->andFilterWhere(['like', 'linkTo', $this->linkTo])
            ->andFilterWhere(['like', 'linkTarget', $this->linkTarget]);

        return $dataProvider;
    }
}
