<?php 

return [

	// 文件分类
	'category' => [
		'name' => '分类名称',
		'symbol' => '分类编号',
		'parent_id' => '父分类ID',
		'is_lock' => '是否锁定',
		'config_number' => '文件编号配置',
		'config_number.*' => '文件编号配置',
		'config_operate' => '文件操作配置',
		'config_operate.*' => '文件操作配置',
		'config_ability' => '文件权限配置',
		'config_ability.*' => '文件权限配置',
		'config_format' => '文件格式配置',
		'config_format.*' => '文件格式配置',
		'config_path' => '文件路径配置',
		'description' => '分类备注',
	],

	// 权限
	'ability' => [
		'name' => '权限名',
		'parent_id' => '上级ID',
		'is_lock' => '是否锁定',
		'sort' => '排序值',
	],

	// 角色
	'role' => [
		'name' => '角色名',
		'abilities' => '权限',
		'abilities.*' => '权限ID',
		'categories' => '分类',
		'categories.*' => '分类ID',
		'staff' => '员工'
	],
	'tag_categories' => [
		'name' => '名称',
		'color' => '验证',
		'rank' => '排序值',
	],
];