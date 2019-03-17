<?php 

return [
	'file' => [
		'number' => '文件编号',
		'hash' => '文件hash',
		'mime' => '文件mime',
		'size' => '文件大小',
		'tags' => '标签',
		'tags.*' => '标签ID',
		'user_id' => '员工编号',
		'filename' => '保存文件名',
		'origin_name' => '文件名',
		'category_id' => '文件分类',
		'expired_at' => '过期时间',
	],
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
		'name' => '分类名称',
		'color' => '分类颜色',
		'rank' => '排序值',
	],
	'tags' => [
		'name' => '标签名称',
		'tag_category_id' => '标签分类',
		'description' => '标签说明',
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
	'mkfile' => [
		'tags.*' => '标签ID', 
        'cate_id' => '文件分类',
        'block_list' => '文件块',
        'filename' => '文件名',
        'filesize' => '文件大小',
        'extension' => '文件扩展',
	],
];