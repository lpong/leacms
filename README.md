leacmf是一款基于ThinkPHP5+Bootstrap+layui的极速后台开发框架。
===============

## **主要特性**

* 基于`Auth`验证的权限管理系统
* API快速开发，已完成初始化程序
* 完善的前端功能组件开发
    * 基于`AdminLTE` `layui`开发
    * 基于`Bootstrap`开发，自适应手机、平板、PC 
    * 封装了部分方法，开发快速简单，没有文档，看例子
 * 自动高亮菜单，自动面包屑，根据权限自动生成菜单树
 * 集成api验证,开发api和后台都快速方便
 * api工具集Y。包含如`Y::$identity`用户类。`Y::Json()`等api开发神器。
* 无须验证的类配置即可
 `
  '/v1/public/*'
 `
  
  
  
  ## **安装方式**  
  
leacmf 需要 PHP &gt;= 5.6以上的版本，并且同时需要PHP安装以下扩展

```
- cURL extension

- mbstring

- BC Math
```
使用 ` git ` 将代码clone到本地，导入数据库文件 `/data/backup/leacmf.sql` 并配置号数据库。然后运行

```
composer update
```


## **在线演示**
暂无

用户名：admin
密　码：123456

## **界面截图**
![1](/public/static/1.png "1")
![2](/public/static/2.png "2")
![3](/public/static/3.png "3")
![4](/public/static/4.png "4")


## **特别鸣谢**

感谢以下的项目,排名不分先后

ThinkPHP：http://www.thinkphp.cn

layui：http://www.layui.com

AdminLTE：https://almsaeedstudio.com

Bootstrap：http://getbootstrap.com

jQuery：http://jquery.com
