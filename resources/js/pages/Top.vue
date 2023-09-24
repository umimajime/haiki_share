<template>
    <main class="p-top">
        <section class="p-top__fv">
            <div class="p-top__title">
                <p class="mb30">みんなお得で三方ヨシ！</p>
                <p>「haiki share」</p>
            </div>
        </section>
        <section class="p-top__intro">
            <p>【利用者】「値上がりで食費がかかる。。。」</p>
            <p>【コンビニ】「売れ残りで廃棄がたくさん。。。」</p>
            <p>その悩み、<span>「haiki share」</span>が解決いたします！</p>
            <p>シンプルな機能で簡単にご利用いただけます。</p>
        </section>
        <section class="p-top__register">
            <p>ユーザー登録はコチラ</p>
            <div class="p-top__btn">
                <RouterLink to="/register">
                    <button class="c-button">利用者側ユーザー登録</button>
                </RouterLink>
                <RouterLink to="/register?isStore=true">
                    <button class="c-button">コンビニ側ユーザー登録</button>
                </RouterLink>
            </div>
        </section>
    </main>
</template>
