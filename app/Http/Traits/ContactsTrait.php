<?php

namespace App\Http\Traits;
use App\Contact;

trait ContactsTrait {

	public function getContactsForSelect2() 
    {
		$contacts = Contact::where('status', 'active')->orderBy('name', 'desc')->get();

		$result = [];
        foreach ($contacts as $contact) {
            $result[] = [
                'id' => $contact->phone_no . '|' .  $contact->name,
                'text' => $contact->name . " <$contact->phone_no>"
            ];
        }
        return $result;
	}

    public function getContacts($id = 0) 
    {
        if (!$id) {
            $contacts = Contact::where('status', 'active')->orderBy('name', 'desc')->get();
        } else {
            $contacts = Contact::where('id', $id)->get();
        }
        return $contacts;
    }

    public function saveContact($data) 
    {
        if ($data['id']) {
            $contact = Contact::find($data['id']);
        } else {
            $contact = new Contact;
        }
        $contact->name = $data['name'];
        $contact->phone_no = $data['phone_no'];
        return $contact->save() ? ['success' => true] : ['success' => false];
    }

    public function deleteContact($id)
    {
         $contact = Contact::find($id);
         return $contact->delete() ? ['success' => true] : ['success' => false];
    }

}