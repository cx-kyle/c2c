## Rage


### 思维导图

![image](docs/guide-zh-CN/images/RageFrame2.png)

### 系统快照

【首页】
![image](docs/guide-zh-CN/images/index.png)
【微信自定义菜单】
![image](docs/guide-zh-CN/images/wechat-menu.png)
【微信关注统计】
![image](docs/guide-zh-CN/images/wechat-stat.png)
【插件模块列表】
![image](docs/guide-zh-CN/images/addon-list.png)
【插件模块文章模块】
![image](docs/guide-zh-CN/images/addon-activity.png)

### 开始之前

- 具备 PHP 基础知识
- 具备 Yii2 基础开发知识
- 具备 开发环境的搭建
- 仔细阅读文档，一般常见的报错可以自行先解决，解决不了在来提问
- 如果要做小程序或微信开发需要明白微信接口的组成，自有服务器、微信服务器、公众号（还有其它各种号）、测试号、以及通信原理（交互过程）
- 如果需要做接口开发(RESTful API)了解基本的 HTTP 协议，Header 头、请求方式（`GET\POST\PUT\PATCH\DELETE`）等
- 能查看日志和Debug技能
- 一定要仔细走一遍文档

## 用户注册
>### 接口场景
接收手机号和密码 入库操作
>### 调用地址
	/user/register
>#### 请求方式
  POST
>### 请求参数
| 字段名 | 变量名 | 必填 | 类型 | 示例值 | 描述 |
| ------- | --------- | -------- | --------- | ---------- | --------- |
| 用户手机号 | account | 是 | string(11) | 13333838316 | 手机号 |
| 用户密码 | password | 是 | string(32) | f561aaf6ef0bf14d4208bb46a4ccb3ad | md5后的用户密码 |
| 验证码 | vcode | 是 | string(6) | 138988 | 手机验证码 |

举例如下:
	{
		"account"  	: "13333838316",
		"password" 	: "f561aaf6ef0bf14d4208bb46a4ccb3ad",
		"vcode" 	: "138988"	
	}

>### 返回结果
| 字段名 | 变量名 | 必填 | 类型 | 示例值 | 描述 | 	
| ------------- | ------------- | ------------- | ------------- | ------------- | ------------- |
| 返回状态码 | return_code | 是 | string(32) | SUCCESS | 用户是否注册成功 |
| 返回信息 | return_msg | 否 | string(128) | 手机号已注册或验证码错误 | 错误信息描述 |

举例如下：
	{
		"return_code" : "SUCCESS",
		"return_msg"  : "",
	}

>### 错误码
| 名称 | 原因 |
| ---  | --- |
| USERNAME_EXISTS | 用户名存在 |
| WRONG_PARAMS | 参数错误 |
| VCODE_ERROR | 验证码错误 |
