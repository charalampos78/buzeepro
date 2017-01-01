<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCollectorsImportTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('collectors_import', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('county_id')->unsigned()->nullable()->index('county_id_INDEX');
			$table->integer('state_id')->unsigned()->nullable()->index('state_id_INDEX');
			$table->string('county', 128)->nullable()->index('county_INDEX');;
			$table->string('st', 3)->nullable()->index('st_INDEX');
			$table->string('office', 128)->nullable();
			$table->string('municipality', 128)->nullable();
			$table->string('commissioner', 256)->nullable();
			$table->string('com2', 128)->nullable();
			$table->string('address', 128)->nullable();
			$table->string('city', 128)->nullable();
			$table->string('state', 3)->nullable();
			$table->string('zip', 10)->nullable();
			$table->string('email', 128)->nullable();
			$table->string('phone', 30)->nullable();
			$table->string('phone2', 30)->nullable();
			$table->string('fax', 30)->nullable();
			$table->string('fax2', 30)->nullable();
			$table->string('website', 256)->nullable();
			$table->string('paysite', 256)->nullable();
			$table->string('m_address', 128)->nullable();
			$table->string('m_city', 128)->nullable();
			$table->string('m_state', 3)->nullable();
			$table->string('m_zip', 10)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('collectors_import');
	}

}
