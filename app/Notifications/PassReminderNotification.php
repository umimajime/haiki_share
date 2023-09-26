<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\ResetPassword;

class PassReminderNotification extends ResetPassword
{
    use Queueable;

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        return (new MailMessage())
            ->subject('パスワードリマインダー通知')
            ->view('email.pass-reminder-mail', [
                'reset_url' => url(config('app.url') . substr(route('pass-reset', ['token' => $this->token, 'email' => $notifiable->getEmailForPasswordReset(), 'isStore' => empty($notifiable->store_name) ? 'false' : 'true'], false), 4)),
                'text1' => "
                haiki shareをご利用いただきまして誠にありがとうございます。\n\n
                下記記載のURLよりパスワード変更画面にアクセスし、パスワードを変更してください。\n
                なお、URLの有効期限は15分となっております。有効期限を過ぎた場合は、再度パスワードリマインダー送信画面からメールアドレスを送信してください。",
                'text2' => "
                ご利用お待ちしております。\n\n
                ※本メールは送信専用です。本メールに返信されてもご対応は致しかねます。"
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
