<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\Notification;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
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
     * Check the token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function checkToken(Request $request)
    {
        $passwordReset = DB::table('password_resets')
            ->where('email', $request['email'])
            ->first();
        if (!Hash::check($request['token'], $passwordReset->token) || Carbon::parse($passwordReset->created_at)->addMinutes(config('auth.passwords.users.expire'))->isPast()) {
            return response()->json([
                'errors' => ['token' => ['無効なトークンです。']],
            ], Response::HTTP_UNAUTHORIZED);
        }

        return response()->json();
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function reset(Request $request)
    {
        $request->validate($this->rules());

        $user = User::where('email', $request['email'])->get();

        if (Hash::check($request['password'], $user[0]['password'])) {
            return response()->json([
                'errors' => ['password' => ['入力されたパスワードは既に登録されています。']],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $response = $this->broker()->reset(
            $this->credentials($request),
            function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        if (Password::PASSWORD_RESET) {
            $mail_title = 'パスワードリセット完了通知';
            $mail_text = "
            haiki shareをご利用いただきまして誠にありがとうございます。\n\n
            パスワードの変更が完了いたしました。\n\n
            ご利用お待ちしております。\n\n
            ※本メールは送信専用です。本メールに返信されてもご対応は致しかねます。";
            $mail_to = $request->get('email');
            Mail::to($mail_to)->send(new Notification($mail_title, $mail_text));

            return response()->json();
        }

        return response()->json([
            'errors' => ['token' => ['トークンの有効期限が切れています']],
        ], Response::HTTP_UNAUTHORIZED);
    }
}
