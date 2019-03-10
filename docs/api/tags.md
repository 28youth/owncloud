# 文件标签管理

## 获取标签
```
GET /api/tags
```

> 数据字典

| 名称 | 类型 | 说明 |
|:----|:----|:----|
| id | int | 主键 |
| name| string | 标签名称 |
| category| array | 关联标签分类 |
| tag_category_id | int | 标签分类 |

## 创建标签
```
POST /api/tags
```
> params

| 名称 | 类型 | 说明 |
|:----|:----|:----|
| name| string | 标签名称 |
| tag_category_id| integer | 关联标签分类 |

## 编辑标签
```
PATCH /api/tags/:tag
```
> params

| 名称 | 类型 | 说明 |
|:----|:----|:----|
|  id | int | 标签id |
| name| string | 标签名称 |
| tag_category_id| int | 关联标签分类 |

## 删除标签
```
DELETE /api/tags/:tag
```
```
Response 204 OK
```
	