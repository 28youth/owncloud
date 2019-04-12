<?php 

use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

if (!function_exists('getSize')) {
    /**
     * 获取 bit 流大小.
     * 
     * @param  integer  $bit
     * @param  boolean $array
     * 
     * @return float||array
     */
    function getSize($bit, $array = false){
        $type = ['Bytes', 'KB', 'MB', 'GB', 'TB'];  
        $box = ['1', '1024', '1048576', '1073741824', 'TB'];  
        for ($i = 0; $bit >= 1024; $i++) {
            $bit/=1024;  
        }
        if ($array) {
            return [(floor($bit * 100) / 100), $box[$i]];  
        }
        return (floor($bit * 100) / 100) . $type[$i];  
    }
}

if (!function_exists('unique_validator')) {
    /**
     * 数据唯一性验证.
     * 
     * @param  string       $table  表名称
     * @param  bool|boolean $ignore 是否排除操作ID
     * @param  bool|boolean $hasDel 是否排除已删除
     * 
     * @return \Illuminate\Validation\Rule
     */
    function unique_validator(string $table, bool $ignore = true, bool $hasDel = false)
    {
        $rule = Rule::unique($table);
        if ($ignore) $rule->ignore(request()->id);
        if ($hasDel === false) {
            $rule->where(function ($query) {
                $query->whereNull('deleted_at');
            });
        }

        return $rule;
    }
}

if (!function_exists('array_to_tree')) {
    /**
     * 数组转为树形结构.
     * 
     * @param  array  $list  数据源
     * @param  string  $pk   主键
     * @param  string  $pid  上级ID健
     * @param  string  $child 子集健
     * @param  integer $root  根ID
     * 
     * @return treeData
     */
    function array_to_tree(array $list, $pk = 'id', $pid = 'parent_id', $child = '_children', $root = 0)
    {
        $tree = [];
        $refer = [];
        foreach ($list as $key => $data) {
            $list[$key][$child] = [];
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
        return $tree;
    }
}

if (!function_exists('makeFilePath')) {
    /**
     * 生成文件路径.
     * 
     * @param  string $rule
     * @param  int $userID
     * 
     * @return string
     */
    function makeFilePath($rule)
    {
        $policy = array_filter([
            '{year}' => date('Y'),
            '{month}' => date('m'),
            '{day}' => date('d'),
            '{date}' => date('Ymd'),
            '{staff_sn}' => request()->user()->staff_sn ?? '',
            '{shop_sn}' => request()->user()->shop_sn ?? 'notshopsn',
        ]);
        return trim(strtr($rule, $policy), '/').'/';
    }
}

if (!function_exists('makeFileName')) {
    /**
     * 生成文件名.
     * 
     * @param  string $rule     生成规则
     * @param  string $symbol   分类编号
     * @param  string $origin   原文名称
     * 
     * @return string
     */
    function makeFileName($rule, $symbol = null, $origin = null)
    {
        $policy = array_filter([
            '{year}' => date('Y'),
            '{month}' => date('m'),
            '{day}' => date('d'),
            '{date}' => date('Ymd'),
            '{category}' => $symbol,
            '{originname}' => $origin,
            '{randomkey8}' => Str::random(8),
            '{randomkey16}' => Str::random(16),
            '{staff_sn}' => getAuthUser(),
            '{shop_sn}' => getAuthUser('shop_sn') ?: 'shopsn',
        ]);

        return strtr($rule, $policy);
    }
}

if (!function_exists('getAuthUser')) {
    /**
     * 获取用户信息.
     * 
     * @param  string $clumn
     * @return mixed
     */
    function getAuthUser($clumn = '')
    {
        if (!empty($clumn)) {
            return Auth::user()[$clumn];
        }
        return Auth::id();
    }
}

if (!function_exists('cacheclear')) {
    /**
     * 清除缓存.
     * 
     * @param  boolean $all 是否清除所有
     * @return void
     */
    function cacheclear($all = false)
    {
        if ($all === true) {
            Artisan::call('cache:clear');
        }
        // 上传服务器配置 (name => item)
        Cache::forget('policies_mapwithkeys');
        // 上传服务器配置（id => name）
        Cache::forget('policies_mapwithname');
    }
}