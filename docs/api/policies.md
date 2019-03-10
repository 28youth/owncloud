# 服务器配置管理


## 获取服务器配置

```
GET /api/policies
```

> 数据字典

| 名称 | 类型 | 说明 |
|:----|:----|:----|
| id | int | 主键 |
| name| string | 配置名称 |
| driver | string | 存储驱动 |
| host | ip | 服务器ip |
| port | int | 服务器端口 |
| username | string | 服务器用户名 |
| root | string | 上传根目录 |
| privateKey | string | 服务器密钥 |
| timeout | int | 上传超时时间 `默认30秒` |

## 创建服务器配置

```
POST /api/policies
```

> params

| 名称 | 类型 | 说明 |
|:----|:----|:----|
| name| string | 配置名称 `必填` |
| driver | string | 存储驱动 `本地：local 服务器：sftp` |
| host | ip | 服务器ip 当`driver`为`sftp`时必填 |
| port | int | 服务器端口 当`driver`为`sftp`时必填 |
| username | string | 服务器用户名 当`driver`为`sftp`时必填 |
| root | string | 上传根目录 当`driver`为`sftp`时必填 |
| privateKey | string | 服务器密钥 当`driver`为`sftp`时必填 |
| timeout | int | 上传超时时间 `默认30秒` |

## 编辑服务器配置

```
PATCH /api/policies/:policy
```

> params

| 名称 | 类型 | 说明 |
|:----|:----|:----|
| id | int | 主键 |
| name| string | 配置名称 `必填` |
| driver | string | 存储驱动 `本地：local 服务器：sftp` |
| host | ip | 服务器ip 当`driver`为`sftp`时必填 |
| port | int | 服务器端口 当`driver`为`sftp`时必填 |
| username | string | 服务器用户名 当`driver`为`sftp`时必填 |
| root | string | 上传根目录 当`driver`为`sftp`时必填 |
| privateKey | string | 服务器密钥 当`driver`为`sftp`时必填 |
| timeout | int | 上传超时时间 `默认30秒` |

## 删除服务器配置

```
DELETE /api/policies/:policy
```
```
Response 204 OK
```