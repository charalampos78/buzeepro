<?php

namespace Controller\Api;

use Auth;
use Controller\Exceptions\ValidationException;
use DB;
use Config;
use Models; //PHPStorm autocomplete wants this... :/
use Mail;
use Response;
use Input;
use Flash;
use Validator;
use Illuminate\Support\Arr;

class ContactApi extends BaseApi {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex()
	{
        return ['contact'=>['list goes here']];
		//
	}

    /**
     * Send Contact
     */
    public function postIndex() {

        $inputs = Input::get('contact', []);
        //$contact->fill($inputs);

        $rules = array(
            'name'       => 'required',
            'email'      => 'required|email',
            'subject'    => 'required',
            'message'    => 'required'
        );

        $validator = Validator::make($inputs, $rules);

        if ( ($validator->fails()) ) {
            $this->setFormErrors('contact', $validator->messages());
        }

        if (empty($this->formErrors)) {

            //send email
			if (Auth::check()) {
                $inputs['username'] = Auth::user()->username;
			} else {
                $inputs['username'] = false;
            }

            $inputs['body'] = $inputs['message']; //message is reserved word in email templates
            Mail::send('emails.contact', $inputs, function($message) use ($inputs)
            {
                $message
                    ->from($inputs['email'], $inputs['name'])
                    ->to(Config::get('app.contact'), Config::get('app.name'))
                    ->subject("New Contact Form Email");

            });

            return [
                'success' => true,
                'flash_msg' => Flash::get_flash(),
            ];
        } else {
            DB::rollBack();
            throw new ValidationException(['errors' => $this->formErrors]);
        }
    }

}
