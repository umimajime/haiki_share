<template>
    <main class="l-main">
        <h2 class="c-title"><span>パスワードリマインダー送信画面</span></h2>
        <Loader v-show="loading"></Loader>
        <form
            class="c-form"
            v-show="!loading"
            @submit.prevent="passReminderSend"
        >
            <div class="c-form__group validation">
                <ul v-if="errors.email">
                    <li v-for="msg in errors.email" :key="msg">
                        {{ msg }}
                    </li>
                </ul>
            </div>
            <div class="c-form__group">
                入力されたメールアドレス宛てにパスワード更新画面へのURLが記載されたメールを送信いたします。<br />
                メールに記載のURLからパスワード更新画面に遷移し、新しいパスワードを登録してください。
            </div>
            <div
                class="c-form__group"
                :class="{
                    success: errors.email !== null && errors.email.length === 0,
                    error: errors.email !== null && errors.email.length !== 0,
                }"
            >
                <label for="email">メールアドレス<span>必須</span></label>
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
            <div class="c-form__group">
                <input class="c-button" type="submit" value="送信" />
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
                isStore: this.$route.query.isStore,
            },
            errors: {
                email: null,
            },
        };
    },
    computed: mapState({
        // apiとの通信可否を返却するプロパティ
        apiStatus: (state) => state.auth.apiStatus,
        // パスワードリマインダー送信のエラーを返却するプロパティ
        passReminderSendErrors: (state) =>
            state.auth.passReminderSendErrorMessages,
    }),
    watch: {
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
        // パスワードリマインダー送信でエラーがあった場合にエラーを返却する関数
        passReminderSendErrors: function () {
            if (JSON.parse(JSON.stringify(this.passReminderSendErrors)).email) {
                this.errors.email = [];
                for (
                    let i = 0;
                    i <
                    JSON.parse(JSON.stringify(this.passReminderSendErrors))
                        .email.length;
                    i++
                ) {
                    this.errors.email.push(
                        this.passReminderSendErrors.email[i]
                    );
                }
            }
        },
    },
    methods: {
        // パスワードリマインダーを送信するメソッド
        async passReminderSend() {
            let errArr = Object.values(this.errors);

            for (let i = 0; i < errArr.length; i++) {
                if (errArr[i] !== null && errArr[i].length !== 0) {
                    return;
                }
            }

            this.loading = true;

            await this.$store.dispatch("auth/passReminderSend", this.user);

            this.loading = false;

            if (this.apiStatus) {
                this.$store.commit("message/setContent", {
                    content: "メールを送信しました！",
                    timeout: 5000,
                });
            }
        },
        // エラーを消すメソッド
        clearError() {
            this.$store.commit("auth/setPassReminderSendErrorMessages", null);
        },
    },
    created() {
        this.clearError();
    },
};
</script>
