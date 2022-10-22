<template>
    <div id="page-container" class="sidebar-o sidebar-dark enable-page-overlay side-scroll page-header-fixed main-content-narrow">
        <div v-show="loading" id="page-loader" class="show" />
        <SidebarLeft />
        <Header />
        <main id="main-container">
            <div v-if="banner" class="bg-body-light">
                <div class="content content-full">
                    <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center py-2">
                        <div class="flex-grow-1">
                            <h1 id="title" class="h3 fw-bold mb-2">{{ title }} - {{ app_name }}</h1>
                        </div>
                    </div>
                </div>
            </div>
            <slot name="banner" />
            <slot name="top" />
            <div v-if="banner" class="content">
                <slot />
            </div>
            <slot v-else />
        </main>
        <Footer />
    </div>
</template>

<script lang="ts">
import Footer from '@abbieben/vue-library/layouts/App/Footer.vue'
import Header from '@abbieben/vue-library/layouts/App/Header.vue'
import SidebarLeft from '@abbieben/vue-library/layouts/App/SidebarLeft.vue'
import Theme from '@/theme/js/main/app'
import { Options, Prop, Vue } from 'vue-decorator'

@Options({
    components: {
        SidebarLeft,
        Header,
        Footer
    },
})
export default class App extends Vue {
    oneui!: Theme
    loading = true

    @Prop({ type: String, required: false })
    readonly title!: string

    @Prop({ type: Boolean, default: true })
    readonly banner!: boolean

    get app_name() {
        return process.env.MIX_APP_NAME
    }

    mounted() {
        this.oneui = new Theme()
        this.loading = false
    }
}
</script>

<style lang="scss">
#main-container>.content {
    width: 100% !important;
    padding-bottom: 1.875rem !important;
}
</style>
