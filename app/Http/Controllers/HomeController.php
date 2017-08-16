<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    use \App\Http\Traits\ContactsTrait;
    use \App\Http\Traits\TwilioTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contacts = $this->getContactsForSelect2();
        return view('home')->with('contacts', $contacts);
    }

    public function send(Request $request)
    {
        $this->validate($request, [
            'contacts' => 'required|string',
            'message' => 'required|string',
            'mediaUrl.0' => 'nullable|url',
            'mediaUrl.1' => 'nullable|url'
        ]);

        $contactsString = $request->contacts;
        $contacts = explode('^', $contactsString);
        foreach ($contacts as $contact) {
            $contact = explode('|', $contact);
            $people[$contact[0]] = $contact[1];
        }

        $mediaUrl = isset($request->mediaUrl) ? $request->mediaUrl : [];
    
        $this->sendMessage($people, $request->message, $mediaUrl);

        return redirect()->route('home')->with('message', 'Message sent!');
    }
}