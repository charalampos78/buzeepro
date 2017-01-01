<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateZipImportTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('zip_import', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('county', 128)->nullable()->index('county_INDEX');
			$table->string('zip', 45)->nullable()->index('zip_INDEX');
			$table->char('zip_type', 1)->nullable();
			$table->string('fips_code', 45)->nullable();
			$table->string('city', 128)->nullable();
			$table->string('st', 45)->nullable()->index('st_INDEX');
			$table->string('addy_count', 45)->nullable();
			$table->char('primary_county', 1)->nullable();
			$table->char('multi_county', 1)->nullable();
			$table->integer('county_id')->nullable()->index('county_id_INDEX');
			$table->integer('state_id')->nullable()->index('state_id_INDEX');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('zip_import');
	}

}
