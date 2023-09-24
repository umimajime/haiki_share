<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserEditProfile;
use Illuminate\Support\Facades\Hash;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Mail\Notification;
use Illuminate\Support\Facades\Mail;

class EditUserProfileController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Edit User Profile Controller
    |--------------------------------------------------------------------------
    |
    | このコントローラーは、利用者のプロフィールの編集に関する機能を提供する役割がある。
    | 具体的には、以下の機能を提供する。
    | ・利用者のプロフィールを編集する
    */

    /**
     * 利用者のプロフィールを編集するメソッド
     * @param UserEditProfile $request
     * @return \Illuminate\Http\Response
     */

    public function edit(UserEditProfile $request)
    {
        if ($request->email === null && ($request->password === null || $request->password_confirmation === null)) {
            return response('', 200);
        }

        $isChangeEmail = false;
        $isChangepPassword = false;

        $user = new User();

        $user = $user::find(Auth::user()->id);

        if ($request->email !== null) {
            $user::where('id', '=', Auth::user()->id)->update(['email' => $request->email]);
            $isChangeEmail = true;
        }

        if ($request->password !== null) {
            if (Hash::check($request->password, $user->password)) {
                $password['password'] = ['入力されたパスワードは既に登録されています。'];
                $errors['errors'] = $password;
                return response($errors, 422);
            }

            $user::where('id', '=', Auth::user()->id)->update(['password' => Hash::make($request->password)]);
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

        $mail_to_old = $user->email;
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
