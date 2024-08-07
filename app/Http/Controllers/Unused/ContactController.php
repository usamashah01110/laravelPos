<?php

namespace App\Http\Controllers\Unused;

use App\Exports\ContactsExport;
use App\Http\Controllers\Controller;
use App\Imports\ContactsImport;
use App\Mail\CareerEmail;
use App\Mail\ContactEmail;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ContactController extends Controller
{
    public function contactView()
    {
        return view('contact.contactUs');
    }

    public function contactSubmit(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:255',
            'email' => 'required|string|email|max:255|unique:contacts|regex:/@.*\./',
            'phone' => 'required|unique:contacts|regex:/^03[0-9]{9}$/|min:11',
            'city' => 'required|string',
            'message' => 'required|string'
        ]);
        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate)->withInput($request->input());
        }
        if ($request->method() == 'POST') {
            $data['title'] = 'New User';
            $contact = new Contact();
            $contact['name'] = $request['name'];
            $contact['email'] = $request['email'];
            $contact['phone'] = $request['phone'];
            $contact['city'] = $request['city'];
            $contact['message'] = $request['message'];
            $contact->save();
            $data['data'] = $contact;
            $data['email'] = env('Receiver_Email');
            Mail::to($data['email'])->send(new ContactEmail($data));
            session()->flash('success', 'Form submitted successfully!');
            return redirect()->back();
        }
    }

    public function contactsList()
    {
        $allContacts = Contact::all();
        return view('contact.contactsList', compact('allContacts'));
    }

    public function contactDelete($id)
    {
        $data = Contact::find($id);
        $data->delete();
        session()->flash('success', 'Record successfully deleted.');
        return redirect()->back();
    }

    public function contactEdit($id)
    {
        $contactId = $id;
        $allContacts = Contact::findOrFail($id);

        if ($allContacts) {
            return view('contact.contactUpdate', compact('allContacts', 'contactId'));
        } else {
            session()->flash('alert', 'Record not found.');
            return redirect()->back();
        }
    }


    public function submitUpdateContact(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:255',
            'email' => 'required|string|email|max:255',
            'phone' => 'required|unique:users|regex:/^03[0-9]{9}$/|min:11',
            'city' => 'required|string',
            'message' => 'required|string'
        ]);
        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate)->withInput($request->input());
        }
        Contact::updateOrCreate([
            'id' => $id
        ], [
            'name' => $request['name'],
            'email' => $request['email'],
            'phone' => $request['phone'],
            'city' => $request['city'],
            'message' => $request['message']
        ]);
        session()->flash('success', 'Record successfully updated.');
        return redirect('/contact/list');
    }

    /**
     * @return \Illuminate\Support\Collection
     */

    public function export()

    {

        return Excel::download(new ContactsExport, 'Contacts.xlsx');
//        return Excel::download(new ContactsExport, 'Contacts.xls');
//        return Excel::download(new ContactsExport, 'Contacts.xlsm');
//        return Excel::download(new ContactsExport, 'Contacts.xlsb');
//        return Excel::download(new ContactsExport, 'Contacts.csv');
//        return Excel::download(new ContactsExport, 'Contacts.ods');
//        return Excel::download(new ContactsExport, 'Contacts.xml');

    }


    /**
     * @return \Illuminate\Support\Collection
     */

    public function import(Request $request)
    {
//        $validator = Validator::make($request->all(), [
//            'file' => 'required|mimes:xlsx,xls',
//        ]);
//        if ($validator->fails()) {
//            session()->flash('errors-import', $validator->errors()->get('file'));
//            return redirect('/contact/list');
//        }
//        if (!$validator->fails()) {
//            $path = $request->file('file')->getRealPath();
//            $rows = Excel::toArray([], $path);
//
//            $failedRows = [];
//
//            foreach ($rows[0] as $index => $row) {
//                $validator = Validator::make($row, [
//                    '*.name' => 'required|string|min:2|max:255',
//                    '*.email' => 'required|string|email|max:255|unique:users',
//                    '*.phone' => 'required|unique:users|regex:/^([0-9\s\-\+\(\)]*)$/|min:12',
//                    '*.city' => 'required|string|min:2|max:255',
//                    '*.message' => 'required',
//                ]);
//
//                if ($validator->fails()) {
//                    $failedRows[$index] = $validator->errors()->all();
//                    dd($failedRows);
//                }
//            }
//
////           $er =  $validator->sometimes('file', [new ExcelDataValidation()], function ($input) {
////                return !empty($input['file']);
////            });
////           dd($er);
//            $validationMessages = [];
//            if ($validator->fails()) {
//                $validationMessages = array_merge($validationMessages, $validator->errors()->all());
//            }
////
//            if (!empty($validationMessages)) {
//                session()->flash('errors-import', $validationMessages);
//                return redirect('/contact/list');
//            }
            Excel::import(new ContactsImport, $request->file('file'));
            return back();

//        }

//        Excel::import(new ContactsImport, $request->file('file'));
//        return back();

//        $validationMessages = [];
//
//        if ($validator->fails()) {
//            $validationMessages = array_merge($validationMessages, $validator->errors()->all());
//        }
//
//        if (!empty($validationMessages)) {
//            session()->flash('errors-import', $validationMessages);
//            return redirect('/contact/list');
//        }


        //===========================
//
//        $validationMessages = [];
//
//        $inputData = $request->all();
////        dd($inputData);
//        $validator = Validator::make($inputData, [
//            'name' => 'required',
//            'email' => 'required|email',
//            'phone' => 'required',
//            'city' => 'required',
//            'message' => 'required',
//        ],
////            [
////            'name.required'=> 'Name column data dose not exist in file.',
////            'email.required'=> 'Email column data dose not exist in file.',
////            'phone.required'=> 'Phone column data dose not exist in file.',
////            'city.required'=> 'City column data dose not exist in file.',
////            'message.required'=> 'Message column data dose not exist in file.',
////        ]
//        );
//
//        if ($validator->fails()) {
//            $validationMessages = array_merge($validationMessages, $validator->errors()->all());
//        }
//
//        if (!empty($validationMessages)) {
//            session()->flash('errors-import', $validationMessages);
//            return redirect('/contact/list');
//        }
//        try {
//            Excel::import(new ContactsImport, $request->file('file'));
//        } catch (Exception $e) {
//            $validationMessages[] = $e->getMessage();
//        }
//        return back();
    }

}
