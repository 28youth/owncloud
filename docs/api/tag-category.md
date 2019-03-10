# 标签分类管理

## 获取标签分类
```
GET /api/tags/categories
```
> 数据字典

| 名称 | 类型 | 说明 |
|:----|:----|:----|
| id | int | 主键 |
| name| string | 分类名称 |
| color | string |颜色 |

## 创建标签分类
```
POST /api/tags/categories
```
>params

| 名称 | 类型 | 说明 |
|:----|:----|:----|
| name| string | 分类名称 |
| rank | integer | 排序 |
| color | string | 颜色 |
## 编辑标签分类
```
PATCH /api/tags/categories/:cate
```
>params

| 名称 | 类型 | 说明 |
|:----|:----|:----|
| name| string | 分类名称 |
| rank | integer | 排序 |
| color | string | 颜色 |

## 删除标签分类
```
DELETE /api/tags/categories/:cate
```
```
Response 204 OK
```