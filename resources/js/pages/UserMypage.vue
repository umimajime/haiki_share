<template>
    <main class="l-main">
        <h2 class="c-title">
            <span>利用者側マイページ画面</span>
        </h2>
        <Loader v-show="loading"></Loader>
        <div class="p-list-mypage-cover" v-show="!loading">
            <div class="p-search">
                <RouterLink to="edit-user-profile" class="c-button"
                    >プロフィール編集画面へ</RouterLink
                >
                <RouterLink to="item-list" class="c-button"
                    >商品一覧画面へ</RouterLink
                >
            </div>
            <h3>
                <span>購入した商品一覧</span>
            </h3>
            <div class="p-list" v-if="items.length !== 0">
                <ul>
                    <MypageItem
                        v-for="item in items"
                        :item="item"
                        @onClickCancel="cancelItem($event)"
                    ></MypageItem>
                </ul>
                <ul>
                    <li
                        :class="current_page == 1 ? 'disable' : ''"
                        @click="changePage(current_page - 1)"
                    >
                        «
                    </li>
                    <li
                        v-for="page in frontPageRange"
                        :key="page"
                        @click="changePage(page)"
                        :class="isCurrent(page) ? 'active' : ''"
                    >
                        {{ page }}
                    </li>
                    <li v-show="front_dot" class="disable">...</li>
                    <li
                        v-for="page in middlePageRange"
                        :key="page"
                        @click="changePage(page)"
                        :class="isCurrent(page) ? 'active' : ''"
                    >
                        {{ page }}
                    </li>
                    <li v-show="end_dot" class="disable">...</li>
                    <li
                        v-for="page in endPageRange"
                        :key="page"
                        @click="changePage(page)"
                        :class="isCurrent(page) ? 'active' : ''"
                    >
                        {{ page }}
                    </li>
                    <li
                        :class="current_page >= last_page ? 'disable' : ''"
                        @click="changePage(current_page + 1)"
                    >
                        »
                    </li>
                </ul>
            </div>
            <p class="p-not-list" v-else>購入した商品はありません。</p>
        </div>
    </main>
</template>

<script>
import { OK, BAD_REQUEST } from "../util";
import Loader from "../components/Loader.vue";
import MypageItem from "./MypageItem.vue";

export default {
    components: {
        Loader,
        MypageItem,
    },
    data() {
        return {
            loading: false,
            items: [],
            itemsInfo: null,
            current_page: 1,
            last_page: "",
            range: 5,
            front_dot: false,
            end_dot: false,
        };
    },
    computed: {
        // 1,2を管理するプロパティ
        frontPageRange() {
            if (!this.sizeCheck) {
                this.front_dot = false;
                this.end_dot = false;
                return this.calRange(1, this.last_page);
            }
            return this.calRange(1, 2);
        },
        // 5を管理するプロパティ
        middlePageRange() {
            if (!this.sizeCheck) return [];
            let start = "";
            let end = "";
            if (this.current_page <= this.range) {
                start = 3;
                end = this.range + 2;
                this.front_dot = false;
                this.end_dot = true;
            } else if (this.current_page > this.last_page - this.range) {
                start = this.last_page - this.range - 1;
                end = this.last_page - 2;
                this.front_dot = true;
                this.end_dot = false;
            } else {
                start = this.current_page - Math.floor(this.range / 2);
                end = this.current_page + Math.floor(this.range / 2);
                this.front_dot = true;
                this.end_dot = true;
            }
            return this.calRange(start, end);
        },
        // 最後の2つを管理するプロパティ
        endPageRange() {
            if (!this.sizeCheck) return [];
            return this.calRange(this.last_page - 1, this.last_page);
        },
        // ページの数を判定するプロパティ
        sizeCheck() {
            if (this.last_page <= this.range + 4) {
                return false;
            }
            return true;
        },
    },
    methods: {
        // 配列を作成するメソッド
        calRange(start, end) {
            const range = [];
            for (let i = start; i <= end; i++) {
                range.push(i);
            }
            return range;
        },
        // ページを移動するメソッド
        changePage(page) {
            if (page > 0 && page <= this.last_page) {
                this.current_page = page;
                this.getItems();
            }
        },
        // 現在のページを判定するメソッド
        isCurrent(page) {
            return page === this.current_page;
        },
        // 購入した商品の一覧を取得するメソッド
        async getItems() {
            this.loading = true;

            const response = await axios.get(
                `/api/items/buy?page=${this.current_page}`
            );

            this.loading = false;

            if (response.status !== OK) {
                this.$store.commit("error/setCode", response.status);
                return false;
            }

            this.items = response.data.data;
            this.itemsInfo = response.data;
            this.current_page = response.data.current_page;
            this.last_page = response.data.last_page;
        },
        // 特定の商品の購入をキャンセルするメソッド
        async cancelItem(id) {
            this.loading = true;

            const response = await axios.get(
                `/api/history/delete?item_id=${id}`
            );

            this.loading = false;

            if (response.status === BAD_REQUEST) {
                this.$store.commit("message/setContent", {
                    content: "他の人が購入した商品のキャンセルは出来ません！",
                    timeout: 5000,
                });

                return;
            }

            if (response.status !== OK) {
                this.$store.commit("error/setCode", response.status);
                return false;
            }

            this.$store.commit("message/setContent", {
                content: "商品の購入をキャンセルしました！",
                timeout: 5000,
            });

            this.$router.push("/user-mypage");
        },
    },
    created() {
        this.getItems();
    },
};
</script>
