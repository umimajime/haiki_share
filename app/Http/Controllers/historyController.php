<?php

namespace App\Http\Controllers;

use App\History;
use App\Item;
use App\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Mail\Notification;
use Illuminate\Support\Facades\Mail;

class historyController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | History Controller
    |--------------------------------------------------------------------------
    |
    | このコントローラーは、購入・販売履歴に関する機能を提供する役割がある。
    | 具体的には、以下の機能を提供する。
    | ・特定の商品を購入
    | ・特定の商品をキャンセル
    */

    /**
     * 特定の商品を購入するメソッド
     * @param Request $request
     * @return \Illuminate\Http\Response
     */

    public function create(Request $request)
    {
        $history = new History();
        $item = new Item();
        $store = new Store();

        $count = $history::where([
            'item_id' => $request->item_id,
        ])->count();

        if ($count !==  0) {
            return response('', 400);
        }

        $history->create([
            'store_id' => $request->store_id,
            'item_id' => $request->item_id,
            'user_id' => Auth::user()->id,
        ]);

        $item::where('id', $request->item_id)->update(['sell_flg' => 1]);

        $item = $item::where('id', $request->item_id)->get();

        $store = $store::join('prefectures', 'stores.prefecture', '=', 'prefectures.id')
            ->where('stores.id', $request->store_id)->get();

        $mail_title_user = '商品購入完了通知';
        $mail_title_store = '商品販売完了通知';

        $mail_text_user = "
        haiki shareをご利用いただきまして誠にありがとうございます。\n\n
        商品の購入が完了いたしました。\n\n
        以下は商品の詳細です。\n
        商品名：" . $item[0]->name . "
        価格：" . $item[0]->price . "
        賞味期限：" . $item[0]->expiry_date . "\n\n
        また、以下は商品を出品したコンビニの詳細です。\n
        コンビニ名：" . $store[0]->store_name . "\n";

        $mail_text_user .= !empty($store[0]->branch_name) ? "支店名：" . $store[0]->branch_name : "";

        $mail_text_user .= "
        郵便番号：" . $store[0]->post_code . "
        住所：" . $store[0]->name . $store[0]->municipality . $store[0]->address;

        $mail_text_user .= !empty($store[0]->building) ? $store[0]->building : "";

        $mail_text_user .= "
        メールアドレス：" . $store[0]->email . "\n\n
        商品購入後は、購入した商品を出品したコンビニまで向かい、実際に購入して下さい。\n
        引き続きのご利用お待ちしております。\n\n
        ※本メールは送信専用です。本メールに返信されてもご対応は致しかねます。";

        $mail_text_store = "
        haiki shareをご利用いただきまして誠にありがとうございます。\n\n
        商品の販売が完了いたしました。\n\n
        以下は商品の詳細です。\n
        商品名：" . $item[0]->name . "
        価格：" . $item[0]->price . "
        賞味期限：" . $item[0]->expiry_date . "
        出品日時：" . $item[0]->created_at . "\n\n
        商品販売後は、購入した利用者が実際に購入しに来るため、商品を用意して下さい。\n
        引き続きのご利用お待ちしております。\n\n
        ※本メールは送信専用です。本メールに返信されてもご対応は致しかねます。";

        $mail_to_user = Auth::user()->email;
        $mail_to_store = $store[0]->email;
        Mail::to($mail_to_user)->send(new Notification($mail_title_user, $mail_text_user));
        Mail::to($mail_to_store)->send(new Notification($mail_title_store, $mail_text_store));

        return response('', 201);
    }

    /**
     * 特定の商品をキャンセルするメソッド
     * @param Request $request
     * @return \Illuminate\Http\Response
     */

    public function delete(Request $request)
    {
        $history = new History();
        $item = new Item();

        $user_id = $history->where('item_id', '=', $request->item_id)->select('user_id')->get();

        if ($user_id ===  Auth::user()->id) {
            return response('', 400);
        }

        $history->where('item_id', '=', $request->item_id)->delete();

        $item::where('id', $request->item_id)->update(['sell_flg' => 0]);

        return response('', 200);
    }
}
