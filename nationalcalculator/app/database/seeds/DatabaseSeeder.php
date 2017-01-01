<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		$this->call('RoleTableSeeder');
		$this->call('AdminSeeder');
		$this->call('StateSeeder');
	}

}

class RoleTableSeeder extends Seeder {

	public function run()
	{
		DB::table('roles')->delete();

		Models\Role::create(array('id' => 1, 'name' => 'login', 'description' => 'Default user privilege'));
		Models\Role::create(array('id' => 2, 'name' => 'admin', 'description' => 'Administration of site'));
		//DB::connection()->getSchemaBuilder()->getColumnListing("users");
	}

}

class AdminSeeder extends Seeder {

	public function run()
	{
		DB::table('users')->delete();

		$user = Models\User::create(array('id' => 1, 'email' => 'admin@site.com', 'username' => 'admin', 'password' => 'password', /*'password_confirmation' => 'password',*/ 'confirmed' => 1));

		$user->roles()->sync(array(1,2));


	}

}

class StateSeeder extends Seeder {

	public function run()
	{
		DB::table('states')->delete();

		$states = [
			['name' => 'Alabama', 'abbr' => 'AL', 'status_flag' => 0],
			['name' => 'Alaska', 'abbr' => 'AK', 'status_flag' => 0],
			['name' => 'Arizona', 'abbr' => 'AZ', 'status_flag' => 0],
			['name' => 'Arkansas', 'abbr' => 'AR', 'status_flag' => 0],
			['name' => 'California', 'abbr' => 'CA', 'status_flag' => 0],
			['name' => 'Colorado', 'abbr' => 'CO', 'status_flag' => 0],
			['name' => 'Connecticut', 'abbr' => 'CT', 'status_flag' => 0],
			['name' => 'Delaware', 'abbr' => 'DE', 'status_flag' => 0],
			['name' => 'District of Columbia', 'abbr' => 'DC', 'status_flag' => 0],
			['name' => 'Florida', 'abbr' => 'FL', 'status_flag' => 0],
			['name' => 'Georgia', 'abbr' => 'GA', 'status_flag' => 0],
			['name' => 'Hawaii', 'abbr' => 'HI', 'status_flag' => 0],
			['name' => 'Idaho', 'abbr' => 'ID', 'status_flag' => 0],
			['name' => 'Illinois', 'abbr' => 'IL', 'status_flag' => 0],
			['name' => 'Indiana', 'abbr' => 'IN', 'status_flag' => 0],
			['name' => 'Iowa', 'abbr' => 'IA', 'status_flag' => 0],
			['name' => 'Kansas', 'abbr' => 'KS', 'status_flag' => 0],
			['name' => 'Kentucky', 'abbr' => 'KY', 'status_flag' => 0],
			['name' => 'Louisiana', 'abbr' => 'LA', 'status_flag' => 0],
			['name' => 'Maine', 'abbr' => 'ME', 'status_flag' => 0],
			['name' => 'Maryland', 'abbr' => 'MD', 'status_flag' => 0],
			['name' => 'Massachusetts', 'abbr' => 'MA', 'status_flag' => 0],
			['name' => 'Michigan', 'abbr' => 'MI', 'status_flag' => 0],
			['name' => 'Minnesota', 'abbr' => 'MN', 'status_flag' => 0],
			['name' => 'Mississippi', 'abbr' => 'MS', 'status_flag' => 0],
			['name' => 'Missouri', 'abbr' => 'MO', 'status_flag' => 0],
			['name' => 'Montana', 'abbr' => 'MT', 'status_flag' => 0],
			['name' => 'Nebraska', 'abbr' => 'NE', 'status_flag' => 0],
			['name' => 'Nevada', 'abbr' => 'NV', 'status_flag' => 0],
			['name' => 'New Hampshire', 'abbr' => 'NH', 'status_flag' => 0],
			['name' => 'New Jersey', 'abbr' => 'NJ', 'status_flag' => 0],
			['name' => 'New Mexico', 'abbr' => 'NM', 'status_flag' => 0],
			['name' => 'New York', 'abbr' => 'NY', 'status_flag' => 0],
			['name' => 'North Carolina', 'abbr' => 'NC', 'status_flag' => 0],
			['name' => 'North Dakota', 'abbr' => 'ND', 'status_flag' => 0],
			['name' => 'Ohio', 'abbr' => 'OH', 'status_flag' => 0],
			['name' => 'Oklahoma', 'abbr' => 'OK', 'status_flag' => 0],
			['name' => 'Oregon', 'abbr' => 'OR', 'status_flag' => 0],
			['name' => 'Pennsylvania', 'abbr' => 'PA', 'status_flag' => 0],
			['name' => 'Rhode Island', 'abbr' => 'RI', 'status_flag' => 0],
			['name' => 'South Carolina', 'abbr' => 'SC', 'status_flag' => 0],
			['name' => 'South Dakota', 'abbr' => 'SD', 'status_flag' => 0],
			['name' => 'Tennessee', 'abbr' => 'TN', 'status_flag' => 0],
			['name' => 'Texas', 'abbr' => 'TX', 'status_flag' => 0],
			['name' => 'Utah', 'abbr' => 'UT', 'status_flag' => 0],
			['name' => 'Vermont', 'abbr' => 'VT', 'status_flag' => 0],
			['name' => 'Virginia', 'abbr' => 'VA', 'status_flag' => 0],
			['name' => 'Washington', 'abbr' => 'WA', 'status_flag' => 0],
			['name' => 'West Virginia', 'abbr' => 'WV', 'status_flag' => 0],
			['name' => 'Wisconsin', 'abbr' => 'WI', 'status_flag' => 0],
			['name' => 'Wyoming', 'abbr' => 'WY', 'status_flag' => 0],
		];
		DB::table('states')->insert($states);

		DB::table('states')->update(['created_at'=> Carbon\Carbon::now(), 'updated_at' => Carbon\Carbon::now()]);

		$user = Models\User::create(array('id' => 1, 'email' => 'admin@site.com', 'username' => 'admin', 'password' => 'password', /*'password_confirmation' => 'password',*/ 'confirmed' => 1));

		$user->roles()->sync(array(1,2));


	}

}
