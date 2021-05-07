<?php

define('IS_V2', true);//是否为云车2.0版
define('STATIC_URL', IS_V2 ? '/frontend/web/cloudcarv2' : '.');

defined('INSNEWS') or define('INSNEWS', 6);//年检云车说id

//类型
defined('INSPECTION') or define('INSPECTION', 7);//年检
defined('SEGWAY') or define('SEGWAY', 8);//盛大代步
defined('ETC') or define('ETC', 9);//山东etc

//优惠券状态
defined('COUPON_UNACTIVE') or define('COUPON_UNACTIVE', 0);//未激活
defined('COUPON_ACTIVE') or define('COUPON_ACTIVE', 1);//已激活
defined('COUPON_HASUSE') or define('COUPON_HASUSE', 2);//已使用
defined('COUPON_OVERDUE') or define('COUPON_OVERDUE', 3);//已过期

//订单状态
defined('ORDER_CANCEL') or define('ORDER_CANCEL', -1);//取消订单  已退单
defined('ORDER_HANDLING') or define('ORDER_HANDLING', 1);//已付款 处理中
defined('ORDER_SUCCESS') or define('ORDER_SUCCESS', 2);//成功
defined('ORDER_FAIL') or define('ORDER_FAIL', 3);//失败
defined('ORDER_LOCK') or define('ORDER_LOCK', 4);//充值锁定中 (也是处理中)
defined('ORDER_CANCELING') or define('ORDER_CANCELING', 5);//取消订单申请中
defined('ORDER_UNPAY') or define('ORDER_UNPAY', 6);//未支付
defined('ORDER_SERVEING') or define('ORDER_SERVEING', 7);//服务中
defined('ORDER_WAITING') or define('ORDER_WAITING', 8);//等待中
defined('ORDER_UNSURE') or define('ORDER_UNSURE', 9);//未确认
defined('ORDER_SURE') or define('ORDER_SURE', 10);//已确认
defined('ORDER_UNHAND') or define('ORDER_UNHAND', 11);//未处理

//供应商名称
defined('COMPANY_DIANDIAN') or define('COMPANY_DIANDIAN', 0);//典典
defined('COMPANY_CHEXINGYI') or define('COMPANY_CHEXINGYI', 1);//车行易
defined('COMPANY_SHENGDA_SEGWAY') or define('COMPANY_SHENGDA_SEGWAY', 3);//盛大代步（暂时没用到）
defined('COMPANY_SHANDONG_ETC') or define('COMPANY_SHANDONG_ETC', 4);//山东ETC（暂时没用到）
defined('COMPANY_SHENGDA_WASH') or define('COMPANY_SHENGDA_WASH', 2);//盛大洗车
defined('COMPANY_DIANDIAN_WASH') or define('COMPANY_DIANDIAN_WASH', 1);//典典洗车

//提示状态code
defined('ERROR_STATUS') or define('ERROR_STATUS', 0);//错误码
defined('SUCCESS_STATUS') or define('SUCCESS_STATUS', 1);//成功码