<template>
    <main class="l-main">
        <h2 class="c-title">
            <span>コンビニ側マイページ画面</span>
        </h2>
        <Loader v-show="loading"></Loader>
        <div class="p-list-mypage-cover" v-show="!loading">
            <div class="p-search">
                <RouterLink to="edit-store-profile" class="c-button"
                    >プロフィール編集画面へ</RouterLink
                >
                <RouterLink to="/register-item" class="c-button"
                    >商品出品画面へ</RouterLink
                >
                <RouterLink to="item-list" class="c-button"
                    >商品一覧画面へ</RouterLink
                >
            </div>
            <h3>
                <span>出品した商品一覧</span>
            </h3>
            <div class="p-list" v-if="sellItems.length !== 0">
                <ul>
                    <Item
                        v-for="item in sellItemLimitCount"
                        :item="item"
                        :editFlg="editFlg"
                    ></Item>
                </ul>
                <RouterLink to="sell-item-list" class="c-button"
                    >全件表示</RouterLink
                >
            </div>
            <p class="p-not-list" v-else>出品した商品はありません。</p>
            <h3>
                <span>購入された商品一覧</span>
            </h3>
            <div class="p-list" v-if="soldItems.length !== 0">
                <ul>
                    <Item
                        v-for="item in soldItemLimitCount"
                        :item="item"
                        :editFlg="editFlg"
                    ></Item>
                </ul>
                <RouterLink to="sold-item-list" class="c-button"
                    >全件表示</RouterLink
                >
            </div>
            <p class="p-not-list" v-else>購入された商品はありません。</p>
        </div>
    </main>
</template>

<script>
import { OK } from "../util";
import Loader from "../components/Loader.vue";
import Item from "./Item.vue";
import { RouterLink } from "vue-router";

export default {
    components: {
        Loader,
        Item,
        RouterLink,
    },
    data() {
        return {
            editFlg: 1,
            loading: false,
            sellItems: [],
            soldItems: [],
            current_page: 1,
        };
    },
    computed: {
        // 出品した商品の５件を返却するプロパティ
        sellItemLimitCount() {
            return this.sellItems.slice(0, 5);
        },
        // 購入された商品の５件を返却するプロパティ
        soldItemLimitCount() {
            return this.soldItems.slice(0, 5);
        },
    },
    methods: {
        // 出品した商品と購入された商品の一覧をを取得するメソッド
        async getItems() {
            this.loading = true;

            const sellItemResponse = await axios.get(
                `/api/items/sell?page=${this.current_page}`
            );

            const soldItemResponse = await axios.get(
                `/api/items/sold?page=${this.current_page}`
            );

            this.loading = false;

            if (
                sellItemResponse.status !== OK ||
                soldItemResponse.status !== OK
            ) {
                this.$store.commit("error/setCode", response.status);
                return false;
            }

            this.sellItems = sellItemResponse.data.data;
            this.soldItems = soldItemResponse.data.data;
        },
        // 特定の商品を削除するメソッド
        async cancelItem(id) {
            this.loading = true;

            const response = await axios.get(
                `/api/history/delete?item_id=${id}`
            );

            this.loading = false;

            if (response.status !== OK) {
                this.$store.commit("error/setCode", response.status);
                return false;
            }

            this.$store.commit("message/setContent", {
                content: "商品の購入をキャンセルしました！",
                timeout: 5000,
            });
        },
    },
    created() {
        this.getItems();
    },
};
</script>
