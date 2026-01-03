<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\ContactUs;
use App\Models\HeaderLogo;
use App\Models\Language;
use App\Models\Section;
use Illuminate\Http\Request;

class CmsController extends Controller
{

    public function contact(Request $request) {
        $condition = session('condition', 'new');

        if ($request->isMethod('post')) {
            $data = $request->all();
            $rules = [
                // Fields/Column Names
                'name'    => 'required|string|max:100',
                'email'   => 'required|email|max:150',
                'subject' => 'required|max:200',
                'message' => 'required'
            ];


            $customMessages = [
                // The SAME last Fields (inside $rules array)
                'name.required'    => 'Name is required',
                'name.string'      => 'Name must be string',

                'email.required'   => 'Email is required',
                'email.email'      => 'Valid email is required',

                'subject.required' => 'Subject is requireed',

                'message.required' => 'Message is required'
            ];

            $validator = \Illuminate\Support\Facades\Validator::make($data, $rules, $customMessages);



            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }


            // Send the user's Contact Us inquiry as an email to the 'admin'
            $email = 'admin@admin.com'; // Admin's email

            // The email message data/variables that will be passed in to the email view
            $messageData = [
                'name'    => $data['name'],
                'email'   => $data['email'],
                'subject' => $data['subject'],
                'comment' => $data['message']
            ];

            // Save to database
            ContactUs::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'subject' => $data['subject'],
                'message' => $data['message'],
                'status' => 'pending',
            ]);

            \Illuminate\Support\Facades\Mail::send('emails.inquiry', $messageData, function ($message) use ($email) {
                $message->to($email)->subject('Inquiry from a user');
            });
            $logos     = HeaderLogo::first();
            $sections  = Section::all();
            $language  = Language::get();
            $condition = $request->query('condition');


            // Return the user back with a Success Message
            $message = 'Thanks for your inquiry. We will get back to you soon.';
            return redirect()->back()->with('success_message', $message);
        }


        $logos     = HeaderLogo::first();
        $sections  = Section::all();
        $language  = Language::get();
        $condition = session('condition', 'new');

        return view('front.pages.contact',compact('condition', 'sections', 'language', 'logos'));
    }
}
