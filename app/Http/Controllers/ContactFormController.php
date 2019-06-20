<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMessage as ContactMail;
use Illuminate\Support\Facades\Validator;

class ContactFormController extends Controller
{
    public function receiveMessage(Request $request){

        $validator = Validator::make($request->all(), [
            'name'          => 'required|string',
            'email'         => 'required|email',
            'phone'         => 'required|celular_com_ddd',
            'message'       => 'required',
            'attached_file' => 'required|mimes:docx,doc,pdf,odt,txt|max:500'
        ], 
        ['celular_com_ddd' => 'O campo :attribute está com valor inválido']);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $data = [
            'name'          => $request->get('name'),
            'email'         => $request->get('email'),
            'phone'         => $request->get('phone'),
            'ip'            => $request->ip(),
            'message'       => $request->get('message')
        ];

        $contact = new ContactMessage;
        $contact->name          = $data['name'];
        $contact->email         = $data['email'];
        $contact->phone         = $data['phone'];
        $contact->message       = $data['message'];
        $contact->ip            = $data['ip'];
        $contact->save();

        $file_path = Storage::put('public/uploaded/messages/' . $data['email'], $request->file('attached_file'));

        $contact->update([
            'attached_file' => Storage::url($file_path)
        ]);

        $myEmail = $contact->email;

        try {
            Mail::to($myEmail)->send(new ContactMail([
                'name'      => $contact->name,
                'email'     => $contact->email,
                'message'   => $contact->message,
                'file_path' => trim($contact->attached_file, '/')
            ]));

            return response()
            ->json(['status' => 'success']);
        } catch (\Throwable $th) {
            return response()
            ->json(['status' => 'fail']);   
        }
    }
}
