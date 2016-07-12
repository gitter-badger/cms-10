<?php
/**
 * Created by PhpStorm.
 * User: lf
 * Date: 16/4/1
 * Time: 23:29
 */
namespace backend\models;

use yii\data\ActiveDataProvider;

class AdminLogSearch extends AdminLog
{

    public $user_username;

    public function rules()
    {
        return [
            [['description'], 'string'],
            [['created_at', 'user_id'], 'integer'],
            [['route'], 'string', 'max' => 255],
            ['user_username', 'safe']
        ];
    }

    public function search($params)
    {
        $query = self::find()->orderBy("id desc");
        $query->joinWith(['user']);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->setSort([
            'attributes' => [
                /* 其它字段不要动 */
                /*  下面这段是加入的 */
                /*=============*/
                'user_username' => [
                    'asc' => ['admin_user.id' => SORT_ASC],
                    'desc' => ['admin_user.id' => SORT_DESC],
                ],
                'id' => [
                    'asc' => ['admin_log.id' => SORT_ASC],
                    'desc' => ['admin_log.id' => SORT_DESC],
                ],
                'created_at' => [
                    'asc' => ['created_at' => SORT_ASC],
                    'desc' => ['created_at' => SORT_DESC],
                ],
                'route' => [
                    'asc' => ['route' => SORT_ASC],
                    'desc' => ['route' => SORT_DESC],
                ],
                'description' => [
                    'asc' => ['description' => SORT_ASC],
                    'desc' => ['description' => SORT_DESC],
                ],
                /*=============*/
            ]
        ]);
        $this->load($params);
        if(!$this->validate()){
            return $dataProvider;
        }
        $query->andFilterWhere(['id'=>$this->id])
            ->andFilterWhere(['like', 'route', $this->route])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'admin_user.username', $this->user_username]) ;
        return $dataProvider;
    }


}