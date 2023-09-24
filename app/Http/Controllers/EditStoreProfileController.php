<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEditProfile;
use Illuminate\Support\Facades\Hash;
use App\Store;
use Illuminate\Support\Facades\Auth;
use App\Mail\Notification;
use Illuminate\Support\Facades\Mail;

class EditStoreProfileController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Edit Store Profile Controller
    |--------------------------------------------------------------------------
    |
    | このコントローラーは、コンビニのプロフィールの編集に関する機能を提供する役割がある。
    | 具体的には、以下の機能を提供する。
    | ・コンビニのプロフィールを編集する
    */

    /**
     * コンビニのプロフィールを編集するメソッドs
     * @param StoreEditProfile $request
     * @return \Illuminate\Http\Response
     */

    public function edit(StoreEditProfile $request)
    {
        if (
            $request->store_name === null &&
            $request->branch_name === null &&
            $request->post_code === null &&
            $request->prefecture === null &&
            $request->municipality === null &&
            $request->address === null &&
            $request->building === null &&
            $request->email === null &&
            ($request->password === null || $request->password_confirmation === null)
        ) {
            return response('', 200);
        }

        $isChangeEmail = false;
        $isChangepPassword = false;

        $store = new Store();

        $store = $store::find(Auth::guard('store')->user()->id);

        if ($request->store_name !== null) {
            $store::where('id', '=', Auth::guard('store')->user()->id)->update(['store_name' => $request->store_name]);
        }

        if ($request->branch_name !== null) {
            $store::where('id', '=', Auth::guard('store')->user()->id)->update(['branch_name' => $request->branch_name]);
        }

        if ($request->post_code !== null) {
            $store::where('id', '=', Auth::guard('store')->user()->id)->update(['post_code' => $request->post_code]);
        }

        if ($request->prefecture !== null) {
            $store::where('id', '=', Auth::guard('store')->user()->id)->update(['prefecture' => $request->prefecture]);
        }

        if ($request->municipality !== null) {
            $store::where('id', '=', Auth::guard('store')->user()->id)->update(['municipality' => $request->municipality]);
        }

        if ($request->address !== null) {
            $store::where('id', '=', Auth::guard('store')->user()->id)->update(['address' => $request->address]);
        }

        if ($request->building !== null) {
            $store::where('id', '=', Auth::guard('store')->user()->id)->update(['building' => $request->building]);
        }

        if ($request->email !== null) {
            $store::where('id', '=', Auth::guard('store')->user()->id)->update(['email' => $request->email]);
            $isChangeEmail = true;
        }

        if ($request->password !== null) {
            if (Hash::check($request->password, $store->password)) {
                $password['password'] = ['入力されたパスワードは既に登録されています。'];
                $errors['errors'] = $password;
                return response($errors, 422);
            }

            $store::where('id', '=', Auth::guard('store')->user()->id)->update(['password' => Hash::make($request->password)]);
            $isChangepPassword = true;
        }

        $mail_title_email = 'メールアドレス変更完了通知';
        $mail_title_password = 'パスワード変更完了通知';

        $mail_text_email = "
        haiki shareをご利用いただきまして誠にありがとうございます。\n\n
         メールアドレスの変更が完了いたしました。\n\n
         引き続きのご利用お待ちしております。\n\n
         ※本メールは送信専用です。本メールに返信されてもご対応は致しかねます。";
        $mail_text_password = "
        haiki shareをご利用いただきまして誠にありがとうございます。\n\n
         パスワードの変更が完了いたしました。\n\n
         引き続きのご利用お待ちしております。\n\n
         ※本メールは送信専用です。本メールに返信されてもご対応は致しかねます。";

        $mail_to_old = $store->email;
        $mail_to_new = $request->email;

        if ($isChangeEmail === true && $isChangepPassword === true) {
            Mail::to($mail_to_old)->send(new Notification($mail_title_email, $mail_text_email));
            Mail::to($mail_to_new)->send(new Notification($mail_title_email, $mail_text_email));
            Mail::to($mail_to_old)->send(new Notification($mail_title_password, $mail_text_password));
            Mail::to($mail_to_new)->send(new Notification($mail_title_password, $mail_text_password));
        }

        if ($isChangeEmail === true && $isChangepPassword === false) {
            Mail::to($mail_to_old)->send(new Notification($mail_title_email, $mail_text_email));
            Mail::to($mail_to_new)->send(new Notification($mail_title_email, $mail_text_email));
        }

        if ($isChangeEmail === false && $isChangepPassword === true) {
            Mail::to($mail_to_old)->send(new Notification($mail_title_password, $mail_text_password));
        }

        return response('', 201);
    }
}
