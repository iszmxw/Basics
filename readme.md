<p align="center"><img src="https://laravel.com/assets/img/components/logo-laravel.svg"></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>
</p>

## About 用户权限基础系统

用户权限基础系统采用的是laravel框架进行编写的

##### Laravel Framework 5.5.46

# 项目采用了以下依赖包

```json
    {
        "guzzlehttp/guzzle": "^6.3",
        "alibabacloud/sdk": "^1.6",
        "iidestiny/laravel-filesystem-oss": "^1.2",
        "predis/predis": "^1.1",
        "rap2hpoutre/laravel-log-viewer": "^1.1",
        "barryvdh/laravel-ide-helper": "^2.6",
        "iszmxw/ip-address": "dev-master"
    }
```

- 【一个网络请求包】[guzzlehttp/guzzle](https://packagist.org/packages/guzzlehttp/guzzle)。
- 【阿里巴巴用于阿里大鱼短信验证】[alibabacloud/sdk](https://packagist.org/packages/alibabacloud/sdk)。
- 【阿里云对象存储第三方封装包】[iidestiny/laravel-filesystem-oss](https://packagist.org/packages/iidestiny/laravel-filesystem-oss)。
- 【Redis封装包】[predis/predis](https://packagist.org/packages/predis/predis).
- 【laravel日志辅助包，利于日志查看】[rap2hpoutre/laravel-log-viewer](https://packagist.org/packages/rap2hpoutre/laravel-log-viewer).
- 【laravel框架辅助ide】[barryvdh/laravel-ide-helper](https://packagist.org/packages/barryvdh/laravel-ide-helper).
- 【IP地址转换】[iszmxw/ip-address](https://packagist.org/packages/iszmxw/ip-address).

## 目录结构

主要使用的到的目录结构如下：

~~~
www                     WEB部署目录（或者子目录）
├─app                   应用目录
│  ├─Console            公共模块目录（可以更改）
│  │  ├─Commands        Command命令（可以自定义php artiasn命令）
│  │  ├─Kernel.php      在文件中可以调用自定义命令
│  ├─Exceptions         
│  ├─Http               网络请求目录
│  │  ├─Controllers     控制器目录
│  │  ├─Middleware      中间件目录
│  │  ├─Requests        表单请求验证目录
│  │  └─Kernel.php      中间件核心，用于控制中间件的注册和分组
│  │
│  ├─Library            自定义Library仓库可以删除，不属于框架
│  ├─Models             数据库模型文件
│  ├─Providers          服务
│  └─User.php           系统自带的用户模型
│
├─bootstrap             bootstrap
├─config                系统配置目录
├─database              数据迁移功能相关目录
├─public                WEB目录（对外访问目录）
├─resources             静态资源目录
├─routes                系统路由存放位置
├─storage               框架缓存位置
├─tests                 矿国家测试目录
├─vendor                vendor目录用于存放composer仓管库的所有依赖包
├─.env                  正式=>服务器读取的配置文件
├─.env.develop          测试=>服务器读取的配置文件
├─.env.example          系统示例配置文件
├─.gitattributes        自动生成定义文件（参考）
├─.gitignore            git忽略配置文件
├─_ide_helper.php       相当于函数助手
├─artisan               artisan
├─composer.json         项目的composer管理配置文件
~~~

## 项目说明
>基础项目，集合了后台账号登录创建，操作日志，菜单管理，权限管理等基础功能，在这里整理一下，以免每次重复进行相同的工作

>克隆项目
```git
git clone https://github.com/iszmxw/Basics.git
cd Basics
composer install
```
>编辑数据库配置信息
```git
vim .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=basics_test
DB_USERNAME=basics_test
DB_PASSWORD=basics_test
DB_PREFIX=xw_
```
>最后导入数据库文件到数据库（basics_test.sql）

## 后台截图
![avatar](/public/images/ht01.png)
![avatar](/public/images/ht02.png)
![avatar](/public/images/ht03.png)
![avatar](/public/images/ht04.png)
![avatar](/public/images/ht05.png)
![avatar](/public/images/ht06.png)
![avatar](/public/images/ht08.png)

## Author微信
![avatar](/public/images/my.png)

