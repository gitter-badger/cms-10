<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;

/**
 * Base controller
 */
class BaseController extends Controller
{
    public function beforeAction($action)
    {
        $headers = Yii::$app->response->headers;
        $headers->add('X-Powered-By', 'feehi');
        if(!yii::$app->user->isGuest){
            if( yii::$app->rbac->checkPermission() === false ){
                Yii::$app->response->redirect(['error/forbidden'], 200)->send();
                exit();
            }
        }
        if(yii::$app->user->isGuest && Yii::$app->controller->id.'/'.Yii::$app->controller->action->id != 'site/login') yii::$app->controller->redirect(['site/login']);
        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }

    public function actionDelete($id)
    {
        if(yii::$app->request->isAjax){
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $ids = explode(',', $id);
            $errorIds = [];
            foreach ($ids as $one){
                if(!$result = $this->getModel($one)->delete()){
                    $errorIds[] = $one;
                }
            }
            if(count($errorIds) == 0){
                return $this->redirect(yii::$app->request->headers['referer']);
            }else{
                return ['status'=>0, 'msg'=>implode(',', $errorIds)];
            }
        }else {
            $this->getModel($id)->delete();
            return $this->redirect(yii::$app->request->headers['referer']);
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->getModel($id);
        if ( Yii::$app->request->isPost ) {
            if( $model->load(Yii::$app->request->post()) && $model->save() ){
                Yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
                return $this->redirect(['update', 'id'=>$model->primaryKey]);
            }else{
                Yii::$app->getSession()->setFlash('error', yii::t('app', 'Error'));
                $errors = $model->getErrors();
                $err = '';
                foreach($errors as $v){
                    $err .= $v[0].'<br>';
                }
                Yii::$app->getSession()->setFlash('reason', $err);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionSort()
    {
        if(yii::$app->request->isPost) {
            $data = yii::$app->request->post();
            if (!empty($data['sort'])) {
                foreach ($data['sort'] as $key => $value) {
                    $model = $this->getModel($key);
                    if( $model->sort != $value ){
                        $model->sort = $value;
                        $model->save();
                    }
                }
            }
        }
        $this->redirect(['index']);
    }

    public function actionChangeStatus($id='', $status=0)
    {
        $model = $this->getModel($id);
        $model->status = $status;
        if( $model->save() ){
            return $this->redirect([
                'index'
            ]);
        }
    }

    public function getModel($id='')
    {
        return '';
    }

}
