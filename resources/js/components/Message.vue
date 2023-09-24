<template>
    <Transition>
        <div class="c-message" v-show="message">
            {{ message }}
        </div>
    </Transition>
</template>

<style>
/* アニメーション中のスタイル */
.v-leave-active,
.v-enter-active {
    transition: opacity 1s;
}

/* 表示アニメーション */
.v-enter {
    opacity: 0;
}
.v-enter-to {
    opacity: 1;
}

/* 非表示アニメーション */
.v-leave {
    opacity: 1;
}
.v-leave-to {
    opacity: 0;
}
</style>

<script>
import { mapState } from "vuex";

export default {
    computed: {
        ...mapState({
            message: (state) => state.message.content,
        }),
    },
};
</script>
