const state = {
    code: null,
};

const mutations = {
    // stateのcodeにステータスコードを格納するmutation
    setCode(state, code) {
        state.code = code;
    },
};

export default {
    namespaced: true,
    state,
    mutations,
};
