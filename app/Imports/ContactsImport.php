<?php

namespace App\Imports;

use App\Models\Contact;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ContactsImport implements ToCollection, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    public function collection(Collection $rows)

    {
        if ($rows) {
            $validationMessages = [];
            $validate = Validator::make($rows->toArray(), [
                '*.name' => 'required',
                '*.email' => 'required',
                '*.phone' => 'required',
                '*.city' => 'required',
                '*.message' => 'required',
            ]);
//            ])->validate();
            if ($validate->fails()) {
                $validationMessages = array_merge($validationMessages, $validate->errors()->all());
            }
//
            if (!empty($validationMessages)) {
                session()->flash('errors-import', $validationMessages);
                return redirect('/contact/list');
            }
        }

        foreach ($rows as $row) {

            Contact::create([

                'name' => $row['name'],

                'email' => $row['email'],

                'phone' => $row['phone'],

                'city' => $row['city'],

                'message' => $row['message'],

            ]);

        }

    }
}
