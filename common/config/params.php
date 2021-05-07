<?php
return [
    'serverId' => '10086',
    'serverKey' => '5RNGjHSQXMdzAeh1',
    'backendTitle' => '云车驾到管理后台',
    'jserverId' => 'jiutian001',
    'jserverKey' => '83W85WJ9',
    'signKey' => '90up174wdddtrt12ogdqzhoqigwesarg',
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'user.passwordResetTokenExpire' => 3600,
    'url' => 'http://www.yunche168.com',
    'yunche_hotline' => '400-617-1981', //云车服务热线
    'groupbuyTime' => '5',
    'pid' => ['dhygbuy' => 1],
    'Ecar' => [
        'appkey' => '61000204',
        'secret' => '3239275a-6388-4b32-af73-7bc799980b26',
        'from' => '01051583',
        'url'=>'http://open.api.edaijia.cn/',
        'key'=>'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCdwcgkEI5KLNmPBLxE2liPl/9UokSmd6szru9wHPs6D6JdNk4cmIDJPpV+UslberqnG/IMHl0pf0jbU6HKb+KvpE5EAZoJqJOg76chSQtxdB/zSqr1zgDT+VaoRgol/rDmIA3SLAAw5sDp7eia9eYuKSUuYkf4dRSpX933jb16CwIDAQAB',
    ],
    'juhe'=>[
        'oil_key'=>'01f3f3db3628cb31d9a5293d8c81a557',
        'oil_url'=>'http://test-v.juhe.cn/',
        'open_id'=>'JH6f735158305a4271bdd3a25df72519bb',
    ],

    'bangchong'=>[
        'oil_key'=>'1e76d4de45e674ecf6893c773f2356e9',
        'oil_url'=>'http://120.27.219.49:80',
        'uid'=>'test',
        'notice_url'=>'http://testing.yunche168.com/frontend/web/notice/bang-chong-status.html',
        'business_id'=>'yk',
    ],
/*    商户ID1854901219
商户Key(customerKey)
子渠道：10031
权益id: 200120
APP Key： e3d92eb7a0cc4532a7e045d96d6632d5
APP Sec： b1b5738723c24a7f978c4f7df7f9bd11*/
    'didi'=>[
        'secret' => 'b1b5738723c24a7f978c4f7df7f9bd11',
        'url'=>'https://test.kuaidadi.com:9002/gateway',
        'h5_url'=>'https://pinzhi-sp.didichuxing.com/m/djhome.node',
        'key'=>'e3d92eb7a0cc4532a7e045d96d6632d5',
        'channel'=>'10031',
        'ttid'=>'yunche',
        'customerKey'=>'MTg1NDkwMTIxOQ==',  //合作商
//        'privilegeId'=>'200120',  //因为有多个优惠权益，权益绑定在优惠券上
    ],
    'driving_coupon_type'=>[
        '3236' => '滴滴10公里30分钟等待代驾服务',
        '3238' => '滴滴15公里30分钟等待代驾服务',
        '3240' => '滴滴20公里30分钟等待代驾服务',
        '3242' => '滴滴30公里30分钟等待代驾服务'
    ],
    'driving_coupon_amount'=>[
        '3236' => '10',
        '3238' => '15',
        '3240' => '20',
        '3242'  =>'30'
    ],

    'chehoujia'=>[
        'url'=>'http://123.56.48.89:8170/',
        'user_id'=>'322',
        'privateKey'=>'fbb174ab2a7d17bf67d4953a10d5c59d98a6c8ff4040548882fed1d3087bb90f',
    ],
    //'shengda_url' => 'http://sd.auto1768.com:8042/ShengDaAutoPlatform/', //盛大汽车URL
    'shengda_url' => 'http://h5.schengle.com/ShengDaAutoPlatform/', //盛大汽车URL
    'shengda_sourceApp' => 'SZDHXC', //盛大汽车key
    'shengda_key' => '9d65b3594312b9110c4476021dc04ea5', //盛大汽车key
    'shengda_key3Des' => 'C205BC5839533270jUN1d77Y', //盛大汽车3des


//    'BmapWeb' => 'Rwh2q8UoSllxKMNekOTrRefBddWpG21s',//百度地图web端api
//    'BmapServer' => 'etp5dSbNN6INt2o9ZYRmifR2TII0VmbP',//百度地图服务器api
    'BmapWeb' => 'rG5cHdKKGSqpVRQgShG6yck7SS3gMlo6',//百度地图web端api
    'BmapServer' => 'QphPRptKcbmMNrHW8YzdzbZA7OSoISAL',//百度地图服务器api
    'jisuapi' => '4927a81a14b1741b',//极速api,用于获取车型数据
    'ddyc_url'=>'https://openapi.ddyc.com',
    'ddyc_oil_key'=>'QBNHWFCEN45LKTQD',
    'ddyc_oil_secret'=>'EK4T09CZ1CX1NVBCBJPHJADNESM4W1MB',
    'ddyc_app_key'=>'D8GAYH87D098HP5P',
    'ddyc_app_secret'=>'8ACFGDSVRN1UC6A6QR4TJBJOIBKMSXBF',
    'oil_append_success'=>'亲爱的车主，您的加油卡充值服务已提交成功，48小时内会为您完成加油卡充值，请耐心等待。【云车驾到】',
    'oil_pay_success'=>'您提交的油卡充值金额amount元已成功充入您的加油卡！请先至加油站进行圈存（找工作人员激活卡内充值余额），即可进行加油服务。【云车驾到】',
    'ins_create_order'=>'【云车驾到】尊敬的客户:您的代办年检订单已生成，请尽快邮寄材料：1、行驶证正副本原件 2、在保险期间内的交强险保单副本原件 3、车辆所有的人身份证复印件（如车辆是贷款或者抵押车需要提供车主身份证原件）4、违章处理完（无需证明，请自行确保） 邮寄地址：[smsaddr]  办理时效为收到材料后1-2个工作日（建议使用顺丰快递）。为确保材料安全及办理顺畅，邮寄后请即刻在云车驾到-年检订单页面正确填写物流单号，否则对造成的后果及费用需自行承担！如有疑问，请拨打：400-617-1981',
    'ins_create_order_new'=>'【云车驾到】尊敬的客户:您的代办年检订单已生成，您可以直接在订单页面提交订单，等待客服联系您告知具体的办理方式！如有疑问，可主动拨打：400-617-1981',
    'ins_finish_order'=>'【云车驾到】尊敬的客户:您的代办年检订单已完成服务,办理结果:orderstatus。材料寄回中，请耐心等待！可进入平台查看订单详情。如有疑问，请拨打：400-617-1981',

    'cyx_url'=>'http://test.cx580.com:5083',//test
    'cyx_key'=>'dinghantest',//合作方账号，由车行易分配 test
    'cyx_aeskey'=>'759D9A2E7790474A99E9300132DAEB80',//密钥由车行易提供 test
    'shandongtaibao' => [       //山东太保接口URL地址 测试环境
        'url' => 'https://rescueinterfacesit.ecpic.com.cn/AGAInterface/innerServer',
        'spCode' => '1CXDJ14'
        ],

    'sms'=>[
        'api_send_url'=>'http://smssh1.253.com/msg/send/json',
        'api_account'=>'N9778747',
        'api_password'=>'7vSQ7KHeaT5itjAf',
    ],
//    'segway_url'=>'http://101.231.154.154:18050/third_party/',//盛大代步URl
    'segway_url'=>'http://101.231.154.154:7777/',//盛大代步URl

    'pinganhcz' => [  //平安好车主账号 测试环境
        'userName' => '13142186218',
        'key' => '243439B8C6583DA0',
        'url' => 'https://test-api.pingan.com.cn:20443',
        'client_id' => 'P_DINGHAN001',
        'client_secret' => 'eM8B1g3G'
    ],

    'shandong_etc' => [  //山东ETC 测试环境
        'url' => 'http://xlapi-c.test.etcsd.cn/xl-api/common/gateway',
        'appid' => 'DH000001',
        'rsa_pri' =>'MIIEogIBAAKCAQEAx2BFXE1heY4nZpl8o3Eg6dcpm5WIumffinILvs6Vq7EXKSGg/ufN0F3H0tSQWCIWrQ2zS6CMKhtVJIdLKqV877eLIbjNiP32a35Z/zVRIZqjrZxX3hjJJIxTVbRqUzhir/F8ZnjDP4LNjw4hEAAXnkkybSO5XyKDiC8oNt4RFOc8WN5zwyFYTf997PsjQqE87NL4F79x2eekTAj1mmMmHcpUMG6L8eYVYK39Pl/Pia+rwsy1IdLbdAnLQIcOY0js+U0KJLy9B6SArpuHfk8Nu2an+JVv/zxRJLOL1rOVu2YmYG1tQSoT9/R/vv4nZaOMH0jXZN2wHRu8uLNP+n+2SQIDAQABAoIBAB+NuOx2wqp+OPYf7H3hdZgMmWUqMU1vCw3aJsm34DAgAs5bqCMvhH8pmGphgcaw/m288VdSvVwcn0j8sIS7VB6MKdTYbo7zwoAYqx2czo5G7wiW6cEYoxzJqJazyvAIyHWX/lwpWJ1Akk/FqK3jldlTNEvZj4ji6XYwIGkuk/1+oxz5i0yxwzUCFGelar3xmi5eW1M2wcAIsdjm1OqGU7V4wZrmw+mYs25hzfDMDAwX/mvL5uRie8kyb4v4mJgj9Ao35e35+LvXsQfu9WjbPEKXYSmHBwlDz042BGkF/2iD4ShOE96qt2m+97qNo7tzT3ACJQki4nQB2MxclF1bZEECgYEA/3/YBYeCdeounesJGCJ1zXdDyqlkETM2ek0OpZR1cAI2t0i04luRHskVvs0JYPPxcvnoDnzjNlgrfWqBIibqCKfPo2wE1Jrl3pDGMgQG8aEIMBGR8eCqEg39lh/JfkI68q8+y1fMSBOj4830n2Py1P4U0wt0nZW2xfGJQT6t810CgYEAx8RGsgDaBkIePg1aVt2FLOruzW3pIE+N3zhSVR+dySrE5pBw7f8RA3MI+4pEPswkC951Bc90IgG/4K7TKbmLHgUs1gorvzXww+P6t+nT4VCtjhf6TA60ZFuW/lPU2sruvVBAM51hmHGI8jET11tCduABDEPjzPQV9lQc6K0EK90CgYBs/ed0LGNt07GBMkNV5VVliewEZxBF+hfSQk7uOY+DcZ5Pv5dSPIeCn3tEQokur8GfL5Zbn9D5XNFoledyXzFU8ek0qJ7C5zUKJa42pdskdnJctxIQIavLOeakhPjagHxPDIz4B4MgeChgxHoHCIcNbzoI8YkUtEoc1LWdfJSODQKBgCCpM5HI19ysFLu6fSm0oCB+7LpDnRj+SMjVMYjfWXHZf0XfGcefq01wEmjl61CFGeOi5GklyOwCczet/bJVtqwa1oDMOujRTeMJNCW7oju1cBrKSs8CnHXID8DXPfbjtPLY2xZVRe6hqW1HnuHX9t7ust0pgGYzBiA3El7sYsE1AoGAOncRKRAYvRPTVilTySDnIENzALzXON9YqiYqGKFo2Gkjukuqbjf0x4s2NS5hc3Dz2mrWeQSmC8c/+F6xkNbtkz5HfoLev5OgWytnUQqy2jc9VezAhmIVZh7Gs21pZKTC9WMk9R50JSesQWe/uYwx5WxRLnpaoQ0ZF+2fzowQwiA=',
        'rsa_pub' =>'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAxvmAIUoWQ2Zebc1gC2f+dlwWzf9YYDJ04OMIu4Gl1FPuZ0pd2U0XlUsZP45TGDl8Nt+5AgTXByi2nRQlCcWRsVnW8qHlLJRvmY1EOFeyhMfoJKkZSLt07oXBtvmJDPMHGZTw2RP2gIhuoyS2I2tasYgGWZMt+QEGEOA9sN4Y/GXxvwoMhngDkghlwSdj0WCwzlxEFCa6vAfJJ3htnQKFvckQ8ZqhT+Uk7onIgeyVPRfHhdf5WeMqw+jGN4fWUDCHjt8fqHyvKOVI8ZM3yH6Zm4Q9j55A0JYlK3uYIGjM4UwKLv8uiOl5/7LTt2FQjBro2HRXAyfSzgvzXIq9j3mnGQIDAQAB',
    ],

];
