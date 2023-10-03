<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreItem;
use App\Http\Requests\StoreEditItem;
use App\Item;
use App\Store;
use App\History;
use InterventionImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Item Controller
    |--------------------------------------------------------------------------
    |
    | このコントローラーは、商品に関する機能を提供する役割がある。
    | 具体的には、以下の機能を提供する。
    | ・特定の商品を取得
    | ・商品の一覧を取得
    | ・特定の商品の出品
    | ・特定の商品の編集
    | ・特定の商品の削除
    | ・ログイン中の利用者が購入した商品の一覧を取得
    | ・ログイン中のコンビニが出品した商品の一覧を取得
    | ・ログイン中のコンビニの購入された商品の一覧を取得
    */

    /**
     * 特定の商品を取得するメソッド
     * @param Request $request
     * @return \Illuminate\Http\Response
     */

    public function readOneItem(Request $request)
    {
        $store = new Store();
        $history = new History();

        Log::debug($request);

        $item = $store::with('prefecture')
            ->join('items', 'stores.id', '=', 'items.store_id')
            ->where('items.id', $request->id)
            ->get();

        $itemArr = $item->toArray();

        if (!empty(Auth::user())) {
            $count = $history::where([
                ['user_id', '=', Auth::user()->id],
                ['item_id', '=', $request->id]
            ])->count();

            if ($count === 0) {
                $itemArr['isBuyUser'] = false;
            } else {
                $itemArr['isBuyUser'] = true;
            }

            return response()->json($itemArr);
        }

        if ($item[0]->store_id === (int)$request->userId) {
            $itemArr['isMatchStore'] = true;
        } else {
            $itemArr['isMatchStore'] = false;
        }

        return response()->json($itemArr);
    }

    /**
     * 商品の一覧を取得するメソッド
     * @param Request $request
     * @return \Illuminate\Http\Response
     */

    public function read(Request $request)
    {
        $store = new Store();

        $prefecture_id = $request->query('prefecture');
        $price_id = $request->query('price');
        $expiry_date_id = $request->query('expiry_date');

        $search_arr = [];

        if ($prefecture_id === '0' && $price_id === '0' && $expiry_date_id === '0') {
            $items = $store::with('prefecture')
                ->join('items', 'stores.id', '=', 'items.store_id')
                ->where('items.deleted_at', '=', null)
                ->orderBy('items.id', 'desc')
                ->paginate(15);
        } else {
            if ($prefecture_id !== '0') {
                array_push($search_arr, ['prefecture', '=', $prefecture_id]);
            }

            if ($price_id === '1') {
                array_push($search_arr, ['price', '<', '500']);
            } elseif ($price_id === '2') {
                array_push($search_arr, ['price', '>=', '500'], ['price', '<', '1000']);
            } else if ($price_id === '3') {
                array_push($search_arr, ['price', '>=', '1000']);
            }

            if ($expiry_date_id === '1') {
                array_push($search_arr, ['items.expiry_date', '<', date('Y-m-d')]);
            } elseif ($expiry_date_id === '2') {
                array_push($search_arr, ['items.expiry_date', '>', date('Y-m-d')]);
            }

            array_push($search_arr, ['items.deleted_at', '=', null]);

            $items = $store::with('prefecture')
                ->join('items', 'stores.id', '=', 'items.store_id')
                ->where($search_arr)
                ->orderBy('items.id', 'desc')
                ->paginate(15);
        }

        return response($items, 200);
    }

    /**
     * 特定の商品を出品するメソッド
     * @param StoreItem $request
     * @return \Illuminate\Http\Response
     */

    public function create(StoreItem $request)
    {
        $item = new Item();
        $store = new Store();

        $extension = $request->photo->extension();

        $item->image = mb_substr($request->name, 0, 100)  .  '_' . ($item::withTrashed()->count() + 1) . '.'  . $extension;

        $photo = InterventionImage::make($request->photo);
        $photo->orientate();
        $photo->resize(
            600,
            null,
            function ($constraint) {
                // 縦横比を保持したままにする
                $constraint->aspectRatio();
                // 小さい画像は大きくしない
                $constraint->upsize();
            }
        );

        $filePath = storage_path('app/public/items');
        $photo->save($filePath . '/' . $item->image);

        $item->name = $request->name;
        $item->price = $request->price;
        $item->expiry_date = $request->expiry_date;

        DB::beginTransaction();

        try {
            $store::find($request->store_id)->items()->save($item);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Storage::disk('public')->delete('items/' . $item->image);
            throw $exception;
        }

        return response("", 201);
    }

    /**
     * 特定の商品を編集するメソッド
     * @param StoreEditItem $request
     * @return \Illuminate\Http\Response
     */

    public function update(StoreEditItem $request)
    {
        $item = new Item();
        $store = new Store();

        $sell_flg = $item::where([
            'id' => $request->item_id,
        ])->select('sell_flg')->get();

        if ($sell_flg[0]->sell_flg === 1) {
            return response('', 400);
        }

        if ($request->imageChangeFlg === '1') {

            if ($request->image === null) {
                $image['image'] = ['画像は選択必須です。'];
                $errors['errors'] = $image;
                return response($errors, 422);
            }

            $extension = $request->image->extension();

            $item->image = mb_substr($request->name, 0, 100) .  '_' . ($item::withTrashed()->count() + 1) . '.'  . $extension;

            $image = InterventionImage::make($request->image);
            $image->orientate();
            $image->resize(
                600,
                null,
                function ($constraint) {
                    // 縦横比を保持したままにする
                    $constraint->aspectRatio();
                    // 小さい画像は大きくしない
                    $constraint->upsize();
                }
            );

            $filePath = storage_path('app/public/items');
            $image->save($filePath . '/' . $item->image);

            $item->name = $request->name;
            $item->price = $request->price;
            $item->expiry_date = $request->expiry_date;

            DB::beginTransaction();

            try {
                $store::find($request->store_id)->items()->where('id', '=', $request->item_id)->update([
                    'image' => $item->image,
                    'name' => $item->name,
                    'price' => $item->price,
                    'expiry_date' => $item->expiry_date
                ]);
                DB::commit();
            } catch (\Exception $exception) {
                DB::rollBack();
                Storage::disk('public')->delete('items/' . $item->image);
                throw $exception;
            }

            return response("", 201);
        }

        $item->name = $request->name;
        $item->price = $request->price;
        $item->expiry_date = $request->expiry_date;

        DB::beginTransaction();

        try {
            $store::find($request->store_id)->items()->where('id', '=', $request->item_id)->update([
                'name' => $item->name,
                'price' => $item->price,
                'expiry_date' => $item->expiry_date
            ]);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        return response("", 201);
    }

    /**
     * 特定の商品を削除するメソッド
     * @param Request $request
     * @return \Illuminate\Http\Response
     */

    public function delete(Request $request)
    {
        $item = new Item();

        $sell_flg = $item::where([
            'id' => $request->item_id,
        ])->select('sell_flg')->get();

        if ($sell_flg[0]->sell_flg === 1) {
            return response('', 400);
        }

        $item::where('id', '=', $request->item_id)->delete();

        return response('', 200);
    }

    /**
     * ログイン中の利用者が購入した商品の一覧を取得するメソッド
     * @param Request $request
     * @return \Illuminate\Http\Response
     */

    public function readBuyItem(Request $request)
    {
        $history = new History();

        $history = $history::join('items as i', 'histories.item_id', '=', 'i.id')
            ->join('stores as s', 'i.store_id', '=', 's.id')
            ->join('prefectures as p', 's.prefecture', '=', 'p.id')
            ->where('histories.user_id', '=', $request->userId)
            ->orderBy('histories.id', 'desc')
            ->select(
                'i.id',
                'i.sell_flg',
                'p.name as prefName',
                'i.image',
                'i.name',
                'i.price',
                'i.expiry_date',
                's.store_name',
            )
            ->paginate(15);

        return response($history, 200);
    }

    /**
     * ログイン中のコンビニが出品した商品の一覧を取得するメソッド
     * @param Request $request
     * @return \Illuminate\Http\Response
     */

    public function readSellItem(Request $request)
    {
        $store = new Store();

        $items = $store::with('prefecture')
            ->join('items', 'stores.id', '=', 'items.store_id')
            ->where([
                ['stores.id', '=', $request->userId],
                ['items.deleted_at', '=', null]
            ])
            ->orderBy('items.id', 'desc')
            ->paginate(15);

        return response($items, 200);
    }

    /**
     * ログイン中のコンビニの購入された商品の一覧を取得するメソッド
     * @param Request $request
     * @return \Illuminate\Http\Response
     */

    public function readSoldItem(Request $request)
    {
        $store = new Store();

        $items = $store::with('prefecture')
            ->join('items', 'stores.id', '=', 'items.store_id')
            ->where([
                ['stores.id', '=', $request->userId],
                ['items.sell_flg', "=", "1"],
                ['items.deleted_at', '=', null]
            ])
            ->orderBy('items.id', 'desc')
            ->paginate(15);

        return response($items, 200);
    }
}
