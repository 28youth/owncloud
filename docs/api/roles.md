# 角色管理

# 获取角色列表

```
GET /api/roles
```
> 数据字典

| 名称 | 类型 | 说明 |
|:----|:----|:----|
| id | int | 主键 |
| name| string | 名称 |

## 添加角色

```
POST /api/roles
```
> params

| 名称 | 类型 | 说明 |
|:----|:----|:----|
| name| string | 名称 |
| categories | array | 关联分类 |
| staff | array | 关联员工 |

## 编辑角色

```
PATCH /api/roles/:role
```
> params

| 名称 | 类型 | 说明 |
|:----|:----|:----|
| id | int | 主键 |
| name| string | 名称 |
| categories | array | 关联分类 |
| staff | array | 关联员工 |

## 删除角色

```
DELETE /api/roles/:role 		Response 204 OK
```