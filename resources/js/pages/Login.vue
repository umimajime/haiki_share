<template>
    <main class="l-main">
        <h2 class="c-title" v-if="!user.isStore">
            <span>利用者側ログイン画面</span>
        </h2>
        <h2 class="c-title" v-if="user.isStore">
            <span>コンビニ側ログイン画面</span>
        </h2>
        <Loader v-show="loading"></Loader>
        <form class="c-form" @submit.prevent="login" v-show="!loading">
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
            <div class="c-form__group">
                <RouterLink to="/pass-reminder-send" v-if="!user.isStore">
                    パスワードを忘れた方はコチラ
                </RouterLink>
                <RouterLink
                    to="/pass-reminder-send?isStore=true"
                    v-if="user.isStore"
                >
                    パスワードを忘れた方はコチラ
                </RouterLink>
            </div>
            <div class="c-form__group">
                <input class="c-button" type="submit" value="ログイン" />
            </div>
        </form>
    </main>
</template>

<script>
import { mapState } from "vuex";
import Loader from "../components/Loader.vue";

export default {
    components: {
        Loader,
    },
    data() {
        return {
            loading: false,
            user: {
                email: "",
                password: "",
                isStore: this.$route.query.isStore,
            },
            errors: {
                email: null,
                password: null,
            },
        };
    },
    computed: mapState({
        // apiとの通信可否を返却するプロパティ
        apiStatus: (state) => state.auth.apiStatus,
        // ログインのエラーを返却するプロパティ
        loginErrors: (state) => state.auth.loginErrorMessages,
    }),
    watch: {
        // クエリパラメーターを適用する関数
        $route() {
            "isStore" in this.$route.query
                ? (this.user.isStore = this.$route.query.isStore)
                : (this.user.isStore = false);
        },
        // メールアドレスのバリデーションチェックをする関数
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
        // パスワードのバリデーションチェックをする関数
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
        // ログインでエラーがあった場合にエラーを返却する関数
        loginErrors: function () {
            if (this.loginErrors.email) {
                this.errors.email = [];
                for (let i = 0; i < this.loginErrors.email.length; i++) {
                    this.errors.email.push(this.loginErrors.email[i]);
                }
            }
            if (this.loginErrors.password) {
                this.errors.email = [];
                for (let i = 0; i < this.loginErrors.password.length; i++) {
                    this.errors.password.push(this.loginErrors.password[i]);
                }
            }
        },
    },
    methods: {
        // ログインをするメソッド
        async login() {
            let errArr = Object.values(this.errors);

            for (let i = 0; i < errArr.length; i++) {
                if (errArr[i] !== null && errArr[i].length !== 0) {
                    return;
                }
            }

            this.loading = true;

            await this.$store.dispatch("auth/login", this.user);

            this.loading = false;

            if (this.apiStatus) {
                this.$store.commit("message/setContent", {
                    content: "ログインしました！",
                    timeout: 5000,
                });

                if (this.user.isStore) {
                    this.$router.push("/store-mypage");
                } else {
                    this.$router.push("/user-mypage");
                }
            }
        },
        // エラーを消すメソッド
        clearError() {
            this.$store.commit("auth/setLoginErrorMessages", null);
        },
    },
    created() {
        this.clearError();
    },
};
</script>
