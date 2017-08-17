<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContactController extends Controller
{
    use \App\Http\Traits\ContactsTrait;
    use \App\Http\Traits\SpreadsheetTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('contacts');
    }

    public function ajax(Request $request)
    {
        $action = $request->action;
        switch ($action) {
            case 'list' :
                $contacts = $this->getContacts();
                echo json_encode(['data' => $contacts]);
                break;
            case 'save' :
                $contact = $request->contact;
                $this->validate($request, [
                    'contact.name' => 'required|max:255',
                    'contact.phone_no' => 'unique:contacts,phone_no,' . $contact['id'] . ',id|required'
                ]);
                $result = $this->saveContact($contact);
                echo json_encode($result);
                break;
            case 'delete' :
                $id = $request->id;
                $result = $this->deleteContact($id);
                echo json_encode($result);
                break;
            default :
                echo json_encode(['success' => false, 'message' => 'Action not found.']);
        }
    }

    public function import(Request $request)
    {
        $result = $this->bulkImport($request->file->getPathName());
        return view('contacts')->with($result);
    }

}