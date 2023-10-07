<template>
    <div>
        <header class="l-header">
            <div class="l-header__container">
                <RouterLink to="/">
                    <h1 class="l-header__logo">haiki share</h1>
                </RouterLink>
                <div
                    class="l-header__hamburger"
                    :class="{ 'is-active': open }"
                    @click="open = !open"
                >
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </header>
        <div class="l-navigation" :class="{ 'is-active': open }">
            <nav class="l-navigation__nav">
                <ul class="l-navigation__menu">
                    <RouterLink to="/register" v-if="!isLogin">
                        <li class="c-button" @click="open = !open">
                            利用者側ユーザー登録
                        </li>
                    </RouterLink>
                    <RouterLink to="/login" v-if="!isLogin">
                        <li class="c-button mb60" @click="open = !open">
                            利用者側ログイン
                        </li>
                    </RouterLink>
                    <RouterLink to="/register?isStore=true" v-if="!isLogin">
                        <li class="c-button" @click="open = !open">
                            コンビニ側ユーザー登録
                        </li>
                    </RouterLink>
                    <RouterLink to="/login?isStore=true" v-if="!isLogin">
                        <li class="c-button" @click="open = !open">
                            コンビニ側ログイン
                        </li>
                    </RouterLink>
                    <RouterLink to="/user-mypage" v-if="isLogin">
                        <li class="c-button mb60" @click="open = !open">
                            マイページ
                        </li>
                    </RouterLink>
                    <li
                        class="c-button"
                        @click="
                            logout();
                            open = !open;
                        "
                        v-if="isLogin"
                    >
                        ログアウト
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</template>

<script>
import { mapState, mapGetters } from "vuex";

export default {
    data: function () {
        return { open: false };
    },
    computed: {
        ...mapState({
            apiStatus: (state) => state.auth.apiStatus,
        }),
        ...mapGetters({
            isLogin: "auth/check",
            isStore: "auth/isStore",
        }),
    },
    methods: {
        // ログアウトのactionを実行するメソッド
        async logout() {
            if (this.isStore) {
                await this.$store.dispatch("auth/logout");

                if (this.apiStatus) {
                    this.$store.commit("message/setContent", {
                        content: "ログアウトしました！",
                        timeout: 5000,
                    });

                    this.$router.push({
                        path: "/login",
                        query: { isStore: true },
                    });
                }
            } else {
                await this.$store.dispatch("auth/logout");

                if (this.apiStatus) {
                    this.$store.commit("message/setContent", {
                        content: "ログアウトしました！",
                        timeout: 5000,
                    });

                    this.$router.push("/login");
                }
            }
        },
    },
};
</script>
