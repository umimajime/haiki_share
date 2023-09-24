import { OK, CREATED, UNAUTHORIZED, UNPROCESSABLE_ENTITY } from "../util";

const state = {
    user: null,
    apiStatus: null,
    registerErrorMessages: null,
    editUserProfileErrorMessages: null,
    editStoreProfileErrorMessages: null,
    loginErrorMessages: null,
    passReminderSendErrorMessages: null,
    checkTokenErrorMessages: null,
    passResetErrorMessages: null,
};

const getters = {
    check: (state) => !!state.user,
    isStore: (state) => !!state.user.store_name,
};

const mutations = {
    // stateのuserにユーザー情報を格納するmutation
    setUser(state, user) {
        state.user = user;
    },
    // stateのapiStatusにapiとの通信の成功可否を格納するmutation
    setApiStatus(state, status) {
        state.apiStatus = status;
    },
    // stateのregisterErrorMessagesにユーザー登録のエラーを格納するmutation
    setRegisterErrorMessages(state, messages) {
        state.registerErrorMessages = messages;
    },
    // stateのeditUserProfileErrorMessagesに利用者側プロフィール編集のエラーを格納するmutation
    setEditUserProfileErrorMessages(state, messages) {
        state.editUserProfileErrorMessages = messages;
    },
    // stateのeditStoreProfileErrorMessagesにコンビニ側プロフィール編集のエラーを格納するmutation
    setEditStoreProfileErrorMessages(state, messages) {
        state.editStoreProfileErrorMessages = messages;
    },
    // stateのloginErrorMessagesにログインのエラーを格納するmutation
    setLoginErrorMessages(state, messages) {
        state.loginErrorMessages = messages;
    },
    // stateのpassReminderSendErrorMessagesにパスワードリマインダーのエラーを格納するmutation
    setPassReminderSendErrorMessages(state, messages) {
        state.passReminderSendErrorMessages = messages;
    },
    // stateのcheckTokenErrorMessagesにトークンのエラーを格納するmutation
    setCheckTokenErrorMessages(state, messages) {
        state.checkTokenErrorMessages = messages;
    },
    // stateのpassResetErrorMessagesにパスワードリセットのエラーを格納するmutation
    setPassResetErrorMessages(state, messages) {
        state.passResetErrorMessages = messages;
    },
};

