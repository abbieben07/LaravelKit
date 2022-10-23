<template>
	<nav id="sidebar" aria-label="Main Navigation">
		<!-- Side Header -->
		<div class="content-header">
			<!-- Logo -->
			<a class="fw-semibold text-dual">
				<span class="smini-visible">
					<i class="fa fa-circle-notch text-primary"></i>
					<!-- <img class="img-icon" src="@/images/svg/icon/icon.white.svg" alt="logo" /> -->
				</span>
				<span class="smini-hide fs-5 tracking-wider pushback">
					{{ app_name }}
					<!-- <img class="img-banner" src="@/images/svg/banner/banner.white.svg" alt="banner" /> -->
				</span>
			</a>
			<!-- END Logo -->

			<!-- Extra -->
			<div>
				<!-- Dark Mode -->
				<!-- Layout API, functionality initialized in Template._uiApiLayout() -->
				<button type="button" class="btn btn-sm btn-alt-secondary" data-toggle="layout" data-action="dark_mode_toggle">
					<i class="far fa-moon"></i>
				</button>
				<!-- END Dark Mode -->

				<!-- Close Sidebar, Visible only on mobile screens -->
				<!-- Layout API, functionality initialized in Template._uiApiLayout() -->
				<a class="d-lg-none btn btn-sm btn-alt-secondary ms-1" data-toggle="layout" data-action="sidebar_close" href="javascript:void(0)">
					<i class="fa fa-fw fa-times"></i>
				</a>
				<!-- END Close Sidebar -->
			</div>
			<!-- END Extra -->
		</div>
		<!-- END Side Header -->



		<!-- Sidebar Scrolling -->
		<div class="js-sidebar-scroll">
			<!-- Side Navigation -->
			<div class="content-side">
				<MenuList :menu="menu" />
			</div>
			<!-- END Side Navigation -->
		</div>
		<!-- END Sidebar Scrolling -->
	</nav>
</template>

<script lang="ts">
import { MenuItem } from '@/components/Menu'
import MenuList from '@/components/Menu.vue'
import { Options, Vue } from 'vue-decorator'

@Options({
	components: {
		MenuList
	}
})
export default class SidebarLeft extends Vue {
	get app_name() {
		return process.env.MIX_APP_NAME
	}

	menu: MenuItem[] = [
		{
			label: 'Dashboard',
			name: 'dashboard.page',
			icon: 'fas fa-gauge',
		},
		{
			label: 'Data Page',
			name: 'data.page',
			icon: 'fas fa-up',
		},
		{
			label: 'Vaults',
			name: 'vault.page.group',
			icon: 'fas fa-shield',

		},
		{
			label: 'ZERO Loans',
			name: 'loan.zero.page.group',
			icon: 'fas fa-money',

		},
		{
			label: 'RNPL Loans',
			name: 'loan.rnpl.page.group',
			icon: 'fas fa-home',

		},
		{
			label: 'Transactions',
			name: 'transaction.page.group',
			icon: 'fas fa-cash',
		},
		{
			label: 'Users',
			name: 'user.page.group',
			icon: 'fas fa-user-alt',
		},
		{
			label: 'Admins',
			name: 'admin.page.group',
			icon: 'fas fa-user-alt',
			submenu: [
				{
					label: 'Admins',
					name: 'admin.page.group',
				},
				{
					label: 'Create Admin',
					name: 'admin.page.create',
				},
			],
		},

	]
}
</script>

<style lang="scss">
.img-banner,
.img-icon {
	width: auto;
	height: 20px;
}

@import "~simplebar/dist/simplebar.css";
</style>
