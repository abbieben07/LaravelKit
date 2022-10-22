<template>
    <!-- Main Container -->
    <div id="page-container">
        <div v-show="loading" id="page-loader" class="show" />
        <main id="main-container">
            <!-- Page Content -->
            <div class="bg-image" style="background-image: url('https://www.unicef.org/ukraine/sites/unicef.org.ukraine/files/styles/press_release_feature/public/UN0414981%20%282%29_0.JPG?itok=I2UltAzF')">
                <div class="row g-0 bg-primary-dark-op">
                    <!-- Meta Info Section -->
                    <div class="hero-static col-lg-4 d-none d-lg-flex flex-column justify-content-center">
                        <div class="p-4 p-xl-5 flex-grow-1 d-flex align-items-center">
                            <div class="w-100">
                                <a class="link-fx fw-semibold fs-2 text-white"> Reatent </a>
                                <p class="text-white-75 me-xl-8 mt-2">Welcome. Feel free to login and start enjoying your lectures.</p>
                            </div>
                        </div>
                        <div class="p-4 p-xl-5 d-xl-flex justify-content-between align-items-center fs-sm">
                            <p class="fw-medium text-white-50 mb-0"><strong>Reatent</strong> &copy; 2022<span data-toggle="year-copy"></span></p>
                        </div>
                    </div>
                    <div class="hero-static col-lg-8 d-flex flex-column bg-body-light">
                        <div class="d-flex flex-column align-items-center flex-grow-1">
                            <div class="p-4 w-100 flex-grow-1 d-flex align-items-center">
                                <div class="w-100">
                                    <!-- Header -->
                                    <div class="text-center mb-5">
                                        <slot name="heading" />
                                    </div>
                                    <!-- END Header -->

                                    <!-- Sign In Form -->
                                    <div class="row g-0 justify-content-center">
                                        <div class="col-sm-8 col-xl-4">
                                            <slot />
                                        </div>
                                    </div>
                                    <!-- END Sign In Form -->
                                </div>
                            </div>
                        </div>
                        <!-- <div class="text-center">
							<Dropdown ref="dropdown" :items="items" class="d-inline-block my-4" direction="top" @menuItemClick="onDropdownItemClick">
								<a v-tooltip="'bottom'" title="Power Menu" class="fs-1" data-bs-toggle="dropdown" @click="onToggleDropdown">
									<i class="fas fa-power-off"></i>
								</a>
							</Dropdown>
						</div> -->
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>
<script lang="ts">
import { Options, Vue } from 'vue-decorator';

@Options({})
export default class Auth extends Vue {
	loading = true;

	mounted() {
		this.loading = false;
	}
}
</script>
