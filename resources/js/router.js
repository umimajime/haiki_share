import Vue from "vue";
import VueRouter from "vue-router";

import Top from "./pages/Top.vue";
import Register from "./pages/Register.vue";
import Login from "./pages/Login.vue";
import PassReminderSend from "./pages/PassReminderSend.vue";
import PassReset from "./pages/PassReset.vue";
import RegisterItem from "./pages/RegisterItem.vue";
import EditItem from "./pages/EditItem.vue";
import ItemList from "./pages/ItemList.vue";
import SellItemList from "./pages/SellItemList.vue";
import SoldItemList from "./pages/SoldItemList.vue";
import ItemDetail from "./pages/ItemDetail.vue";
import UserMypage from "./pages/UserMypage.vue";
import StoreMypage from "./pages/StoreMypage.vue";
import EditUserProfile from "./pages/EditUserProfile.vue";
import EditStoreProfile from "./pages/EditStoreProfile.vue";
import AuthError from "./pages/errors/AuthError.vue";
import NotFound from "./pages/errors/NotFound.vue";
import SystemError from "./pages/errors/SystemError.vue";

import store from "./store";

Vue.use(VueRouter);

const routes = [
    {
        path: "/",
        component: Top,
    },
    {
        path: "/register",
        component: Register,
        beforeEnter(to, from, next) {
            if (store.getters["auth/check"]) {
                next("/user-mypage");
            } else {
                next();
            }
        },
    },
    {
        path: "/login",
        component: Login,
        props: true,
        beforeEnter(to, from, next) {
            if (store.getters["auth/check"]) {
                next("/user-mypage");
            } else {
                next();
            }
        },
    },
    {
        path: "/pass-reminder-send",
        component: PassReminderSend,
        beforeEnter(to, from, next) {
            if (store.getters["auth/check"]) {
                next("/user-mypage");
            } else {
                next();
            }
        },
    },
    {
        path: "/pass-reset",
        component: PassReset,
        beforeEnter(to, from, next) {
            if (store.getters["auth/check"]) {
                next("/user-mypage");
            } else {
                next();
            }
        },
    },
    {
        path: "/register-item",
        component: RegisterItem,
        beforeEnter(to, from, next) {
            if (store.getters["auth/check"]) {
                if (store.getters["auth/isStore"]) {
                    next();
                } else {
                    next("/403");
                }
            } else {
                next("/login?isStore=true");
            }
        },
    },
    {
        path: "/edit-item/:id",
        component: EditItem,
        beforeEnter(to, from, next) {
            if (store.getters["auth/check"]) {
                if (store.getters["auth/isStore"]) {
                    next();
                } else {
                    next("/403");
                }
            } else {
                next("/login?isStore=true");
            }
        },
    },
    {
        path: "/item-list",
        component: ItemList,
        beforeEnter(to, from, next) {
            if (store.getters["auth/check"]) {
                next();
            } else {
                next("/login");
            }
        },
    },
    {
        path: "/sell-item-list",
        component: SellItemList,
        beforeEnter(to, from, next) {
            if (store.getters["auth/check"]) {
                if (store.getters["auth/isStore"]) {
                    next();
                } else {
                    next("/403");
                }
            } else {
                next("/login");
            }
        },
    },
    {
        path: "/sold-item-list",
        component: SoldItemList,
        beforeEnter(to, from, next) {
            if (store.getters["auth/check"]) {
                if (store.getters["auth/isStore"]) {
                    next();
                } else {
                    next("/403");
                }
            } else {
                next("/login");
            }
        },
    },
    {
        path: "/item-detail/:id",
        component: ItemDetail,
        beforeEnter(to, from, next) {
            if (store.getters["auth/check"]) {
                next();
            } else {
                next("/login");
            }
        },
    },
    {
        path: "/user-mypage",
        component: UserMypage,
        beforeEnter(to, from, next) {
            if (store.getters["auth/check"]) {
                if (store.getters["auth/isStore"]) {
                    next("/store-mypage");
                } else {
                    next();
                }
            } else {
                next("/login");
            }
        },
    },
    {
        path: "/store-mypage",
        component: StoreMypage,
        beforeEnter(to, from, next) {
            if (store.getters["auth/check"]) {
                if (store.getters["auth/isStore"]) {
                    next();
                } else {
                    next("/user-mypage");
                }
            } else {
                next("/login");
            }
        },
    },
    {
        path: "/edit-user-profile",
        component: EditUserProfile,
        beforeEnter(to, from, next) {
            if (store.getters["auth/check"]) {
                if (store.getters["auth/isStore"]) {
                    next("/user-mypage");
                } else {
                    next();
                }
            } else {
                next("/login");
            }
        },
    },
    {
        path: "/edit-store-profile",
        component: EditStoreProfile,
        beforeEnter(to, from, next) {
            if (store.getters["auth/check"]) {
                if (store.getters["auth/isStore"]) {
                    next();
                } else {
                    next("/user-mypage");
                }
            } else {
                next("/login");
            }
        },
    },
    {
        path: "/403",
        component: AuthError,
    },
    {
        path: "/500",
        component: SystemError,
    },
    {
        path: "*",
        component: NotFound,
    },
];

const router = new VueRouter({
    mode: "history",
    routes,
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition;
        } else {
            return { x: 0, y: 0 };
        }
    },
});

export default router;
