# 接口说明

<Bit/>

::: tip 提示
接口通信规范
:::

参考链接：[RESTful API 设计指南](http://www.ruanyifeng.com/blog/2014/05/restful_api.html)
# 请求

## 1. GET 获取资源

### 1.1. 使用情境

- 获取资源列表
- 获取资源详情

### 1.2. 特殊情况

#### 1.2.1. 带多条件的列表请求

##### 参数

- 分页：当前页码 page（必填，没有page返回全部列表），每页长度 pagesize（默认10）

```
/api/staff?page=1&pagesize=10
```
- 排序：sort
```
/api/staff?sort=age-desc
```
- 筛选：filters。
使用“ ; ”表示and，“ | ”表示or
使用“ ( ”和“ ) "区分条件优先级
以关联表为条件时在字段名中以“.”区分
```
/api/staff?filters=(department_id=[1,7,11];position.level>5)|name~王
```

条件名称 | 特殊符号 | 值格式
--- | --- | ---
等于 | = |
包含（模糊搜索） | ~ |
大于 | > |
大于等于 | >= |
小于 | < |
小于等于 | <= |
属于 | = | [value1,value2,value3] 

##### 返回
键|内容|数据类型|示例
---| --- | --- 
 data|当前页数据 |Array |
 total|总条数 |Int | 
 filtered|筛选后条数 |Int |
 page|当前页码 |Int | 
 pagesize|每页条数 |Int |
 totalpage|总页数 |Int |

## 2. POST 非幂等提交
## 3. PUT 幂等提交
## 4. DELETE 删除资源

# 响应
### 1.状态码

code|请求类型|说明
---|---|---
200|GET | 服务器成功返回用户请求的数据
201|POST,PUT,PATCH | 用户新建或修改数据成功
204|DELETE|用户删除数据成功
400|POST,PUT,PATCH|用户发出的请求有错误，服务器没有进行新建或修改数据的操作
401| * |表示用户没有权限（令牌、用户名、密码错误）
403| * |表示用户得到授权（与401错误相对），但是访问是被禁止的
404| * |用户发出的请求针对的是不存在的记录，服务器没有进行操作
422|POST,PUT,PATCH|当创建一个对象时，发生一个验证错误


### 2.错误处理
如果状态码是4xx，就应该向用户返回出错信息。一般来说，返回的信息中将error作为键名，出错信息作为键值即可。
```	
{
	"message"："这是错误信息"
}
 ```
# 特殊接口

## 1.批量导入接口
### 1.1.请求类型
> POST/PUT 
### 1.2.参数
> Excel文件
### 1.3.返回值
```
{
	"data":[ Object, Object ],	//导入成功的数据库data（每条同添加/编辑返回值）
	"headers":["header1","header2"],//导入文件的头
	"errors":[
		{ 
			"row":1,
			"rowData":Array,//出现异常的单行数据，与导入表数据相同，键使用导入表头
			"message":{
				"key_1":["error_message_1","error_message_2"],//表头名称：错误原因
				"key_2":["error_message_1"]
			}
		},
	]
}
```