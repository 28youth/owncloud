<?php 

use Illuminate\Database\Seeder;

class FileTypeSeeder extends Seeder
{
    
    public function run()
    {   
        // 文件类型一级大类
        $data = [
            ['id' => 1, 'name' => 'online', 'remark' => '在线文档'],
            ['id' => 2, 'name' => 'image', 'remark' => '图片'],
            ['id' => 3, 'name' => 'video', 'remark' => '视频'],
            ['id' => 4, 'name' => 'audio', 'remark' => '音频'],
            ['id' => 5, 'name' => 'table', 'remark' => '表格'],
        ];
        $children = [
            // 图片类型
            ['name' => 'gif', 'parent_id' => 2],
            ['name' => 'png', 'parent_id' => 2],
            ['name' => 'svg', 'parent_id' => 2],
            ['name' => 'bmp', 'parent_id' => 2],
            ['name' => 'webp', 'parent_id' => 2],
            ['name' => 'jpeg', 'parent_id' => 2],

            // 视频类型
            ['name' => 'mp4', 'parent_id' => 3],
            ['name' => 'avi', 'parent_id' => 3],
            ['name' => 'wmv', 'parent_id' => 3],
            ['name' => 'mpeg', 'parent_id' => 3],
            ['name' => 'rmvb', 'parent_id' => 3],

            // 音频类型
            ['name' => 'mp3', 'parent_id' => 4],
            ['name' => 'acc', 'parent_id' => 4],
            ['name' => 'dts', 'parent_id' => 4],
            ['name' => 'ogg', 'parent_id' => 4],
            ['name' => 'wav', 'parent_id' => 4],
            ['name' => 'aac', 'parent_id' => 4],

            // 表格类型
            ['name' => 'xls', 'parent_id' => 5],
            ['name' => 'csv', 'parent_id' => 5],
            ['name' => 'xlsx', 'parent_id' => 5],
        ];

        $data = array_merge($data, $children);

        \DB::table('file_types')->insert($data);
    }
}