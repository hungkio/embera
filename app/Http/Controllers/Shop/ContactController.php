<?php

namespace App\Http\Controllers\Shop;

use App\Domain\Contact\Models\Contact;
use App\Domain\SubscribeEmail\Models\SubscribeEmail;
use App\Http\Requests\ContactEmailStoreRequest;
use App\Http\Requests\ContactStoreRequest;
use App\Jobs\FormMailAdmin;
use App\Jobs\FormMailCustomer;
use Illuminate\Http\Request;

class ContactController
{
    public function index()
    {
        return view('shop.page.contact');
    }

    public function store(ContactStoreRequest $request) {
        $contact = Contact::updateOrCreate([
            'email' => $request->email
        ], $request->except('email'));
        SubscribeEmail::updateOrCreate([
            'email' => $contact->email
        ], []);
        flash()->success(__('Bạn đã gửi liên hệ thành công!'));

        // Send Mail
        $mail_data = $request->all();
        $replace = [];
        foreach ($mail_data as $key => $value) {
            if (\is_array($value)) {
                continue;
            }
            $replace["[{$key}]"] = $value;
        }

        $body = view('shop.mail.mail-common');
        $body = \str_replace(\array_keys($replace), \array_values($replace), $body);

        $mail_args = [
            'subject' => __('Bạn đã đăng ký liên hệ thành công !'),
            'body' => $body,
            'customer' => $mail_data['email'],
        ];

        // MAIL TO CUSTOMER
        dispatch(new FormMailCustomer($mail_args))->delay(now()->addSeconds(30));

        // MAIL TO ADMIN
        $mail_args['subject'] = __('Đăng ký liên hệ mới !');
        dispatch(new FormMailAdmin($mail_args))->delay(now()->addSeconds(30));

        return response()->json([
            'status' => true,
            'message' =>  __('Bạn đã gửi liên hệ thành công!')
        ]);
    }

    public function subscribeEmail(ContactEmailStoreRequest $request)
    {
        $email = $request->email;
        SubscribeEmail::updateOrCreate([
            'email' => $email
        ], []);
        return response()->json([
            'status' => true,
            'message' =>  __('Bạn đã đăng ký nhận tin thành công!')
        ]);
    }

}
