<template>
    <main class="l-main">
        <h2 class="c-title">
            <span>商品詳細画面</span>
        </h2>
        <Loader v-show="loading"></Loader>
        <div class="p-item" v-if="item.length !== 0" v-show="!loading">
            <span>{{ item.sell_flg === 0 ? "販売中" : "購入済" }}</span>
            <span>{{ item.prefecture.name }}</span>
            <div class="cover">
                <div class="image-cover">
                    <img :src="itemImage" alt="" />
                </div>
                <div class="content">
                    <p>商品名：{{ item.name }}</p>
                    <p>価格：{{ item.price.toLocaleString() }}円</p>
                    <p>賞味期限：{{ formatDate(item.expiry_date) }}</p>
                    <p>コンビニ名：{{ item.store_name }}</p>
                    <p v-if="item.branch_name !== null">
                        支店名：{{ item.branch_name }}
                    </p>
                    <p>
                        住所：{{ item.prefecture.name }}
                        {{ item.municipality }}
                        {{ item.address }}
                        {{ item.building }}
                    </p>
                    <p>メールアドレス：{{ item.email }}</p>
                    <p v-if="item.photo">
                        画像：<br /><img :src="storePhoto" alt="" />
                    </p>
                    <div class="button-field">
                        <button
                            class="c-button"
                            v-if="
                                isMatchStore === null || isMatchStore === false
                            "
                            :class="{
                                soldout:
                                    item.sell_flg === 1 ||
                                    isMatchStore === false,
                            }"
                            @click="buyItem"
                        >
                            購入する
                        </button>
                        <button
                            class="c-button"
                            v-if="isBuyUser === true"
                            @click="cancelItem"
                        >
                            購入をキャンセルする
                        </button>
                        <button class="c-button" @click="sharelItem">
                            Twitterでシェアする
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>

<script>
import { OK, CREATED, BAD_REQUEST } from "../util";
import Loader from "../components/Loader.vue";

export default {
    components: {
        Loader,
    },
    data() {
        return {
            item: [],
            loading: false,
            isBuyUser: null,
            isMatchStore: null,
            itemImage: "",
            storePhoto: "",
        };
    },
    methods: {
        // 賞味期限を年月日の形式に変換するメソッド
        formatDate(val) {
            let dateArr = val.split("-");
            return (
                Number(dateArr[0]) +
                "年" +
                Number(dateArr[1]) +
                "月" +
                Number(dateArr[2]) +
                "日"
            );
        },
        // 特定の商品を取得するメソッド
        async getItem() {
            const response = await axios.get(
                `/api/item?id=${this.$route.params.id}`
            );

            if (response.status !== OK) {
                this.$store.commit("error/setCode", response.status);
                return false;
            }

            this.item = response.data[0];

            if ("isBuyUser" in response.data) {
                this.isBuyUser = response.data["isBuyUser"];
            }

            if ("isMatchStore" in response.data) {
                this.isMatchStore = response.data["isMatchStore"];
            }

            this.itemImage = "/storage/items/" + response.data[0].image;
            this.storePhoto = "/storage/items/" + response.data[0].photo;
        },
        // 商品を購入するメソッド
        async buyItem() {
            if (this.item.sell_flg === 1) {
                this.$store.commit("message/setContent", {
                    content: "この商品は購入済みです！",
                    timeout: 5000,
                });

                return;
            }

            if (this.isMatchStore === false) {
                this.$store.commit("message/setContent", {
                    content: "他のコンビニの商品は購入できません！",
                    timeout: 5000,
                });

                return;
            }

            this.loading = true;

            const response = await axios.get(
                `/api/history/create?store_id=${this.item.store_id}&item_id=${this.item.id}`
            );

            this.loading = false;

            if (response.status === BAD_REQUEST) {
                this.$store.commit("message/setContent", {
                    content: "この商品は購入済みです！",
                    timeout: 5000,
                });

                return;
            }

            if (response.status !== CREATED) {
                this.$store.commit("error/setCode", response.status);
                return false;
            }

            this.$store.commit("message/setContent", {
                content: "商品を購入しました！",
                timeout: 5000,
            });

            this.$router.push("/item-list");
        },
        // 商品の購入をキャンセルするメソッド
        async cancelItem() {
            this.loading = true;

            const response = await axios.get(
                `/api/history/delete?item_id=${this.item.id}`
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

            this.$router.push("/item-list");
        },
        // 商品をX（Twitter）でシェアするメソッド
        sharelItem() {
            let shareURL =
                "https://twitter.com/intent/tweet?text=" +
                `「${this.item.name}」を販売してます！%0D%0A%0D%0A` +
                `商品名：${this.item.name}%0D%0A` +
                `金額：${this.item.price}円%0D%0A` +
                `賞味期限：${this.formatDate(
                    this.item.expiry_date
                )}%0D%0A%0D%0A` +
                "%20%23haikishare" +
                "&url=" +
                "http://52.195.211.5/";
            window.open(shareURL, "_blank");
        },
    },
    created() {
        this.getItem();
    },
};
</script>
