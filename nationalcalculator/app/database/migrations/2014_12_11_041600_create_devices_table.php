<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDevicesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('devices', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned();
			$table->enum('type', array('ios','android','windows'))->nullable();
			$table->string('device_number')->index();
			$table->string('auth_token', 100)->unique()->nullable();
			$table->string('push_token')->nullable();
			$table->string('user_agent')->nullable();
			$table->timestamps();
			$table->foreign('user_id')->references('id')
				->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{

		Schema::table('devices', function(Blueprint $table)
		{
			$table->dropForeign(['user_id']);
		});

		Schema::drop('devices');
	}

}
