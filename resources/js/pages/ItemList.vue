<template>
    <main class="l-main">
        <h2 class="c-title">
            <span>商品一覧画面</span>
        </h2>
        <Loader v-show="loading"></Loader>
        <div class="p-list-cover" v-show="!loading">
            <div class="p-search">
                <h3>
                    <span>条件で絞る</span>
                </h3>
                <ul>
                    <li>
                        <label for="prefecture">都道府県</label>
                        <select
                            name="prefecture"
                            id="prefecture"
                            v-model="search.prefecture"
                            @change="getItemsInSearch"
                        >
                            <option value="0" selected>未選択</option>
                            <option value="1">北海道</option>
                            <option value="2">青森県</option>
                            <option value="3">岩手県</option>
                            <option value="4">宮城県</option>
                            <option value="5">秋田県</option>
                            <option value="6">山形県</option>
                            <option value="7">福島県</option>
                            <option value="8">茨城県</option>
                            <option value="9">栃木県</option>
                            <option value="10">群馬県</option>
                            <option value="11">埼玉県</option>
                            <option value="12">千葉県</option>
                            <option value="13">東京都</option>
                            <option value="14">神奈川県</option>
                            <option value="15">新潟県</option>
                            <option value="16">富山県</option>
                            <option value="17">石川県</option>
                            <option value="18">福井県</option>
                            <option value="19">山梨県</option>
                            <option value="20">長野県</option>
                            <option value="21">岐阜県</option>
                            <option value="22">静岡県</option>
                            <option value="23">愛知県</option>
                            <option value="24">三重県</option>
                            <option value="25">滋賀県</option>
                            <option value="26">京都府</option>
                            <option value="27">大阪府</option>
                            <option value="28">兵庫県</option>
                            <option value="29">奈良県</option>
                            <option value="30">和歌山県</option>
                            <option value="31">鳥取県</option>
                            <option value="32">島根県</option>
                            <option value="33">岡山県</option>
                            <option value="34">広島県</option>
                            <option value="35">山口県</option>
                            <option value="36">徳島県</option>
                            <option value="37">香川県</option>
                            <option value="38">愛媛県</option>
                            <option value="39">高知県</option>
                            <option value="40">福岡県</option>
                            <option value="41">佐賀県</option>
                            <option value="42">長崎県</option>
                            <option value="43">熊本県</option>
                            <option value="44">大分県</option>
                            <option value="45">宮崎県</option>
                            <option value="46">鹿児島県</option>
                            <option value="47">沖縄県</option>
                        </select>
                    </li>
                    <li>
                        <label>価格</label>
                        <input
                            type="radio"
                            name="price"
                            id="0"
                            value="0"
                            v-model="search.price"
                            @change="getItemsInSearch"
                            checked
                        /><label for="0">未選択</label>
                        <input
                            type="radio"
                            name="price"
                            id="500"
                            value="1"
                            v-model="search.price"
                            @change="getItemsInSearch"
                        /><label for="500">500円未満</label>
                        <input
                            type="radio"
                            name="price"
                            id="500-1000"
                            value="2"
                            v-model="search.price"
                            @change="getItemsInSearch"
                        /><label for="500-1000">500円以上1000円未満</label>
                        <input
                            type="radio"
                            name="price"
                            id="1000"
                            value="3"
                            v-model="search.price"
                            @change="getItemsInSearch"
                        /><label for="1000">1000円以上</label>
                    </li>
                    <li>
                        <label>賞味期限</label>
                        <input
                            type="radio"
                            name="expiry_date"
                            id="not"
                            value="0"
                            v-model="search.expiry_date"
                            @change="getItemsInSearch"
                            checked
                        /><label for="not">未選択</label>
                        <input
                            type="radio"
                            name="expiry_date"
                            id="out"
                            value="1"
                            v-model="search.expiry_date"
                            @change="getItemsInSearch"
                        /><label for="out">期限切れ</label>
                        <input
                            type="radio"
                            name="expiry_date"
                            id="safe"
                            value="2"
                            v-model="search.expiry_date"
                            @change="getItemsInSearch"
                        /><label for="safe">期限内</label>
                    </li>
                </ul>
            </div>
            <div class="p-list" v-if="items.length !== 0">
                <ul>
                    <Item v-for="item in items" :item="item"></Item>
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
            <p class="p-not-list" v-else>検索結果はありません。</p>
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
            itemsInfo: null,
            current_page: 1,
            last_page: "",
            range: 5,
            front_dot: false,
            end_dot: false,
            search: {
                prefecture: "0",
                price: "0",
                expiry_date: "0",
            },
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
        // 商品の一覧を取得するメソッド
        async getItems() {
            this.loading = true;

            const response = await axios.get(
                `/api/items?prefecture=${this.search.prefecture}&price=${this.search.price}&expiry_date=${this.search.expiry_date}&page=${this.current_page}`
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
        // 検索条件に一致した商品の一覧を取得するメソッド
        async getItemsInSearch() {
            this.loading = true;

            const response = await axios.get(
                `/api/items?prefecture=${this.search.prefecture}&price=${this.search.price}&expiry_date=${this.search.expiry_date}&page=1`
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
