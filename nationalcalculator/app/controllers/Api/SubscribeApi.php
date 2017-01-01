<?php

namespace Controller\Api;

use Auth;
use Controller\Exceptions\ValidationException;
use DB;
use Config;
use Carbon\Carbon;
use Models; //PHPStorm autocomplete wants this... :/
use Mail;
use Response;
use Input;
use Flash;
use Validator;
use Illuminate\Support\Arr;

class SubscribeApi extends BaseApi {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex()
	{
        return ['subscribe'=>['list goes here']];
		//
	}

    /**
     * Send Subscribe
     */
    public function postIndex() {
        //DB::beginTransaction();
        //can't use transaction because the stripe user gets created on stripes end and that can't be undone

        /** @var Models\User $user */
        $user = Auth::user();

        $inputs = Input::get('subscribe', []);
        $inputs['plan'] = Input::get('subscribe.plan', Input::get('subscribe.swap_plan', []));
        $update_plan = Input::get('subscribe.update_plan', false);
        $update_card = Input::get('subscribe.update_card', false);

        $rules = [];


        if (!$user->hasStripeId() || !$user->subscribed() || $update_plan) {
            $rules['plan'] = 'required';
        }

        if (!$user->hasStripeId() || !$user->subscribed() || $update_card) {
            $rules['token'] = 'required';
        }

        $validator = Validator::make($inputs, $rules);

        if ( ($validator->fails()) ) {
            $this->setFormErrors('subscribe', $validator->messages());
        }

        if (empty($this->formErrors)) {
            try {
                if (!$user->hasStripeId()) {
                    //no stripe user, create user and add subscription
                    
                    $user->createAsStripeCustomer($inputs['token'], [
                        'email'    => $user->email,
                        'metadata' => [
                            'id'       => $user->id,
                            'username' => $user->username
                        ]
                    ]);
                    $subscription = $user->newSubscription('default', $inputs['plan'])
//                        ->trialDays(7)
                        ->create();
                    //grab actual trial end date from stripe
                    $stripeSubscriptionTrialEnd = $subscription->asStripeSubscription()->trial_end;
                    $subscription->trial_ends_at = Carbon::createFromTimestampUTC($stripeSubscriptionTrialEnd);
                    $subscription->save();

                    Flash::success("Subscription Success!");

                } elseif (!$user->subscribed()) {
                    //doesn't have subscription, add one
                    if ($user->subscriptions->count()) {
                        $trialDays = 0;
                    } else {
                        $trialDays = 7;
                    }
                    $subscription = $user->newSubscription('default', $inputs['plan'])
//                        ->trialDays($trialDays)
                        ->create($inputs['token']);
                    //grab actual trial end date from stripe
                    $stripeSubscriptionTrialEnd = $subscription->asStripeSubscription()->trial_end;
                    $subscription->trial_ends_at = Carbon::createFromTimestampUTC($stripeSubscriptionTrialEnd);
                    $subscription->save();

                    Flash::success("Subscription Success!");
                } else {

                    if ($update_card) {
                        $user->updateCard($inputs['token']);
                        Flash::success("Credit card info updated successfully!");
                    }
                    if ($update_plan) {
                        $user->subscription()->swap($inputs['plan']);
                        Flash::success("Subscription plan updated successfully!");
                    }
                }
            }
            catch (\Exception $e) {
                $this->setFormErrors('err_msg', $e->getMessage());
            }
        }


        if (empty($this->formErrors)) {
            //DB::commit();
//            $plan = $user->getStripePlanInfo();
//            $inputs['body'] = $inputs['message']; //message is reserved word in email templates
//            Mail::send('emails.subscribe', ['plan'=>$plan, 'user'=>$user], function($message) use ($inputs)
//            {
//                $message
//                    ->from($inputs['email'], $inputs['name'])
//                    ->to(Config::get('app.subscribe'), Config::get('app.name'))
//                    ->subject("National Calculator - Now subscribed");
//
//            });

            return [
                'success' => true,
            ];
        } else {
            //DB::rollBack();
            throw new ValidationException(['errors' => $this->formErrors]);
        }
    }

    public function putIndex() {
        //DB::beginTransaction();

        /** @var Models\User $user */
        $user = Auth::user();

        $resume = (bool)Input::get('subscribe.resume', false);

        try {
            if ($resume) {
                $user->subscription()->resume();
                Flash::success("Subscription has been resumed.  Glad to see you back!");
            }
        }
        catch (\Exception $e) {
            $this->setFormErrors('err_msg', $e->getMessage());
        }

        if (empty($this->formErrors)) {
            //DB::commit();
            return [
                'success' => true,
            ];
        } else {
            //DB::rollBack();
            throw new ValidationException(['errors' => $this->formErrors]);
        }
    }

    public function deleteIndex() {
        //DB::beginTransaction();

        /** @var Models\User $user */
        $user = Auth::user();

        $cancel = (bool)Input::get('subscribe.cancel', false);

        try {
            if ($cancel) {
                $user->subscription()->cancel();
                Flash::notice("Subscription has been cancelled.  Sorry to see you go.");
            }
        }
        catch (\Exception $e) {
            throw $e;
            //$this->setFormErrors('err_msg', $e->getMessage());
        }

        if (empty($this->formErrors)) {
            //DB::commit();
            return [
                'success' => true,
            ];
        } else {
            //DB::rollBack();
            throw new ValidationException(['errors' => $this->formErrors]);
        }
    }

}
