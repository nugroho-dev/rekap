<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;

class MailController extends Controller
{
    public function index(Request $request)
    {
        $id = request('id');
        $subject = $request->subject;
        $email = $request->email;
        $pesan = $request->pesan;
        $link = $request->link;
  
        $mailData = [
            'pesan' => $pesan,
            'link' => $link,
            'subject'=> $subject
        ];

        Mail::to($email)->send(new SendEmail($mailData));

        return back()->with("success", "Email Berhasil Dikirim");
    }
}
