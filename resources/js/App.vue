<template>
    <div class="wrapper">
        <Header></Header>
        <Message></Message>
        <RouterView />
        <Footer></Footer>
    </div>
</template>

<script>
import Header from "./components/Header.vue";
import Message from "./components/Message.vue";
import Footer from "./components/Footer.vue";
import { NOT_FOUND, UNAUTHORIZED_2, INTERNAL_SERVER_ERROR } from "./util";

export default {
    components: {
        Header,
        Message,
        Footer,
    },
    computed: {
        errorCode() {
            return this.$store.state.error.code;
        },
    },
    watch: {
        errorCode: {
            async handler(val) {
                if (val === INTERNAL_SERVER_ERROR) {
                    this.$router.push("/500");
                } else if (val === UNAUTHORIZED_2) {
                    await axios.get("/api/refresh-token");
                    this.$store.commit("auth/setUser", null);
                    this.$router.push("/login");
                } else if (val === NOT_FOUND) {
                    this.$router.push("/not-found");
                }
            },
            immediate: true,
        },
        $route() {
            this.$store.commit("error/setCode", null);
        },
    },
};
</script>
