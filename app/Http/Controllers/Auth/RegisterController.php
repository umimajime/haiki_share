<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use App\Mail\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => ['required', 'string', 'email:filter,dns', 'max:255', 'unique:users', 'unique:stores'],
            'password' => ['required', 'string', 'min:8', 'max:24', 'regex:{^[a-zA-Z0-9.?/-]{8,24}$}', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    // ユーザー登録の完了後に、ユーザー情報を返却するメソッド
    protected function registered(Request $request, $user)
    {
        $mail_title = 'ユーザー登録完了通知';
        $mail_text = "
        haiki shareへのユーザー登録が完了いたしました。\n\n
        サービス利用の流れ\n
        <利用者側（廃棄を買う側）>\n
        1. ユーザー登録する\n
        2. 商品一覧から気になる商品を探す\n
        3. 購入の意思が固まったら、商品詳細画面で購入ボタンを押す（このときコンビニ側・利用者側の両方に通知メールが行く）\n
        4. 利用者はコンビニに商品を購入しに行く。\n\n
        <コンビニ側>\n
        1. ユーザー登録する\n
        2. 廃棄となった商品を登録する\n
        3. 「商品が購入された」というメール通知があれば、実際の商品を用意しておく。\n\n
        ご利用お待ちしております。\n\n
        ※本メールは送信専用です。本メールに返信されてもご対応は致しかねます。";
        $mail_to = $request->get('email');
        Mail::to($mail_to)->send(new Notification($mail_title, $mail_text));

        return $user;
    }
}
