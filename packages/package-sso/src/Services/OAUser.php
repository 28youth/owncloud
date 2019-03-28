<?php
/**
 * Created by PhpStorm.
 * User: Fisher
 * Date: 2018/4/4 0004
 * Time: 20:56
 */

namespace Fisher\SSO\Services;

use XigeCloud\Models\Role;
use XigeCloud\Models\StaffHasRole;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;

class OAUser implements UserContract
{
    protected $attributes;

    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    public function getAuthIdentifierName()
    {
        return 'staff_sn';
    }

    public function getAuthIdentifier()
    {
        $name = $this->getAuthIdentifierName();
        return $this->attributes[$name];
    }

    public function getAuthPassword()
    {
        return $this->attributes['password'];
    }

    public function getRememberToken()
    {
        // TODO: Implement getRememberToken() method.
    }

    public function setRememberToken($value)
    {
        // TODO: Implement setRememberToken() method.
    }

    public function getRememberTokenName()
    {
        // TODO: Implement getRememberTokenName() method.
    }

    // 获取认证用户角色
    public function roles($role = '')
    {
        $ids = StaffHasRole::byStaffSn($this->staff_sn)->pluck('role_id');
        $roles = Role::query()
            ->with(['categories' => function ($query) {
                $query->select(['id']);
            }])
            ->whereIn('id', $ids)
            ->get()->keyBy('id');
        if (! $role) {
            return $roles;
        }

        return $roles->get($role, false);
    }

    /**
     * 获取文件操作权限.
     * 
     * @param  integer $cateID 文件分类
     * @param  string $name upload|delete|edit|edit_tag|download
     * 
     * @return bool|array
     */
    public function ability($cateID, string $name = '')
    {
        $abilities = [];
        $roles = $this->roles()->map(function ($item) {
            return $item->categories->mapWithKeys(function ($cate) {
                return [$cate->id => $cate->pivot];
            });
        })->map(function($item) use ($cateID) {
            return $item->filter(function ($item, $key) use ($cateID) {
                return $key === (int)$cateID;
            });
        })->collapse();

        // 该分类没有关联的可操作角色/权限名错误～
        if (
            $roles->isEmpty() || 
            ($name && !in_array($name, ['upload', 'delete', 'edit', 'edit_tag', 'download']))
        ) {
            return 0;
        }

        // 合并操作权限～
        $roles->map(function ($item) use (&$abilities) {
            if (empty($abilities)) {
                $abilities = $item->toArray();
            } else {
                collect($item)->map(function ($v, $k) use (&$abilities) {
                    if ($abilities[$k] < $v) {
                        $abilities[$k] = $v;
                    }
                });
            }
        });
        
        return !empty($name) ? $abilities['file_'.$name] : $abilities;
    }

    public function toArray()
    {
        return $this->attributes;
    }

    public function __get($key)
    {
        return $this->attributes[$key];
    }

    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    public function __isset($key)
    {
        return isset($this->attributes[$key]);
    }

    public function __unset($key)
    {
        unset($this->attributes[$key]);
    }

    public function __toString()
    {
        return json_encode($this->attributes);
    }
}