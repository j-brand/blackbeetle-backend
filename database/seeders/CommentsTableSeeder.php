<?php
use Carbon\Carbon;

use Illuminate\Database\Seeder;

class CommentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$post_id = 1;
    	for ($i=1; $i < 7; $i++) { 

    		DB::table('comments')->insert([
	            'post_id'	 => $post_id,
	            'user_id'    => 69,
	            'name'		 =>	str_random(20),
	            'content'	 => str_random(400),
	            'created_at' => Carbon::now(),
				'updated_at' => Carbon::now(),
        	]);
        	
			if($post_id == 1){
				$post_id  = 2;
			}else{
				$post_id  =1;
			}
		
    	}
        
    }
}
