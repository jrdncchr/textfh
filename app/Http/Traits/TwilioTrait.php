<?php
namespace App\Http\Traits;

use Twilio\Rest\Client;

trait TwilioTrait {

	public function sendMessage($contacts, $message, $media = []) 
	{
        $AccountSid = env('TWILIO_ACCOUNT_SID');
        $AuthToken = env('TWILIO_AUTH_TOKEN');

        $client = new Client($AccountSid, $AuthToken);

        $media = array_filter($media, function($var) { return !is_null($var); } );

        foreach ($contacts as $number => $name) {
            $sms = $client->account->messages->create(
                $number,
                array(
                    'from' => env('TWILIO_PHONE_NO'), 
                    'body' => $message,
                    'mediaUrl' => $media
                )
            );
        }
	}

}