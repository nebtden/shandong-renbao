<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/1 0001
 * Time: 上午 11:03
 */

use yii\helpers\Url;
use yii\helpers\Html;
use Faker\date;
?>
<div class="page-header am-fl am-cf">
    <h4>优惠券管理 <small>&nbsp;/&nbsp;优惠券编辑</small></h4>
</div>
<div class="container-fluid" style="padding-top: 15px;height:800px;">
    <form class="form-horizontal"   method="post" action="<?php echo Url::to(['coupon/companyedit']) ?>" >
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">公司名称</label>
            <div class="col-sm-3">
                <input type="text" required placeholder="公司名称" name="name"   value="<?php  echo $info['name']; ?>" class="form-control"  />
            </div>
        </div>

            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">是否需要券包使用回调</label>
                <div class="col-sm-3">
                    <select  name="is_pnotice"  placeholder="是否需要券包回调"  class="form-control">
                        <option value="0" <?php if($info['is_pnotice']==0) echo 'selected'; ?>>否</option>
                        <option value="1" <?php if($info['is_pnotice']==1) echo 'selected'; ?>>是</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">券包使用回调地址</label>
                <div class="col-sm-3">
                    <input type="text" placeholder="券包使用回调地址" name="package_url"   value="<?php  echo $info['package_url']; ?>" class="form-control"  />
                </div>
            </div>
            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">是否需要券使用回调</label>
                <div class="col-sm-3">
                    <select  name="is_cnotice"  placeholder="是否需要券使用回调"  class="form-control">
                        <option value="0" <?php if($info['is_cnotice']==0) echo 'selected'; ?>>否</option>
                        <option value="1" <?php if($info['is_cnotice']==1) echo 'selected'; ?>>是</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">券使用回调地址</label>
                <div class="col-sm-3">
                    <input type="text" placeholder="券使用回调地址" name="coupon_url"   value="<?php  echo $info['coupon_url']; ?>" class="form-control"  />
                </div>
            </div>
            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">是否需要后台发包</label>
                <div class="col-sm-3">
                    <select  name="is_anotice"  placeholder="是否需要后台发包"  class="form-control">
                        <option value="0" <?php if($info['is_anotice']==0) echo 'selected'; ?>>否</option>
                        <option value="1" <?php if($info['is_anotice']==1) echo 'selected'; ?>>是</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">发包通知地址</label>
                <div class="col-sm-3">
                    <input type="text" placeholder="发包通知地址" name="admin_url"   value="<?php  echo $info['admin_url']; ?>" class="form-control"  />
                </div>
            </div>
        <?php  if($info['id']) {?>
            <?php if($info['is_pnotice']==1 || $info['is_cnotice']==1 || $info['is_anotice']==1){?>
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Appkey</label>
                    <div class="col-sm-3">
                        <input type="text"  name="appkey" disabled="disabled"  value="<?php  echo $info['appkey']; ?>" class="form-control"  />
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Secret</label>
                    <div class="col-sm-3">
                        <input type="text"  name="secret" disabled="disabled"  value="<?php  echo $info['secret']; ?>" class="form-control"  />
                    </div>
                </div>
            <?php } ?>
            <input type="hidden"  name="id"  value="<?php echo $info['id']; ?>">
        <?php }?>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="button" class="btn btn-default" onclick="history.go(-1);">返回</button>
                <button type="submit" class="btn btn-default">保存</button>
            </div>
        </div>
    </form>
</div>

