<template>
    <main class="l-main">
        <h2 class="c-title">
            <span>購入された商品一覧画面</span>
        </h2>
        <Loader v-show="loading"></Loader>
        <div class="p-list-not-search-cover" v-show="!loading">
            <div class="p-list" v-if="items.length !== 0">
                <ul>
                    <Item
                        v-for="item in items"
                        :item="item"
                        :editFlg="editFlg"
                    ></Item>
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
            <p class="p-not-list" v-else>購入された商品はありません。</p>
        </div>
    </main>
</template>

<script>
import { OK } from "../util";
import Loader from "../components/Loader.vue";
import Item from "./Item.vue";

export default {
    components: {
        Loader,
        Item,
    },
    data() {
        return {
            loading: false,
            items: [],
            editFlg: 0,
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
        // 購入された商品の一覧を取得するメソッド
        async getItems() {
            this.loading = true;

            const response = await axios.get(
                `/api/items/sold?page=${this.current_page}&userId=${this.$store.state.auth.user.id}`
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
    },
    created() {
        this.getItems();
    },
};
</script>
