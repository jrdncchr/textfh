<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('users')->truncate();

		factory(App\User::class, 10)->create();

		$user = App\User::find(1);
		$user->name = "Jordan Cachero";
		$user->email = "cacherojordan@gmail.com";
		$user->save();
    }
}
