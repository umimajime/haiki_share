<template>
    <main class="l-main">
        <h2 class="c-title" v-if="!isStore">
            <span>利用者側ユーザー登録</span>
        </h2>
        <h2 class="c-title" v-if="isStore">
            <span>コンビニ側ユーザー登録</span>
        </h2>
        <Loader v-show="loading"></Loader>
        <form
            class="c-form"
            @submit.prevent="register"
            v-if="!isStore"
            v-show="!loading"
        >
            <div class="c-form__group validation">
                <ul v-if="errors.email">
                    <li v-for="msg in errors.email" :key="msg">
                        {{ msg }}
                    </li>
                </ul>
                <ul v-if="errors.password">
                    <li v-for="msg in errors.password" :key="msg">
                        {{ msg }}
                    </li>
                </ul>
                <ul v-if="errors.password_confirmation">
                    <li v-for="msg in errors.password_confirmation" :key="msg">
                        {{ msg }}
                    </li>
                </ul>
            </div>
            <div
                class="c-form__group"
                :class="{
                    success: errors.email !== null && errors.email.length === 0,
                    error: errors.email !== null && errors.email.length !== 0,
                }"
            >
                <label for="email">メールアドレス<span>＊必須</span></label>
                <input
                    type="email"
                    id="email"
                    v-model="user.email"
                    required
                    maxlength="255"
                />
                <ul>
                    <li>・255文字以下で入力してください。</li>
                    <li>・Eメールの形式で入力してください。</li>
                </ul>
            </div>
            <div
                class="c-form__group"
                :class="{
                    success:
                        errors.password !== null &&
                        errors.password.length === 0,
                    error:
                        errors.password !== null &&
                        errors.password.length !== 0,
                }"
            >
                <label for="password">パスワード<span>＊必須</span></label>
                <input
                    type="password"
                    id="password"
                    v-model="user.password"
                    required
                    minlength="8"
                    maxlength="24"
                />
                <ul>
                    <li>・8文字以上24文字以下で入力してください。</li>
                    <li>・半角英数字・記号が利用可能です。</li>
                    <li>
                        ・記号は、ピリオド(.)、スラッシュ(/)、クエスチョンマーク(?)、ハイフン(-)が利用可能です。
                    </li>
                </ul>
            </div>
            <div
                class="c-form__group"
                :class="{
                    success:
                        errors.password_confirmation !== null &&
                        errors.password_confirmation.length === 0,
                    error:
                        errors.password_confirmation !== null &&
                        errors.password_confirmation.length !== 0,
                }"
            >
                <label for="password_confirmation"
                    >パスワード(再入力)<span>＊必須</span></label
                >
                <input
                    type="password"
                    id="password_confirmation"
                    v-model="user.password_confirmation"
                    required
                    minlength="8"
                    maxlength="24"
                />
            </div>
            <div class="c-form__group">
                <input class="c-button" type="submit" value="登録" />
            </div>
        </form>
        <form
            class="c-form"
            @submit.prevent="register"
            v-if="isStore"
            v-show="!loading"
        >
            <div class="c-form__group validation">
                <ul v-if="errors.store_name">
                    <li v-for="msg in errors.store_name" :key="msg">
                        {{ msg }}
                    </li>
                </ul>
                <ul v-if="errors.branch_name">
                    <li v-for="msg in errors.branch_name" :key="msg">
                        {{ msg }}
                    </li>
                </ul>
                <ul v-if="errors.post_code">
                    <li v-for="msg in errors.post_code" :key="msg">
                        {{ msg }}
                    </li>
                </ul>
                <ul v-if="errors.prefecture">
                    <li v-for="msg in errors.prefecture" :key="msg">
                        {{ msg }}
                    </li>
                </ul>
                <ul v-if="errors.municipality">
                    <li v-for="msg in errors.municipality" :key="msg">
                        {{ msg }}
                    </li>
                </ul>
                <ul v-if="errors.address">
                    <li v-for="msg in errors.address" :key="msg">
                        {{ msg }}
                    </li>
                </ul>
                <ul v-if="errors.building">
                    <li v-for="msg in errors.building" :key="msg">
                        {{ msg }}
                    </li>
                </ul>
                <ul v-if="errors.email">
                    <li v-for="msg in errors.email" :key="msg">
                        {{ msg }}
                    </li>
                </ul>
                <ul v-if="errors.password">
                    <li v-for="msg in errors.password" :key="msg">
                        {{ msg }}
                    </li>
                </ul>
                <ul v-if="errors.password_confirmation">
                    <li v-for="msg in errors.password_confirmation" :key="msg">
                        {{ msg }}
                    </li>
                </ul>
            </div>
            <div
                class="c-form__group"
                :class="{
                    success:
                        errors.store_name !== null &&
                        errors.store_name.length === 0,
                    error:
                        errors.store_name !== null &&
                        errors.store_name.length !== 0,
                }"
            >
                <label for="store_name">コンビニ名<span>＊必須</span></label>
                <input
                    type="text"
                    id="store_name"
                    v-model="store.store_name"
                    required
                    maxlength="255"
                />
                <ul>
                    <li>・255文字以下で入力してください。</li>
                </ul>
            </div>
            <div
                class="c-form__group"
                :class="{
                    success:
                        errors.branch_name !== null &&
                        errors.branch_name.length === 0,
                    error:
                        errors.branch_name !== null &&
                        errors.branch_name.length !== 0,
                }"
            >
                <label for="branch_name"
                    >支店名<span class="optional">＊任意</span></label
                >
                <input
                    type="text"
                    id="branch_name"
                    v-model="store.branch_name"
                    maxlength="255"
                />
                <ul>
                    <li>・255文字以下で入力してください。</li>
                </ul>
            </div>
            <div
                class="c-form__group"
                :class="{
                    success:
                        errors.post_code !== null &&
                        errors.post_code.length === 0,
                    error:
                        errors.post_code !== null &&
                        errors.post_code.length !== 0,
                }"
            >
                <label for="post_code">郵便番号<span>＊必須</span></label>
                <input
                    type="text"
                    id="post_code"
                    v-model="store.post_code"
                    required
                    class="post_code"
                />
                <button
                    type="button"
                    class="c-button"
                    :class="{
                        active:
                            errors.post_code !== null &&
                            errors.post_code.length === 0,
                    }"
                    @click="postCodeLookup"
                >
                    住所検索
                </button>
                <ul>
                    <li>・半角数字・半角記号で入力してください。</li>
                    <li>
                        ・郵便番号の形式で入力してください。（例：000-0000）
                    </li>
                </ul>
            </div>
            <div
                class="c-form__group"
                :class="{
                    success:
                        errors.prefecture !== null &&
                        errors.prefecture.length === 0,
                    error:
                        errors.prefecture !== null &&
                        errors.prefecture.length !== 0,
                }"
            >
                <label for="prefecture">都道府県<span>＊必須</span></label>
                <select
                    name="prefecture"
                    id="prefecture"
                    class="js-get-prefecture"
                    v-model="store.prefecture"
                >
                    <option value="選択してください">選択してください</option>
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
            </div>
            <div
                class="c-form__group"
                :class="{
                    success:
                        errors.municipality !== null &&
                        errors.municipality.length === 0,
                    error:
                        errors.municipality !== null &&
                        errors.municipality.length !== 0,
                }"
            >
                <label for="municipality">市区町村<span>＊必須</span></label>
                <select
                    name="municipality"
                    id="municipality"
                    class="js-get-municipality"
                    v-model="store.municipality"
                >
                    <option value="選択してください">選択してください</option>
                </select>
                <ul>
                    <li>
                        ・市区町村の選択項目は、都道府県の選択項目に応じて変更されるため、まず都道府県を選択してください。
                    </li>
                </ul>
            </div>
            <div
                class="c-form__group"
                :class="{
                    success:
                        errors.address !== null && errors.address.length === 0,
                    error:
                        errors.address !== null && errors.address.length !== 0,
                }"
            >
                <label for="address">番地<span>＊必須</span></label>
                <input
                    type="text"
                    id="address"
                    v-model="store.address"
                    maxlength="255"
                    required
                />
                <ul>
                    <li>・255文字以下で入力してください。</li>
                    <li>・（例：南青山０ー０ー０）</li>
                </ul>
            </div>
            <div
                class="c-form__group"
                :class="{
                    success:
                        errors.building !== null &&
                        errors.building.length === 0,
                    error:
                        errors.building !== null &&
                        errors.building.length !== 0,
                }"
            >
                <label for="building"
                    >建物名・部屋番号<span class="optional">＊任意</span></label
                >
                <input
                    type="text"
                    id="building"
                    v-model="store.building"
                    maxlength="255"
                />
                <ul>
                    <li>・255文字以下で入力してください。</li>
                    <li>・（例：haikiマンション　０００号室）</li>
                </ul>
            </div>
            <div
                class="c-form__group"
                :class="{
                    success: errors.email !== null && errors.email.length === 0,
                    error: errors.email !== null && errors.email.length !== 0,
                }"
            >
                <label for="email">メールアドレス<span>＊必須</span></label>
                <input
                    type="email"
                    id="email"
                    v-model="store.email"
                    required
                    maxlength="255"
                />
                <ul>
                    <li>・255文字以下で入力してください。</li>
                    <li>
                        ・Eメールの形式で入力してください。（例：haiki.share@gmail.com）
                    </li>
                </ul>
            </div>
            <div
                class="c-form__group"
                :class="{
                    success:
                        errors.password !== null &&
                        errors.password.length === 0,
                    error:
                        errors.password !== null &&
                        errors.password.length !== 0,
                }"
            >
                <label for="password">パスワード<span>＊必須</span></label>
                <input
                    type="password"
                    id="password"
                    v-model="store.password"
                    required
                    minlength="8"
                    maxlength="24"
                />
                <ul>
                    <li>・8文字以上24文字以下で入力してください。</li>
                    <li>・半角英数字・記号が利用可能です。</li>
                    <li>
                        ・記号は、ピリオド(.)、スラッシュ(/)、クエスチョンマーク(?)、ハイフン(-)が利用可能です。
                    </li>
                </ul>
            </div>
            <div
                class="c-form__group"
                :class="{
                    success:
                        errors.password_confirmation !== null &&
                        errors.password_confirmation.length === 0,
                    error:
                        errors.password_confirmation !== null &&
                        errors.password_confirmation.length !== 0,
                }"
            >
                <label for="password_confirmation"
                    >パスワード(再入力)<span>＊必須</span></label
                >
                <input
                    type="password"
                    id="password_confirmation"
                    v-model="store.password_confirmation"
                    required
                    minlength="8"
                    maxlength="24"
                />
            </div>
            <div class="c-form__group">
                <input class="c-button" type="submit" value="登録" />
            </div>
        </form>
    </main>
