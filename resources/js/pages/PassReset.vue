<template>
    <main class="l-main">
        <h2 class="c-title" v-if="user.token_valid_flg === 0">
            <span>パスワードリセット画面</span>
        </h2>
        <Loader v-show="loading"></Loader>
        <form
            class="c-form"
            @submit.prevent="passReset"
            v-if="user.token_valid_flg === 0"
            v-show="!loading"
        >
            <div class="c-form__group validation">
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
                        errors.password !== null &&
                        errors.password.length === 0,
                    error:
                        errors.password !== null &&
                        errors.password.length !== 0,
                }"
            >
                <label for="password">新しいパスワード<span>必須</span></label>
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
                    >新しいパスワード（再入力）<span>必須</span></label
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
                <input class="c-button" type="submit" value="リセット" />
            </div>
        </form>
        <div v-if="user.token_valid_flg === 1">
            <p>
                パスワードリセットリンクの有効期限が切れています。再度パスワードリマインダー送信画面からメールを送信してください。
            </p>
            <RouterLink to="/pass-reminder-send"
                >パスワードリマインダー送信画面へ</RouterLink
            >
        </div>
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
                email: this.$route.query.email,
                token: this.$route.query.token,
                token_valid_flg: null,
                password: "",
                password_confirmation: "",
                isStore: this.$route.query.isStore,
            },
            errors: {
                password: [],
                password_confirmation: [],
            },
        };
    },
    computed: mapState({
        // apiとの通信可否を返却するプロパティ
        apiStatus: (state) => state.auth.apiStatus,
        // パスワードリセットのエラーを返却するプロパティ
        passResetErrors: (state) => state.auth.passResetErrorMessages,
        // トークンチェックのエラーを返却するプロパティ
        checkTokenErrors: (state) => state.auth.checkTokenErrorMessages,
    }),
    watch: {
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
        // パスワード（再入力）のバリデーションチェックをする関数
        "user.password_confirmation": function () {
            this.errors.password_confirmation = [];
            if (this.user.password !== this.user.password_confirmation) {
                this.errors.password_confirmation.push(
                    "パスワードとパスワード（再入力）が一致していません。"
                );
            }
        },
        // パスワードリセットでエラーがあった場合にエラーを返却する関数
        passResetErrors: function () {
            if (this.passResetErrors === null) {
                return;
            }
            if (JSON.parse(JSON.stringify(this.passResetErrors)).password) {
                this.errors.password = [];
                for (
                    let i = 0;
                    i <
                    JSON.parse(JSON.stringify(this.passResetErrors)).password
                        .length;
                    i++
                ) {
                    this.errors.password.push(
                        JSON.parse(JSON.stringify(this.passResetErrors))
                            .password[i]
                    );
                }
            }
        },
        // トークンチェックでエラーがあった場合にエラーを返却する関数
        checkTokenErrors: function () {
            if (this.checkTokenErrors === null) {
                return;
            }
            this.user.token_valid_flg = 1;
        },
    },
    methods: {
        // トークンのチェックをするメソッド
        async checkToken() {
            await this.$store.dispatch("auth/checkToken", this.user);
            if (this.apiStatus) {
                this.user.token_valid_flg = 0;
            }
        },
        // パスワードリセットをするメソッド
        async passReset() {
            let errArr = Object.values(this.errors);

            for (let i = 0; i < errArr.length; i++) {
                if (errArr[i] !== null && errArr[i].length !== 0) {
                    return;
                }
            }

            this.loading = true;

            await this.$store.dispatch("auth/passReset", this.user);

            this.loading = false;

            if (this.apiStatus) {
                this.$store.commit("message/setContent", {
                    content: "パスワードを変更しました！",
                    timeout: 5000,
                });

                this.user.isStore === "false"
                    ? this.$router.push("/login")
                    : this.$router.push("/login?isStore=true");
            }
        },
        // エラーを消すメソッド
        clearError() {
            this.$store.commit("auth/setPassResetErrorMessages", null);
        },
    },
    created() {
        this.clearError();
        this.checkToken();
    },
};
</script>
