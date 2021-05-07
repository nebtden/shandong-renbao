云车驾到项目
1、使用默认的.gitignore 配置
2、默认测试数据库，全部放在common/config/params.php 里面
   一般情况下，使用git下载之后，可以直接使用
   另外，测试数据库上传到git，影响问题不大
   对于正式数据库，正式配置，则使用main-local.php
   params-local.php 去覆盖
   
   
3、分配账号之后，自己使用相关key
4、定时任务使用crontab。。不要使用单独的控制器

