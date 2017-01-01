<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCashierColumns extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('users', function(Blueprint $table)
		{
			$table->timestamp('subscription_ends_at')->after('confirmed')->nullable();
			$table->timestamp('trial_ends_at')->after('confirmed')->nullable();
			$table->string('last_four', 4)->after('confirmed')->nullable();
			$table->string('stripe_plan', 25)->after('confirmed')->nullable();
			$table->string('stripe_subscription')->after('confirmed')->nullable();
			$table->string('stripe_id')->after('confirmed')->nullable();
			$table->tinyInteger('stripe_active')->after('confirmed')->default(0);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('users', function(Blueprint $table)
		{
			$table->dropColumn(
				'stripe_active', 'stripe_id', 'stripe_subscription', 'stripe_plan', 'last_four', 'trial_ends_at', 'subscription_ends_at'
			);
		});
	}

}
