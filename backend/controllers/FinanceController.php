<?php

namespace backend\controllers;

use common\components\AliPay;
use common\models\CarFinanceTransferlog;
use common\models\CarFinanceUserAccount;
use Yii;
use yii\web\Response;
use yii\helpers\Url;
use backend\util\BController;

class FinanceController extends BController
{
    //支付宝转账时的token令牌名称
    private $token_name = 'zfb_transfer_form_token';

    /**
     * @param null $model 获取列表的模型
     * @param string $field 要获取的字段
     * @param array $where 查询条件
     * @param string $order 排序字段
     * @return array|bool       返回数据列表
     */
    protected function get_page_list($model = null, $field = '*', $where = [], $order = 'id DESC')
    {
        //TO DO
        return '';
    }

    /**
     * 获取token与验证token
     * @param null $tokenname
     * @param null $token
     * @return array|bool
     */
    protected function token($tokenname = null, $token = null)
    {
        if (!$tokenname) $tokenname = $this->token_name;
        if ($token === null) {
            //获取token
            $token = md5(uniqid() . time() . microtime());
            Yii::$app->cache->set($tokenname,$token,300);
            return $token;
        } else {
            $oldtoken = Yii::$app->cache->get($tokenname);
            if(!$oldtoken) return false;
            Yii::$app->cache->delete($tokenname);
            if ($oldtoken == $token) {
                return true;
            }
        }
        return false;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            $page = $request->get("pageNumber", 1);
            $pagesize = $request->get("pageSize", 10);
            $list = CarFinanceUserAccount::find()->limit($pagesize)->offset(($page - 1) * $pagesize)->asArray()->all();
            $total = 0;
            if ($list) {
                $total = CarFinanceUserAccount::find()->count();
                foreach ($list as $key => $val) {
                    $list[$key]['account_type_text'] = CarFinanceUserAccount::AccountTypeText($val['account_type']);
                    $list[$key]['u_time_text'] = date("Y-m-d H:i:s", $val['u_time']);
                }
            }
            return json_encode(['IsOk' => 1, 'total' => $total, 'rows' => $list]);
        }
        return $this->render('index');
    }

    public function actionEditaccount()
    {
        $request = Yii::$app->request;
        if ($request->isPost) {
            $data = $request->post();
            if (isset($data['id'])) {
                $id = $data['id'];
                unset($data['id']);
                $data['u_time'] = time();
                $r = CarFinanceUserAccount::updateAll($data, ['id' => $id]);
                if ($r === false) return $this->json(0, '操作失败');
            } else {
                $FUA = new CarFinanceUserAccount();
                foreach ($data as $key => $val) $FUA->$key = $val;
                $FUA->u_time = $FUA->c_time = time();
                $r = $FUA->save();
                if (!$r) return $this->json(0, '操作失败');
            }
            return $this->json(1, '操作成功', [], Url::to(['index']));
        }
        $id = $request->get('id', null);
        $data = null;
        if ($id) {
            $data = CarFinanceUserAccount::find()->where(['id' => $id])->asArray()->one();
        }
        $type = CarFinanceUserAccount::$account_type;
        return $this->render('editaccount', ['type' => $type, 'data' => $data]);
    }

    //转账操作
    public function actionTransfer()
    {
        set_time_limit(0);
        $request = Yii::$app->request;
        if ($request->isPost) {
            $data = $request->post();
            //表单token验证
            $token_name = $this->token_name;
            if (!isset($data[$token_name])) {
                return $this->json(0, '该页面已失效，请刷新页面');
            } else {
                $token = $data[$token_name];
                if (!$this->token($token_name, $token)) return $this->json(0, '该页面已失效，请刷新页面');
            }

            //判断id字段是否存在
            if (!isset($data['id'])) return $this->json(0, '请先创建转账用户账号再进行转账');
            //判断是数字是否合法
            if (!is_numeric($data['amount'])) return $this->json(0, '转账金额输入不正确');
            if ($data['amount'] < 0.1) return $this->json(0, '转账金额至少0.1元');

            $account_id = $data['id'];
            $account = CarFinanceUserAccount::findOne($account_id)->toArray();
            if (!$account) return $this->json(0, '请先创建转账用户账号再进行转账');
            if($account['status'] != 1) return $this->json(0, '账户已禁用，不能转账');

            //构建接口调用所需要的参数
            $out_biz_no = CarFinanceTransferlog::create_out_biz_no();
            $payee_account = $account['payee_account'];
            $amount = $data['amount'];
            $payer_show_name = $data['payer_show_name'] ? $data['payer_show_name'] : '云车驾到';
            $remark = $data['remark'];

            //先将转账信息存入数据库
            $log = new CarFinanceTransferlog();
            $log->out_biz_no = $out_biz_no;
            $log->account_type = $account['account_type'];
            $log->account_id = $account_id;
            $log->payee_account = $payee_account;
            $log->amount = $amount;
            $log->payer_show_name = $payer_show_name;
            $log->payee_real_name = $account['realname'];
            $log->remark = $remark;
            $log->c_time = time();
            $r = $log->save();
            if (!$r) return $this->json(0, '记录插入失败');

            //调用接口
            $res = AliPay::getInstance()->transfer_account($out_biz_no, $payee_account, (string)$amount, $payer_show_name, $remark);
            $flag = 1;
            $msg = '操作成功';
            if ($res) {
                $log->status = 1;
                $log->err_code = $res['code'];
                $log->err_msg = $res['msg'];
                $log->order_id = $res['order_id'];
                $log->pay_date = $res['pay_date'];
            } else {
                $log->status = 2;
                $err = AliPay::getInstance()->getError();
                list($c, $m) = explode(':', $err);
                $log->err_code = $c;
                $log->err_msg = $m;
                $flag = 0;
                $msg = $m;
            }
            $log->pay_day = date("Ymd");
            $log->pay_month = date('Ym');
            $r = $log->save();
            if (!$r) return $this->json(0, '记录保存失败,请记录相关信息并与技术联系！');

            return $this->json($flag, $msg,[],Url::to(['transferlog']));
        }
        $id = $request->get('id', null);
        if (!$id) return '请先添加转账用户账号';
        $data = CarFinanceUserAccount::findOne($id)->toArray();
        $type = CarFinanceUserAccount::$account_type;
        $token['name'] = $this->token_name;
        $token['value'] = $this->token($this->token_name);
        return $this->render('transfer', ['type' => $type, 'data' => $data, 'token' => $token]);
    }

    public function actionTransferlog()
    {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            $page = $request->get("pageNumber", 1);
            $pagesize = $request->get("pageSize", 10);
            $list = CarFinanceTransferlog::find()->limit($pagesize)->offset(($page - 1) * $pagesize)->orderBy('id DESC')->asArray()->all();
            $total = 0;
            if ($list) {
                $total = CarFinanceUserAccount::find()->count();
                foreach ($list as $key => $val) {
                    $list[$key]['account_type_text'] = CarFinanceUserAccount::AccountTypeText($val['account_type']);
                    $list[$key]['c_time_text'] = date("Y-m-d H:i:s", $val['c_time']);
                    $list[$key]['status_text'] = CarFinanceTransferlog::get_status_text($val['status']);
                }
            }
            return json_encode(['IsOk' => 1, 'total' => $total, 'rows' => $list]);
        }
        return $this->render('transferlog');
    }
}