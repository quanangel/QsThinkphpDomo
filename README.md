#Qs-thinkphp5

因为好多常用的类库不能直接支持THINKPHP5
所以有了这个自己有些自己重写、改造的thinkphp5的扩展库
未全部上传，已上传的基本可正常使用。

##目录结构
初始的目录结构如下：
```
Qs  扩展库
|--auth                  AUTH库
|   |--qsAuth.php        AUTH主功能文件
|--phpanalysis           PHPanalysis2.0目录
|   |--dict              字典目录
|   |--readme            自述目录
|   |--demo.php          演示文件
|   |--dict_build.php    编译词库
|   |--PhpAnalysis.php   改造过的PHPANALYSIS文件
|
|
Demo  演示目录（或者子目录）
|--application           应用目录
|  |--common             公共模块目录（可以更改）
|  |--module_name        模块目录
|  |  |--config.php      模块配置文件
|  |  |--common.php      模块函数文件
|  |  |--controller      控制器目录
|  |  |--model           模型目录
|  |  |--view            视图目录
|  |  |-- ...            更多类库目录
|  |
|  |--command.php        命令行工具配置文件
|  |--common.php         公共函数文件
|  |--config.php         公共配置文件
|  |--route.php          路由配置文件
|  |--tags.php           应用行为扩展定义文件
|  |--database.php       数据库配置文件
|
|--public                WEB目录（对外访问目录）
|  |--index.php          入口文件
|  |--router.php         快速测试文件
|  |--.htaccess          用于apache的重写
|
|--thinkphp              框架系统目录
|  |--lang               语言文件目录
|  |--library            框架类库目录
|  |  |--think           Think类库包目录
|  |  |--traits          系统Trait目录
|  |
|  |--tpl                系统模板目录
|  |--base.php           基础定义文件
|  |--console.php        控制台入口文件
|  |--convention.php     框架惯例配置文件
|  |--helper.php         助手函数文件
|  |--phpunit.xml        phpunit配置文件
|  |--start.php          框架入口文件
|
|--extend                扩展类库目录（已集成Qs扩展库）
|--runtime               应用的运行时目录（可写，可定制）
|--vendor                第三方类库目录（Composer依赖库）
|--build.php             自动生成定义文件（参考）
|--composer.json         composer 定义文件
|--LICENSE.txt           授权说明文件
|--README.md             README 文件
|--think                 命令行入口文件
```