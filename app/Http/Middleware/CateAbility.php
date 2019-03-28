<?php

namespace XigeCloud\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use XigeCloud\Models\Category;

class CateAbility
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  $ability  upload|delete|edit|edit_tag|download
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $ability = '')
    {
        if (!$request->user()->roles()) {
            abort(500, '请先配置该员工角色');
        }
        $cateID = $request->cate_id;
        $category = Category::find($cateID);
        if (empty($category)) {
            abort(500, '文件分类不存在');

        } elseif (!$category->allow_edit && ($ability === 'edit')) {
            abort(403, '该分类文件不允许编辑');

        } elseif (!$category->allow_expire && $request->has('expired_at')) {
            abort(403, '该分类文件不允许设置过期时间');

        } elseif (!empty($ability) && !$request->user()->ability($cateID, $ability)) {
            abort(403, '你没有权限执行该操作');
        }

        return $next($request);
    }

    /**
     * 获取文件操作权限.
     * 
     * @param  integer $cateID 文件分类
     * @param  string $name 
     * 
     * @return bool
     */
    protected function getAbility($cateID, $name = '')
    {
        $abilities = [];
        $this->user->roles()->map(function ($item) {
            return $item->categories->mapWithKeys(function ($cate) {
                return [$cate->id => $cate->pivot];
            });
        })->map(function($item) use ($cateID) {
            return $item->filter(function ($item, $key) use ($cateID) {
                return $key === (int)$cateID;
            });
        })->collapse()->map(function ($item) use (&$abilities) {
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
        return $abilities['file_'.$name] === 1;
    }
}
