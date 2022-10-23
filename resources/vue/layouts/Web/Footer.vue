<template>
    <!-- Footer -->
    <footer id="page-footer" class="bg-body-extra-light">
        <div class="content py-3">
            <div class="row fs-sm">
                <div class="col-sm-6 order-sm-2 py-1 text-center text-sm-end">
                    Crafted with <i class="fa fa-heart text-danger"></i> by <span class="fw-semibold">Novacio</span>
                </div>
                <div class="col-sm-6 order-sm-1 py-1 text-center text-sm-start">
                    <span class="fw-semibold">{{ title }}</span> &copy; <span data-toggle="year-copy"></span>
                </div>
            </div>
        </div>
    </footer>
    <!-- END Footer -->
</template>
<script lang="ts">
import { Options, Vue } from 'vue-decorator'


@Options({})
export default class Footer extends Vue {

    title() {
        return process.env.MIX_APP_NAME
    }
}
</script>
<style lang="scss">

</style>