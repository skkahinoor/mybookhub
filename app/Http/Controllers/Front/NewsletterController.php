<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\NewsletterSubscriber;

class NewsletterController extends Controller
{
    public function addSubscriber(Request $request) {
        $condition = $request->query('condition');
        if (!in_array($condition, ['new', 'old'])) {
            $condition = 'new';
        }

        if ($request->ajax()) {
            $data = $request->all();
            // dd($data);

            $subscriberCount = NewsletterSubscriber::where('email', $data['subscriber_email'])->count(); 
            if ($subscriberCount > 0) {
                return 'Email already exists';
            } else {
                // INSERT the email in the `newsletter_subscribers` table
                $subscriber = new NewsletterSubscriber;

                $subscriber->email = $data['subscriber_email'];
                $subscriber->status = 1; // 1 by default

                $subscriber->save();


                return 'Email saved in our database';
            }
        }
    }

}
