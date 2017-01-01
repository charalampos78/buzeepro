<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class StripeLaravelBackport extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//update old stripe columns to new ones
		Schema::table('users', function (Blueprint $table) {
			//$table->string('stripe_id')->nullable();
			$table->string('card_brand')->after('stripe_id')->nullable();
			//$table->string('card_last_four')->nullable();
			$table->renameColumn("last_four", "card_last_four");
		});

		Schema::create('subscriptions', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id');
			$table->string('name');
			$table->string('stripe_id');
			$table->string('stripe_plan');
			$table->integer('quantity');
			$table->timestamp('trial_ends_at')->nullable();
			$table->timestamp('ends_at')->nullable();
			$table->timestamps();
		});

		$users = DB::table("users")->whereNotNull("stripe_plan")->get();
		foreach ($users as $user) {
			/** @var Models\User $user */
			DB::table("subscriptions")->insert([
				"user_id" => $user->id,
				"name" => $user->stripe_plan,
				"stripe_id" => $user->stripe_subscription,
				"stripe_plan" => $user->stripe_plan,
				"trial_ends_at" => $user->trial_ends_at,
				"ends_at" => $user->subscription_ends_at
			]);
		};

		Schema::table('users', function(Blueprint $table)
		{
			$table->dropColumn('stripe_plan');
			$table->dropColumn('trial_ends_at');
			$table->dropColumn('stripe_subscription');
			$table->dropColumn('subscription_ends_at');
			$table->dropColumn('stripe_active');
		});

	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('users', function (Blueprint $table) {
			$table->dropColumn('card_brand');
			$table->renameColumn("card_last_four", "last_four");

			$table->tinyInteger('stripe_active')->after('confirmed')->default(0);

			$table->timestamp('subscription_ends_at')->after('card_last_four')->nullable();
			$table->timestamp('trial_ends_at')->after('card_last_four')->nullable();

			$table->string('stripe_plan', 25)->after('stripe_id')->nullable();
			$table->string('stripe_subscription')->after('stripe_id')->nullable();
		});

		$subscriptions = DB::table("subscriptions")->get();
		foreach ($subscriptions as $subscription) {
			/** @var Models\User $user */
			DB::table("users")->where("id", "=", $subscription->user_id)->update([
				"stripe_plan" => $subscription->stripe_plan,
				"trial_ends_at" => $subscription->trial_ends_at,
				"stripe_subscription" => $subscription->stripe_id,
				"subscription_ends_at" => $subscription->ends_at,
				"stripe_active" => "1"
			]);
		};

		Schema::drop('subscriptions');

	}

}
