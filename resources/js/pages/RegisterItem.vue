<template>
    <main class="l-main">
        <h2 class="c-title">
            <span>商品出品画面</span>
        </h2>
        <Loader v-show="loading"></Loader>
        <form class="c-form" @submit.prevent="registerItem" v-show="!loading">
            <div class="c-form__group validation">
                <ul v-if="errors.photo">
                    <li v-for="msg in errors.photo" :key="msg">
                        {{ msg }}
                    </li>
                </ul>
                <ul v-if="errors.name">
                    <li v-for="msg in errors.name" :key="msg">
                        {{ msg }}
                    </li>
                </ul>
                <ul v-if="errors.price">
                    <li v-for="msg in errors.price" :key="msg">
                        {{ msg }}
                    </li>
                </ul>
                <ul v-if="errors.expiry_date">
                    <li v-for="msg in errors.expiry_date" :key="msg">
                        {{ msg }}
                    </li>
                </ul>
            </div>
            <div
                class="c-form__group"
                :class="{
                    success: errors.photo !== null && errors.photo.length === 0,
                    error: errors.photo !== null && errors.photo.length !== 0,
                }"
            >
                <label for="photo">商品画像<span>＊必須</span></label>
                <button
                    type="button"
                    class="c-button active"
                    @click="onSelectButtonClick"
                >
                    ファイルを選択
                </button>
                <button
                    type="button"
                    class="c-button js-remove-button"
                    @click="clearFile"
                >
                    ファイルを解除
                </button>
                <div
                    class="drop-zone"
                    @dragover="addBackGround"
                    @dragleave="removeBackGround"
                    @drop="onFileDrop"
                >
                    <p>ファイルをドラッグ&ドロップ</p>
                    <input
                        type="file"
                        name=""
                        id=""
                        class="js-select-file"
                        @change="previewFile"
                    />
                </div>
                <output v-if="preview">
                    <img :src="preview" alt="" />
                </output>
                <ul>
                    <li>・画像ファイルのみアップロード可能です。</li>
                    <li>
                        ・拡張子がjpg,jpeg,png,gifである画像ファイルのみアップロード可能です。
                    </li>
                </ul>
            </div>
            <div
                class="c-form__group"
                :class="{
                    success: errors.name !== null && errors.name.length === 0,
                    error: errors.name !== null && errors.name.length !== 0,
                }"
            >
                <label for="name">商品名<span>＊必須</span></label>
                <input
                    type="text"
                    id="name"
                    v-model="item.name"
                    maxlength="255"
                    required
                />
                <ul>
                    <li>・255文字以下で入力してください。</li>
                </ul>
            </div>
            <div
                class="c-form__group"
                :class="{
                    success: errors.price !== null && errors.price.length === 0,
                    error: errors.price !== null && errors.price.length !== 0,
                }"
            >
                <label for="price">価格<span>＊必須</span></label>
                <input type="number" id="price" v-model="item.price" required />
                <ul>
                    <li>・半角数字のみを入力してください。</li>
                    <li>・1桁〜10桁の範囲でご入力頂けます。</li>
                </ul>
            </div>
            <div
                class="c-form__group"
                :class="{
                    success:
                        errors.expiry_date !== null &&
                        errors.expiry_date.length === 0,
                    error:
                        errors.expiry_date !== null &&
                        errors.expiry_date.length !== 0,
                }"
            >
                <label for="expiry_date">賞味期限<span>＊必須</span></label>
                <input
                    type="date"
                    name="expiry_date"
                    id="expiry_date"
                    v-model="item.expiry_date"
                    required
                />
            </div>
            <div class="c-form__group">
                <input class="c-button" type="submit" value="出品" />
            </div>
        </form>
    </main>
</template>

<script>
import { mapState } from "vuex";
import { CREATED, UNPROCESSABLE_ENTITY } from "../util";
import Loader from "../components/Loader.vue";