</template>

<script>
import { mapState } from "vuex";
import { OK } from "../util";
import Loader from "../components/Loader.vue";

export default {
    components: {
        Loader,
    },
    data() {
        return {
            loading: false,
            isStore: this.$route.query.isStore,
            user: {
                email: "",
                password: "",
                password_confirmation: "",
            },
            store: {
                store_name: "",
                branch_name: "",
                post_code: "",
                prefecture: "選択してください",
                municipality: "選択してください",
                address: "",
                building: "",
                email: "",
                password: "",
                password_confirmation: "",
            },
            errors: {
                store_name: null,
                branch_name: null,
                post_code: null,
                prefecture: null,
                municipality: null,
                address: null,
                building: null,
                email: null,
                password: null,
                password_confirmation: null,
            },
        };
    },
    computed: mapState({
        // apiとの通信可否を返却するプロパティ
        apiStatus: (state) => state.auth.apiStatus,
        // ユーザー登録のエラーを返却するプロパティ
        registerErrors: (state) => state.auth.registerErrorMessages,
    }),
    watch: {
        // クエリパラメーターを適用する関数
        $route() {
            "isStore" in this.$route.query
                ? (this.isStore = this.$route.query.isStore)
                : (this.isStore = false);
        },
        // 利用者側のメールアドレスのバリデーションチェックをする関数
        "user.email": function () {
            const emailPattarn = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            this.errors.email = [];
            if (this.user.email.length === 0) {
                this.errors.email.push("メールアドレスは入力必須です。");
                return;
            }
            if (this.user.email.length >= 255) {
                this.errors.email.push(
                    "メールアドレスは255文字以下で入力してください。"
                );
            }
            if (!emailPattarn.test(this.user.email)) {
                this.errors.email.push(
                    "メールアドレスはEメールの形式で入力してください。"
                );
            }
        },
        // 利用者側のパスワードのバリデーションチェックをする関数
        "user.password": function () {
            const passwordPattarn = /^[a-zA-Z0-9.?/-]{8,24}$/;
            this.errors.password = [];
            if (this.user.password.length === 0) {
                this.errors.password.push("パスワードは入力必須です。");
                return;
            }
            if (
                this.user.password.length <= 7 ||
                this.user.password.length >= 25
            ) {
                this.errors.password.push(
                    "パスワードは8文字以上24文字以下で入力してください。"
                );
            }
            if (!passwordPattarn.test(this.user.password)) {
                this.errors.password.push(
                    "パスワードは半角英数字・記号（ピリオド(.)、スラッシュ(/)、クエスチョンマーク(?)、ハイフン(-)）で入力してください。"
                );
            }
        },
        // 利用者側のパスワード（再入力）のバリデーションチェックをする関数
        "user.password_confirmation": function () {
            this.errors.password_confirmation = [];
            if (this.user.password !== this.user.password_confirmation) {
                this.errors.password_confirmation.push(
                    "パスワードとパスワード（再入力）が一致していません。"
                );
            }
        },
        // コンビニ側のコンビニ名のバリデーションチェックをする関数
        "store.store_name": function () {
            this.errors.store_name = [];
            if (this.store.store_name.length === 0) {
                this.errors.store_name.push("コンビニ名は入力必須です。");
                return;
            }
            if (this.store.store_name.length >= 255) {
                this.errors.store_name.push(
                    "コンビニ名は255文字以下で入力してください。"
                );
            }
        },
        // コンビニ側の支店名のバリデーションチェックをする関数
        "store.branch_name": function () {
            this.errors.branch_name = [];
            if (this.store.branch_name.length >= 255) {
                this.errors.branch_name.push(
                    "支店名は255文字以下で入力してください。"
                );
            }
        },
        // コンビニ側の郵便番号のバリデーションチェックをする関数
        "store.post_code": function () {
            const halfSizePattarn = /^[0-9-]*$/;
            const postCodePattarn = /^\d{3}-\d{4}$/;
            this.errors.post_code = [];
            if (this.store.post_code.length === 0) {
                this.errors.post_code.push("郵便番号は入力必須です。");
                return;
            }
            if (!halfSizePattarn.test(this.store.post_code)) {
                this.errors.post_code.push(
                    "郵便番号は半角数字・半角記号で入力してください。"
                );
            }
            if (!postCodePattarn.test(this.store.post_code)) {
                this.errors.post_code.push(
                    "郵便番号の形式で入力してください。"
                );
            }
        },
        // コンビニ側の都道府県のバリデーションチェックをする関数
        "store.prefecture": async function () {
            this.errors.prefecture = [];
            if (this.store.prefecture === "選択してください") {
                this.store.municipality = "選択してください";

                const select = document.querySelector(".js-get-municipality");
                let options = select.options;

                while (true) {
                    if (options.length === 1) {
                        break;
                    }
                    options[1].remove();
                }

                this.errors.prefecture.push("都道府県は選択必須です。");
                return;
            }

            await this.showMunicipality();

            if (this.store.municipality) {
                const select = document.querySelector(".js-get-municipality");
                let options = select.options;
                for (let i = 0; i < options.length; i++) {
                    if (options[i].value === this.store.municipality) {
                        options[i].selected = true;
                    }
                }
            }
        },
        // コンビニ側の市区町村のバリデーションチェックをする関数
        "store.municipality": function () {
            this.errors.municipality = [];
            if (this.store.municipality === "選択してください") {
                this.errors.municipality.push("市区町村は選択必須です。");
                return;
            }
        },
        // コンビニ側の番地のバリデーションチェックをする関数
        "store.address": function () {
            this.errors.address = [];
            if (this.store.address.length === 0) {
                this.errors.address.push("番地は入力必須です。");
                return;
            }
            if (this.store.address.length >= 255) {
                this.errors.address.push(
                    "番地は255文字以下で入力してください。"
                );
            }
        },
        // コンビニ側の建物名・部屋番号のバリデーションチェックをする関数
        "store.building": function () {
            this.errors.building = [];
            if (this.store.building.length >= 255) {
                this.errors.building.push(
                    "建物名・部屋番号は255文字以下で入力してください。"
                );
            }
        },
        // コンビニ側のメールアドレスのバリデーションチェックをする関数
        "store.email": function () {
            const emailPattarn = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            this.errors.email = [];
            if (this.store.email.length === 0) {
                this.errors.email.push("メールアドレスは入力必須です。");
                return;
            }
            if (this.store.email.length >= 255) {
                this.errors.email.push(
                    "メールアドレスは255文字以下で入力してください。"
                );
            }
            if (!emailPattarn.test(this.store.email)) {
                this.errors.email.push(
                    "メールアドレスはEメールの形式で入力してください。"
                );
            }
        },
        // コンビニ側のパスワードのバリデーションチェックをする関数
        "store.password": function () {
            const passwordPattarn = /^[a-zA-Z0-9.?/-]{8,24}$/;
            this.errors.password = [];
            if (this.store.password.length === 0) {
                this.errors.password.push("パスワードは入力必須です。");
                return;
            }
            if (
                this.store.password.length <= 7 ||
                this.store.password.length >= 25
            ) {
                this.errors.password.push(
                    "パスワードは8文字以上24文字以下で入力してください。"
                );
            }
            if (!passwordPattarn.test(this.store.password)) {
                this.errors.password.push(
                    "パスワードは半角英数字・記号（ピリオド(.)、スラッシュ(/)、クエスチョンマーク(?)、ハイフン(-)）で入力してください。"
                );
            }
        },
        // コンビニ側のパスワード（再入力）のバリデーションチェックをする関数
        "store.password_confirmation": function () {
            this.errors.password_confirmation = [];
            if (this.store.password !== this.store.password_confirmation) {
                this.errors.password_confirmation.push(
                    "パスワードとパスワード（再入力）が一致していません。"
                );
            }
        },
        // ユーザー登録でエラーがあった場合にエラーを返却する関数
        registerErrors: function () {
            if (this.registerErrors === null) {
                return;
            }
            if (this.registerErrors.store_name) {
                this.errors.store_name = [];
                for (
                    let i = 0;
                    i < this.registerErrors.store_name.length;
                    i++
                ) {
                    this.errors.store_name.push(
                        this.registerErrors.store_name[i]
                    );
                }
                this.errors.store_name = Array.from(
                    new Set(this.errors.store_name)
                );
            }
            if (this.registerErrors.branch_name) {
                this.errors.branch_name = [];
                for (
                    let i = 0;
                    i < this.registerErrors.branch_name.length;
                    i++
                ) {
                    this.errors.branch_name.push(
                        this.registerErrors.branch_name[i]
                    );
                }
                this.errors.branch_name = Array.from(
                    new Set(this.errors.branch_name)
                );
            }
            if (this.registerErrors.post_code) {
                this.errors.post_code = [];
                for (let i = 0; i < this.registerErrors.post_code.length; i++) {
                    this.errors.post_code.push(
                        this.registerErrors.post_code[i]
                    );
                }
                this.errors.post_code = Array.from(
                    new Set(this.errors.post_code)
                );
            }
            if (this.registerErrors.prefecture) {
                this.errors.prefecture = [];
                for (
                    let i = 0;
                    i < this.registerErrors.prefecture.length;
                    i++
                ) {
                    this.errors.prefecture.push(
                        this.registerErrors.prefecture[i]
                    );
                }
                this.errors.prefecture = Array.from(
                    new Set(this.errors.prefecture)
                );
            }
            if (this.registerErrors.municipality) {
                this.errors.municipality = [];
                for (
                    let i = 0;
                    i < this.registerErrors.municipality.length;
                    i++
                ) {
                    this.errors.municipality.push(
                        this.registerErrors.municipality[i]
                    );
                }
                this.errors.municipality = Array.from(
                    new Set(this.errors.municipality)
                );
            }
            if (this.registerErrors.address) {
                this.errors.address = [];
                for (let i = 0; i < this.registerErrors.address.length; i++) {
                    this.errors.address.push(this.registerErrors.address[i]);
                }
                this.errors.address = Array.from(new Set(this.errors.address));
            }
            if (this.registerErrors.building) {
                this.errors.building = [];
                for (let i = 0; i < this.registerErrors.building.length; i++) {
                    this.errors.building.push(this.registerErrors.building[i]);
                }
                this.errors.building = Array.from(
                    new Set(this.errors.building)
                );
            }
            if (this.registerErrors.email) {
                this.errors.email = [];
                for (let i = 0; i < this.registerErrors.email.length; i++) {
                    this.errors.email.push(this.registerErrors.email[i]);
                }
                this.errors.email = Array.from(new Set(this.errors.email));
            }
            if (this.registerErrors.password) {
                this.errors.password = [];
                for (let i = 0; i < this.registerErrors.password.length; i++) {
                    this.errors.password.push(this.registerErrors.password[i]);
                }
                this.errors.password = Array.from(
                    new Set(this.errors.password)
                );
            }
        },
    },
    methods: {
        // 郵便番号から住所を取得するメソッド
        async postCodeLookup() {
            const response = await axios.get(
                `https://zipcloud.ibsnet.co.jp/api/search?zipcode=${this.store.post_code}`,
                { adapter: jsonpAdapter }
            );

            if (response.status === OK) {
                if (
                    response.data.results === null ||
                    response.data.results[0].address3 === ""
                ) {
                    this.errors.post_code.push(
                        "入力された郵便番号に該当する住所を取得出来ませんでした。"
                    );
                    return false;
                }
                this.store.prefecture = response.data.results[0].prefcode;
                this.store.municipality = response.data.results[0].address2;
                this.store.address = response.data.results[0].address3;
            }

            if (response.status === 400) {
                this.errors.post_code.push("郵便番号が正しくありません。");
                return false;
            }

            if (response.status === 500) {
                this.errors.post_code.push(
                    "現在、住所検索機能はご利用いただけません。"
                );
                return false;
            }
        },
        // 都道府県に属する市区町村を取得するメソッド
        async showMunicipality() {
            const response = await fetch(
                `https://opendata.resas-portal.go.jp/api/v1/cities?prefCode=${this.store.prefecture}`,
                {
                    method: "GET",
                    headers: {
                        "X-API-KEY": "C8Nwloe68E0gQEvmGS5C7UsZoxHoJKX1dqCmTmDt",
                    },
                }
            );
            if (response.status === OK) {
                const data = await response.json();
                const select = document.querySelector(".js-get-municipality");
                while (select.options.length !== 1) {
                    select.options[1].remove();
                }
                for (let i = 0; i < data.result.length; i++) {
                    const option = document.createElement("option");
                    option.text = data.result[i].cityName;
                    option.value = data.result[i].cityName;
                    select.appendChild(option);
                    if (option.value === this.store.municipality) {
                        option.selected = true;
                    }
                }
                return false;
            }

            this.$router.push("/500");
        },
        // ユーザー登録をするメソッド
        async register() {
            let errArr = Object.values(this.errors);

            for (let i = 0; i < errArr.length; i++) {
                if (errArr[i] !== null && errArr[i].length !== 0) {
                    return;
                }
            }

            this.loading = true;

            if (this.isStore) {
                await this.$store.dispatch("auth/register", this.store);
            } else {
                await this.$store.dispatch("auth/register", this.user);
            }

            this.loading = false;

            if (this.apiStatus) {
                this.$store.commit("message/setContent", {
                    content: "ユーザー登録が完了しました！",
                    timeout: 5000,
                });

                if (this.isStore) {
                    this.$router.push("/store-mypage");
                } else {
                    this.$router.push("/user-mypage");
                }
            }
        },
        // エラーを消すメソッド
        clearError() {
            this.$store.commit("auth/setRegisterErrorMessages", null);
        },
    },
    created() {
        this.clearError();
    },
};
</script>
