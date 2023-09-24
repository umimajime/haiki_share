const state = {
    content: "",
};

const mutations = {
    // stateのcontentにメッセージを格納するmutation
    setContent(state, { content, timeout }) {
        state.content = content;

        if (typeof timeout === "undefined") {
            timeout = 3000;
        }

        setTimeout(() => (state.content = ""), timeout);
    },
};

export default {
    namespaced: true,
    state,
    mutations,
};
