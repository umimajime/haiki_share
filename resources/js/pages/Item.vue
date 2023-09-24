<template>
    <li class="c-item">
        <span>{{ item.sell_flg === 0 ? "販売中" : "購入済" }}</span>
        <span>{{ item.prefecture.name }}</span>
        <div class="image-cover">
            <img :src="'storage/items/' + item.image" alt="" />
        </div>
        <p>
            商品名：{{
                item.name.length >= 20
                    ? item.name.substr(0, 20) + "..."
                    : item.name
            }}
        </p>
        <p>価格：{{ item.price.toLocaleString() }}円</p>
        <p>賞味期限：{{ formatDate(item.expiry_date) }}</p>
        <p>
            コンビニ名：{{
                item.store_name.length >= 20
                    ? item.store_name.substr(0, 20) + "..."
                    : item.store_name
            }}
        </p>
        <RouterLink :to="'item-detail/' + item.id">詳細を見る</RouterLink>
        <RouterLink
            :to="'edit-item/' + item.id"
            v-if="item.sell_flg === 0 && editFlg === 1"
            >編集する</RouterLink
        >
    </li>
</template>

<script>
export default {
    props: ["item", "editFlg"],
    methods: {
        // 賞味期限を年月日の形式に変換するメソッド
        formatDate(val) {
            let dateArr = val.split("-");
            return (
                Number(dateArr[0]) +
                "年" +
                Number(dateArr[1]) +
                "月" +
                Number(dateArr[2]) +
                "日"
            );
        },
    },
};
</script>