export default {
    components: {
        Loader,
    },
    data() {
        return {
            loading: false,
            preview: null,
            item: {
                photo: "",
                name: "",
                price: "",
                expiry_date: "",
            },
            errors: {
                photo: null,
                name: null,
                price: null,
                expiry_date: null,
            },
        };
    },
    computed: mapState({
        // apiとの通信可否を返却するプロパティ
        apiStatus: (state) => state.auth.apiStatus,
    }),
    watch: {
        // 商品名のバリデーションチェックをする関数
        "item.name": function () {
            this.errors.name = [];
            if (this.item.name.length === 0) {
                this.errors.name.push("商品名は入力必須です。");
                return;
            }
            if (this.item.name.length >= 255) {
                this.errors.name.push(
                    "商品名は255文字以下で入力してください。"
                );
            }
        },
        // 金額のバリデーションチェックをする関数
        "item.price": function () {
            const halfSizeNumberPattarn = /^[0-9]*$/;
            this.errors.price = [];
            if (this.item.price.length === 0) {
                this.errors.price.push("価格は入力必須です。");
                return;
            }
            if (!halfSizeNumberPattarn.test(this.item.price)) {
                this.errors.price.push("価格は半角数字で入力してください。");
            }
            if (this.item.price.length > 10) {
                this.errors.price.push(
                    "価格は1桁〜10桁の範囲でで入力してください。"
                );
            }
        },
        // 賞味期限のバリデーションチェックをする関数
        "item.expiry_date": function () {
            this.errors.expiry_date = [];
            if (this.item.expiry_date.length === 0) {
                this.errors.expiry_date.push(
                    "賞味期限は年・月・日の入力が必須です。"
                );
                return;
            }
        },
    },
    methods: {
        // クラスを追加するメソッド
        addBackGround(e) {
            e.target.classList.add("cover");
        },
        // クラスを削除するメソッド
        removeBackGround(e) {
            e.target.classList.remove("cover");
        },
        // クリックイベントを発火するメソッド
        onSelectButtonClick() {
            this.$el.querySelector(".js-select-file").click();
        },
        // ファイルドロップ時に実行するメソッド
        onFileDrop(e) {
            this.removeBackGround(e);

            if (this.previewFileForDrop(e)) {
                this.$el.querySelector(".js-select-file").files =
                    e.dataTransfer.files;

                this.$el
                    .querySelector(".js-remove-button")
                    .classList.add("active");
            }
        },
        // ファイルプレビューを実行するメソッド
        previewFile(e) {
            this.errors.photo = [];
            if (e.target.files.length === 0) {
                this.clearFile();
                return false;
            }
            if (!e.target.files[0].type.match("image.*")) {
                this.clearFile();
                this.errors.photo.push(
                    "商品画像は画像ファイルをアップロードして下さい。"
                );
                return false;
            }

            this.$el.querySelector(".js-remove-button").classList.add("active");

            const reader = new FileReader();
            reader.onload = (e) => {
                this.preview = e.target.result;
            };
            reader.readAsDataURL(e.target.files[0]);
            this.item.photo = e.target.files[0];
            e.target.value = "";
        },
        // ファイルドロップ時にファイルプレビューを実行するメソッド
        previewFileForDrop(e) {
            this.errors.photo = [];
            if (e.dataTransfer.files.length === 0) {
                return false;
            }
            if (!e.dataTransfer.files[0].type.match("image.*")) {
                this.errors.photo.push(
                    "商品画像は画像ファイルをアップロードして下さい。"
                );
                return false;
            }

            this.$el.querySelector(".js-remove-button").classList.add("active");

            const reader = new FileReader();
            reader.onload = (e) => {
                this.preview = e.target.result;
            };
            reader.readAsDataURL(e.dataTransfer.files[0]);
            this.item.photo = e.dataTransfer.files[0];

            return true;
        },
        // 選択されているファイルを解除するメソッド
        clearFile() {
            this.item.photo = "";
            this.preview = "";
            this.$el
                .querySelector(".js-remove-button")
                .classList.remove("active");
        },
        // 商品を出品するメソッド
        async registerItem() {
            let errArr = Object.values(this.errors);

            for (let i = 0; i < errArr.length; i++) {
                if (errArr[i] !== null && errArr[i].length !== 0) {
                    return;
                }
            }

            this.loading = true;

            const formData = new FormData();
            formData.append("store_id", this.$store.state.auth.user.id);
            formData.append("photo", this.item.photo);
            formData.append("name", this.item.name);
            formData.append("price", this.item.price);
            formData.append("expiry_date", this.item.expiry_date);
            const response = await axios.post("/api/items", formData);

            this.loading = false;

            if (response.status === UNPROCESSABLE_ENTITY) {
                if (response.data.errors.photo) {
                    this.errors.photo = response.data.errors.photo;
                }
                if (response.data.errors.name) {
                    this.errors.name = response.data.errors.name;
                }
                if (response.data.errors.price) {
                    this.errors.price = response.data.errors.price;
                }
                if (response.data.errors.expiry_date) {
                    this.errors.expiry_date = response.data.errors.expiry_date;
                }
                return false;
            }

            if (response.status !== CREATED) {
                this.$store.commit("error/setCode", response.status);
                return false;
            }

            this.$store.commit("message/setContent", {
                content: "商品が出品されました！",
                timeout: 5000,
            });

            this.$router.push("/store-mypage");
        },
    },
    created() {
        const htmlReplaceCancel = (e) => {
            e.preventDefault();
            return false;
        };
        document.addEventListener("dragover", htmlReplaceCancel);
        document.addEventListener("drop", htmlReplaceCancel);
    },
};
</script>