const actions = {
    // ログインしているユーザーの情報を取得するaction
    async currentUser(context) {
        context.commit("setApiStatus", null);
        let response = await axios.get("/api/user");
        let user = response.data || null;

        if (user === null) {
            response = await axios.get("/api/store");
            user = response.data || null;
        }

        if (response.status === OK) {
            context.commit("setApiStatus", true);
            context.commit("setUser", user);
            return false;
        }

        context.commit("setApiStatus", false);
        context.commit("error/setCode", response.status, { root: true });
    },
    // ユーザー登録を実行するaction
    async register(context, data) {
        context.commit("setApiStatus", null);

        const response = !data.store_name
            ? await axios.post("/api/register", data)
            : await axios.post("/api/register-store", data);

        if (response.status === CREATED) {
            context.commit("setApiStatus", true);
            context.commit("setUser", response.data);
            return false;
        }

        context.commit("setApiStatus", false);
        if (response.status === UNPROCESSABLE_ENTITY) {
            context.commit("setRegisterErrorMessages", response.data.errors);
        } else {
            context.commit("error/setCode", response.status, { root: true });
        }
    },
    // 利用者側プロフィール編集を実行するaction
    async editUserProfile(context, data) {
        context.commit("setApiStatus", null);

        const response = await axios.post("/api/edit-user-profile", data);

        if (response.status === OK) {
            return;
        }

        if (response.status === CREATED) {
            context.commit("setApiStatus", true);
            context.commit("setUser", response.data);
            return false;
        }

        context.commit("setApiStatus", false);
        if (response.status === UNPROCESSABLE_ENTITY) {
            context.commit(
                "setEditUserProfileErrorMessages",
                response.data.errors
            );
        } else {
            context.commit("error/setCode", response.status, { root: true });
        }
    },
    // コンビニ側のプロフィール編集を実行するaction
    async editStoreProfile(context, data) {
        context.commit("setApiStatus", null);

        const response = await axios.post("/api/edit-store-profile", data);

        if (response.status === CREATED) {
            context.commit("setApiStatus", true);
            context.commit("setUser", response.data);
            return false;
        }

        context.commit("setApiStatus", false);
        if (response.status === UNPROCESSABLE_ENTITY) {
            context.commit(
                "setEditStoreProfileErrorMessages",
                response.data.errors
            );
        } else {
            context.commit("error/setCode", response.status, { root: true });
        }
    },
    // ログインを実行するaction
    async login(context, data) {
        context.commit("setApiStatus", null);

        const response = !data.isStore
            ? await axios.post("/api/login", data)
            : await axios.post("/api/login-store", data);

        if (response.status === OK) {
            context.commit("setApiStatus", true);
            context.commit("setUser", response.data);
            return false;
        }

        context.commit("setApiStatus", false);
        if (response.status === UNPROCESSABLE_ENTITY) {
            context.commit("setLoginErrorMessages", response.data.errors);
        } else {
            context.commit("error/setCode", response.status, { root: true });
        }
    },
    // ログアウトを実行するaction
    async logout(context) {
        context.commit("setApiStatus", null);
        const response = await axios.post("/api/logout");

        if (response.status === OK) {
            context.commit("setApiStatus", true);
            context.commit("setUser", null);
            return false;
        }

        context.commit("setApiStatus", false);
        context.commit("error/setCode", response.status, { root: true });
    },
    // パスワードリマインダーを実行するaction
    async passReminderSend(context, data) {
        context.commit("setApiStatus", null);

        const response = !data.isStore
            ? await axios.post("/api/pass-reminder-send", data)
            : await axios.post("/api/pass-reminder-send-store", data);

        if (response.status === CREATED) {
            context.commit("setApiStatus", true);
            return false;
        }

        context.commit("setApiStatus", false);
        if (response.status === UNPROCESSABLE_ENTITY) {
            context.commit(
                "setPassReminderSendErrorMessages",
                response.data.errors
            );
        } else if (response.status === UNAUTHORIZED) {
            context.commit(
                "setPassReminderSendErrorMessages",
                response.data.errors
            );
        } else {
            context.commit("error/setCode", response.status, { root: true });
        }
    },
    // トークンのチェックを実行するaction
    async checkToken(context, data) {
        context.commit("setApiStatus", null);

        const response = !data.isStore
            ? await axios.post("/api/check-token", data)
            : await axios.post("/api/check-token-store", data);

        if (response.status === OK) {
            context.commit("setApiStatus", true);
            return false;
        }

        context.commit("setApiStatus", false);
        if (response.status === UNAUTHORIZED) {
            context.commit("setCheckTokenErrorMessages", response.data.errors);
        } else {
            context.commit("error/setCode", response.status, { root: true });
        }
    },
    // パスワードリセットを実行するaction
    async passReset(context, data) {
        context.commit("setApiStatus", null);

        const response =
            data.isStore === "false"
                ? await axios.post("/api/pass-reset", data)
                : await axios.post("/api/pass-reset-store", data);

        if (response.status === OK) {
            context.commit("setApiStatus", true);
            return false;
        }

        context.commit("setApiStatus", false);
        if (response.status === UNPROCESSABLE_ENTITY) {
            context.commit("setPassResetErrorMessages", response.data.errors);
        } else if (response.status === UNAUTHORIZED) {
            context.commit("setCheckTokenErrorMessages", response.data.errors);
        } else {
            context.commit("error/setCode", response.status, { root: true });
        }
    },
};

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions,
};
