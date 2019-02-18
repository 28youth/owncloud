<?php 

use Illuminate\Database\Seeder;

class PolicyTableSeeder extends Seeder
{
	
	public function run()
	{
		$data = [
			'name' => '本地存储',
			'driver' => 'local',
			'username' => 'root',
		];

		\DB::table('policies')->insert($data);
	}
}