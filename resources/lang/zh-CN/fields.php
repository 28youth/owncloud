<?php 

return [

	// 文件分类
	'category' => [
		'name' => '分类名称',
		'symbol' => '分类编号',
		'is_lock' => '是否锁定',
		'parent_id' => '父分类ID',
		'filetype' => '文件存储类型',
		'policy_id' => '文件存储路径',
		'dirrule' => '文件目录规则',
		'numberrule' => '文件编号规则',
		'max_size' => '上传最大大小',
		'is_expired' => '是否包含过期文件',
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

	'policies' => [
		'name' => '配置名称',
    	'driver' => '存储驱动',
    	'host' => '服务器IP',
    	'port' => '服务器端口',
    	'username' => '服务器用户名',
    	'root' => '服务器用户名',
    	'privatekey' => '服务器密钥',
    	'timeout' => '超时时间（秒）',
	],
];