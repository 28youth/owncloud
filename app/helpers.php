<?php 

use Illuminate\Validation\Rule;

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
            '{randomkey8}' => str_random(8),
            '{randomkey16}' => str_random(16),
            '{staff_sn}' => request()->user()->staff_sn ?? '',
        ]);

        return strtr($rule, $policy);
    }
}