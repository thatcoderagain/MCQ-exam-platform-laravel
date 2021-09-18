<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public static function sendMail($emails, $view, $data, $subject) {
        Mail::send($view, $data, function($message) use ($emails, $subject) {
            $message
                ->to($emails)
                ->subject($subject)
                ->from(env('MAIL_FROM_ADDRESS') ?? 'someone@something.com', env('MAIL_FROM_NAME') ??'Someone');
        });
    }
}
