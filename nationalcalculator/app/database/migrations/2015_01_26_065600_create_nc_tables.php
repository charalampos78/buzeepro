<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateNcTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{

		Schema::create('states', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('abbr', 2)->index('abbr_INDEX');
			$table->string('name', 45);
			$table->decimal('owner_min', 5)->default(0.00);
			$table->decimal('owner_extra', 5)->default(0.00);
			$table->decimal('owner_simultaneous', 5)->default(0.00);
			$table->decimal('lender_min', 5)->default(0.00);
			$table->decimal('lender_extra', 5)->default(0.00);
			$table->decimal('lender_simultaneous', 5)->default(0.00);
			$table->boolean('status_flag')->default(1);
			$table->timestamps();
			$table->softDeletes();
		});

		Schema::create('counties', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('state_id')->unsigned();
			$table->string('name', 45)->index('name_INDEX');;
			$table->string('fips_code', 45)->nullable()->index('fips_code_INDEX');
			$table->text('note')->nullable();
			$table->boolean('status_flag')->default(1);
			$table->timestamps();
			$table->softDeletes();
			$table->foreign('state_id')->references('id')->on('states')
				->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});

		Schema::create('zips', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('zip', 45)->index('zip_INDEX');
			$table->string('city', 128)->nullable();
			$table->char('zip_type', 1)->nullable();
			$table->boolean('primary_county')->default(0);
			$table->boolean('multi_county')->nullable()->default(0);
			$table->integer('county_id')->unsigned();
			$table->integer('state_id')->unsigned();
			$table->boolean('status_flag')->default(1);
			$table->timestamps();
			$table->foreign('county_id')->references('id')->on('counties')
				->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('state_id')->references('id')->on('states')
				->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});

		Schema::create('miscs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('state_id')->unsigned();
			$table->string('name', 128);
			$table->decimal('price', 6);
			$table->foreign('state_id')->references('id')->on('states')
				->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});

		Schema::create('endorsements', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('state_id')->unsigned();
			$table->string('name', 128);
			$table->boolean('standard_flag')->default(0);
			$table->enum('type', array('fixed','percent'));
			$table->decimal('amount', 6);
			$table->timestamps();
			$table->foreign('state_id')->references('id')->on('states')
				->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});

		Schema::create('rates', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('state_id')->unsigned();
			$table->boolean('default_flag')->default(1);
			$table->decimal('percent', 11, 6);
			$table->decimal('extra', 11, 2)->default(0);
			$table->integer('range_min');
			$table->integer('range_max');
			$table->enum('type', array('owner','lender'));
			$table->foreign('state_id')->references('id')->on('states')
				->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});

		Schema::create('rate_counties', function(Blueprint $table)
		{
			$table->integer('rate_id')->unsigned();
			$table->integer('county_id')->unsigned();
			$table->primary(['rate_id','county_id']);
			$table->foreign('county_id')->references('id')->on('counties')
				->onUpdate('NO ACTION')->onDelete('CASCADE');
			$table->foreign('rate_id')->references('id')->on('rates')
				->onUpdate('NO ACTION')->onDelete('CASCADE');
		});

		Schema::create('documents', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('county_id')->unsigned();
			$table->string('name', 45)->index('name_INDEX');
			$table->decimal('price_first', 6);
			$table->string('price_text', 128)->default('First Page');
			$table->integer('price_count')->default(1);
			$table->decimal('price_additional', 6)->nullable();
			$table->boolean('status_flag')->default(1);
			$table->timestamps();
			$table->softDeletes();
			$table->foreign('county_id')->references('id')->on('counties')
				->onUpdate('NO ACTION')->onDelete('CASCADE');
		});

		Schema::create('document_tax', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('document_id')->unsigned();
			$table->string('name', 128)->index('name_INDEX');
			$table->decimal('percent', 11, 6);
			$table->enum('type', array('loan','sales','fixed','sales-loan'));
			$table->foreign('document_id')->references('id')->on('documents')
				->onUpdate('NO ACTION')->onDelete('CASCADE');
		});

		Schema::create('tax_collectors', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('county_id')->unsigned();
			$table->string('municipality', 128)->nullable();
			$table->string('commissioner', 256)->nullable();
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
			$table->timestamps();
			$table->foreign('county_id')->references('id')->on('counties')
				->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});

		Schema::create('notebooks', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned();
			$table->integer('zip_id')->unsigned();
			$table->integer('county_id')->unsigned();
			$table->string('name', 128)->nullable();
			$table->enum('type', array('purchase','cash','refinance'));
			$table->integer('purchase_price')->default(0);
			$table->integer('loan_amount')->nullable();
			$table->timestamps();
			$table->softDeletes();
			$table->foreign('county_id')->references('id')->on('counties')
				->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('user_id')->references('id')->on('users')
				->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('zip_id')->references('id')->on('zips')
				->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});

		Schema::create('notebook_miscs', function(Blueprint $table)
		{
			$table->integer('notebook_id')->unsigned();
			$table->integer('misc_id')->unsigned();
			$table->primary(['notebook_id','misc_id']);
			$table->foreign('misc_id')->references('id')->on('miscs')
				->onUpdate('NO ACTION')->onDelete('CASCADE');
			$table->foreign('notebook_id')->references('id')->on('notebooks')
				->onUpdate('NO ACTION')->onDelete('CASCADE');
		});

		Schema::create('notebook_endorsements', function(Blueprint $table)
		{
			$table->integer('notebook_id')->unsigned();
			$table->integer('endorsement_id')->unsigned();
			$table->primary(['notebook_id','endorsement_id']);
			$table->foreign('endorsement_id')->references('id')->on('endorsements')
				->onUpdate('NO ACTION')->onDelete('CASCADE');
			$table->foreign('notebook_id')->references('id')->on('notebooks')
				->onUpdate('NO ACTION')->onDelete('CASCADE');
		});

		Schema::create('notebook_documents', function(Blueprint $table)
		{
			$table->integer('notebook_id')->unsigned();
			$table->integer('document_id')->unsigned();
			$table->integer('pages')->default(1);
			$table->primary(['notebook_id','document_id']);
			$table->foreign('document_id')->references('id')->on('documents')
				->onUpdate('NO ACTION')->onDelete('CASCADE');
			$table->foreign('notebook_id')->references('id')->on('notebooks')
				->onUpdate('NO ACTION')->onDelete('CASCADE');
		});

	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{

		Schema::table('zips', function(Blueprint $table)
		{
			$table->dropForeign(['county_id']);
			$table->dropForeign(['state_id']);
		});

		Schema::table('counties', function(Blueprint $table)
		{
			$table->dropForeign(['state_id']);
		});

		Schema::table('miscs', function(Blueprint $table)
		{
			$table->dropForeign(['state_id']);
		});

		Schema::table('endorsements', function(Blueprint $table)
		{
			$table->dropForeign(['state_id']);
		});

		Schema::table('rates', function(Blueprint $table)
		{
			$table->dropForeign(['state_id']);
		});

		Schema::table('rate_counties', function(Blueprint $table)
		{
			$table->dropForeign(['county_id']);
			$table->dropForeign(['rate_id']);
		});

		Schema::table('documents', function(Blueprint $table)
		{
			$table->dropForeign(['county_id']);
		});

		Schema::table('document_tax', function(Blueprint $table)
		{
			$table->dropForeign(['document_id']);
		});

		Schema::table('tax_collectors', function(Blueprint $table)
		{
			$table->dropForeign(['county_id']);
		});

		Schema::table('notebooks', function(Blueprint $table)
		{
			$table->dropForeign(['county_id']);
			$table->dropForeign(['user_id']);
			$table->dropForeign(['zip_id']);
		});

		Schema::table('notebook_miscs', function(Blueprint $table)
		{
			$table->dropForeign(['misc_id']);
			$table->dropForeign(['notebook_id']);
		});

		Schema::table('notebook_endorsements', function(Blueprint $table)
		{
			$table->dropForeign(['endorsement_id']);
			$table->dropForeign(['notebook_id']);
		});

		Schema::table('notebook_documents', function(Blueprint $table)
		{
			$table->dropForeign(['document_id']);
			$table->dropForeign(['notebook_id']);
		});

		Schema::drop('notebook_documents');
		Schema::drop('notebook_endorsements');
		Schema::drop('notebook_miscs');
		Schema::drop('notebooks');
		Schema::drop('tax_collectors');
		Schema::drop('document_tax');
		Schema::drop('documents');
		Schema::drop('rate_counties');
		Schema::drop('rates');
		Schema::drop('endorsements');
		Schema::drop('miscs');
		Schema::drop('zips');
		Schema::drop('counties');
		Schema::drop('states');
	}

}
