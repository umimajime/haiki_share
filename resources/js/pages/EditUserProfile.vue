<template>
    <main class="l-main">
        <h2 class="c-title">
            <span>利用者側プロフィール編集画面</span>
        </h2>
        <Loader v-show="loading"></Loader>
        <form
            class="c-form"
            @submit.prevent="editUserProfile"
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
            <div class="c-form__group">
                ＊プロフィールの編集が完了するとログアウトします。再度ログインしてご利用ください。
            </div>
            <div
                class="c-form__group"
                :class="{
                    success: errors.email !== null && errors.email.length === 0,
                    error: errors.email !== null && errors.email.length !== 0,
                }"
            >
                <label for="email"
                    >メールアドレス<span class="optional">＊任意</span></label
                >
                <input
                    type="email"
                    id="email"
                    v-model="user.email"
                    maxlength="255"
                />
                <ul>
                    <li>・255文字以下で入力してください。</li>
                    <li>・Eメールの形式で入力してください。</li>
                    <li>・登録済みのメールアドレスで編集は出来ません。</li>
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
                <label for="password"
                    >新しいパスワード<span class="optional">＊任意</span></label
                >
                <input
                    type="password"
                    id="password"
                    v-model="user.password"
                    minlength="8"
                    maxlength="24"
                />
                <ul>
                    <li>・8文字以上24文字以下で入力してください。</li>
                    <li>・半角英数字・記号が利用可能です。</li>
                    <li>
                        ・記号は、ピリオド(.)、スラッシュ(/)、クエスチョンマーク(?)、ハイフン(-)が利用可能です。
                    </li>
                    <li>・登録済みのパスワードで編集は出来ません。</li>
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
                    >新しいパスワード(再入力)<span class="optional"
                        >＊任意</span
                    ></label
                >
                <input
                    type="password"
                    id="password_confirmation"
                    v-model="user.password_confirmation"
                    minlength="8"
                    maxlength="24"
                />
            </div>
            <div class="c-form__group">
                <input class="c-button" type="submit" value="編集" />
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
            user: {
                email: this.$store.state.auth.user.email,
                password: "",
                password_confirmation: "",
            },
            errors: {
                email: null,
                password: null,
                password_confirmation: null,
            },
        };
    },
    computed: mapState({
        // apiとの通信可否を返却するプロパティ
        apiStatus: (state) => state.auth.apiStatus,
        // 利用者側プロフィール編集のエラーを返却するプロパティ
        editUserProfileError: (state) =>
            state.auth.editUserProfileErrorMessages,
    }),
    watch: {
        // メールアドレスのバリデーションチェックをする関数
        "user.email": function () {
            const emailPattarn = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            this.errors.email = [];
            if (this.user.email.length === 0) {
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
        // 利用者側プロフィール編集でエラーがあった場合にエラーを返却する関数
        editUserProfileError: function () {
            if (this.editUserProfileError.email) {
                this.errors.email = [];
                for (
                    let i = 0;
                    i < this.editUserProfileError.email.length;
                    i++
                ) {
                    this.errors.email.push(this.editUserProfileError.email[i]);
                }
                this.errors.email = Array.from(new Set(this.errors.email));
            }
            if (this.editUserProfileError.password) {
                this.errors.password = [];
                for (
                    let i = 0;
                    i < this.editUserProfileError.password.length;
                    i++
                ) {
                    this.errors.password.push(
                        this.editUserProfileError.password[i]
                    );
                }
                this.errors.password = Array.from(
                    new Set(this.errors.password)
                );
            }
        },
    },
    methods: {
        // 利用者側のプロフィールを編集するメソッド
        async editUserProfile() {
            let errArr = Object.values(this.errors);

            for (let i = 0; i < errArr.length; i++) {
                if (errArr[i] !== null && errArr[i].length !== 0) {
                    return;
                }
            }

            this.loading = true;

            await this.$store.dispatch("auth/editUserProfile", this.user);

            this.loading = false;

            if (this.apiStatus) {
                await this.$store.dispatch("auth/logout");

                if (this.apiStatus) {
                    this.$store.commit("message/setContent", {
                        content:
                            "プロフィールを編集しました！ログアウトします！",
                        timeout: 5000,
                    });

                    this.$router.push("/login");
                }
            }
        },
        // エラーを消すメソッド
        clearError() {
            this.$store.commit("auth/setEditUserProfileErrorMessages", null);
        },
    },
    created() {
        this.clearError();
    },
};
</script>
