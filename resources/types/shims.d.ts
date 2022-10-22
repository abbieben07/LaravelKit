/* eslint-disable no-unused-vars */
import Gate from '../ts/policies/Gate'
import School from '@/ts/models/School'
import User from '@/ts/models/User'
import { Page } from '@inertiajs/inertia'
import { AxiosInstance } from 'axios'
import $ from 'jquery'
import { Store } from 'vuex'
import route, { Router } from 'ziggy-js'

declare module '@vue/runtime-core' {
	interface State {
		count: number
	}
	interface ComponentCustomProperties {
		$http: AxiosInstance
		$store: Store<State>
		$error: (_e: Error) => void
		$url: string
		$jQuery: typeof $
		route: typeof route
		router: Router
		$gate: Gate
		$school: School
		$user: User
		$page: Page
	}
}

declare global {
	interface Window {
		$: typeof $
		jQuery: typeof $
		grecaptcha: {
			ready: () => {}
			execute: () => {}
		}
	}
}
