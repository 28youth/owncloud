# 文件分类管理

## 获取分类
```
GET /api/categories
```

> 数据字典

| 名称 | 类型 | 说明 |
|:----|:----|:----|
| id | int | 主键 |
| name| string | 名称 |
| full_name| string | 全名 |
| symbol | string | 编号 |
| policy | object | 服务器配置 |
| policy_id | int | 服务器配置id |
| parent_id | int | 上级分类 |
| is_lock | int | 是否锁定 |
| is_expired | int | 是否含过期文件 |
| dirrule | string | 目录生成规则 |
| numberrule | string | 编号生成规则 |
| filetype | array | 文件上传类型 |
| max_size | int | 单文件允许上传大小 |
| description | string | 分类备注 |

## 创建分类
```
POST /api/categories
```
> params

| 名称 | 类型 | 说明 |
|:----|:----|:----|
| name| string | 名称 `必填` |
| symbol | string | 编号`不填默认首字母` |
| policy_id | int | 服务器配置id `必填` |
| parent_id | int | 上级分类 |
| is_lock | int | 是否锁定 `0:否 1:是` |
| is_expired | int | 是否含过期文件 `0:否 1:是` |
| dirrule | string | 目录生成规则 `必填` |
| numberrule | string | 编号生成规则 `必填` |
| filetype | array | 文件上传类型 |
| max_size | int | 单文件允许上传大小 `单位kb` |
| description | string | 分类备注 |

## 编辑分类
```
PATCH /api/categories/:category
```
> params

| 名称 | 类型 | 说明 |
|:----|:----|:----|
| id | int | 主键 `必填` |
| name| string | 名称 `必填` |
| symbol | string | 编号`不填默认首字母` |
| policy_id | int | 服务器配置id `必填` |
| parent_id | int | 上级分类 |
| is_lock | int | 是否锁定 `0:否 1:是` |
| is_expired | int | 是否含过期文件 `0:否 1:是` |
| dirrule | string | 目录生成规则 `必填` |
| numberrule | string | 编号生成规则 `必填` |
| filetype | array | 文件上传类型 |
| max_size | int | 单文件允许上传大小 `单位kb` |
| description | string | 分类备注 |


## 删除分类
```
DELETE /api/categories/:category
```
```
Response 204 OK
```
	