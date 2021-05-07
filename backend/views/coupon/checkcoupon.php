<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/24 0024
 * Time: 下午 3:52
 */

use yii\helpers\Url;
use yii\helpers\Html;
?>
<div class="page-header am-fl am-cf">
    <h4>
        优惠券使用率查询<small>&nbsp;&nbsp;/ 优惠券总的使用情况</small>
    </h4>
</div>
<table class="table table-bordered "  style="margin-top:10px;">
    <thead>
    <tr>
        <th>优惠券总数</th>
        <th>未激活数</th>
        <th>未激活百分比%</th>
        <th>已激活数</th>
        <th>已激活百分比%</th>
        <th>已使用数</th>
        <th>已使用百分比%</th>
        <th>已过期数</th>
        <th>已过期百分比%</th>
    </tr>
    </thead>
    <tbody id="coupon_table">

        <tr>
            <td><?=$info['total']?></td>
            <td><?=$info['not_used']?></td>
            <td><?=$info['not_used_percent']?></td>
            <td><?=$info['activation']?></td>
            <td><?=$info['activation_percent']?></td>
            <td><?=$info['already_used']?></td>
            <td><?=$info['already_used_percent']?></td>
            <td><?=$info['expired']?></td>
            <td><?=$info['expired_percent']?></td>
        </tr>

    </tbody>
</table>