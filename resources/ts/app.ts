import { Ziggy } from '@/js/ziggy'
import Form from '@/vue/components/Form.vue'
//import Gate from '@/ts/policies/Gate'
import Tooltip from '@/vue/components/Tooltip'
import { createInertiaApp } from '@inertiajs/inertia-vue3'
import { InertiaProgress } from '@inertiajs/progress'
import axios, { AxiosError } from 'axios'
import $ from 'jquery'
import { isNull } from "lodash"
import moment from "moment"
//import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { createApp, h } from 'vue'
import { ZiggyVue } from 'ziggy-js/dist/vue.es.js'

InertiaProgress.init()

axios.defaults.baseURL = process.env.MIX_APP_URL
window.$ = window.jQuery = $

export const date = (data?: string) => {
	if (isNull(data))
		return ''

	let date = moment(data)
	if (date.isBetween(moment(), moment().subtract(5, 'day'))) {
		return date.fromNow()
	} else if (date.isBetween(moment(), moment().add(5, 'day'))) {
		return date.toNow()
	} else {
		return date.format('MMM DD, YYYY hh:mm A')
	}
}

createInertiaApp({
	title: (title) => `${title} | ${process.env.MIX_APP_NAME}`,
	resolve: (name) => import(`../vue/pages/${name}`),
	//@ts-ignore
	setup({ el, App, props, plugin }) {
		const app = createApp({ render: () => h(App, props) })
		app.use(plugin)
		app.use(ZiggyVue, Ziggy)
		app.config.globalProperties.$http = axios
		//console.log(props)
		//@ts-ignore
		//app.config.globalProperties.$gate = new Gate(props.initialPage.props?.auth?.user)
		app.config.globalProperties.$school = props.initialPage.props?.school
		//@ts-ignore
		app.config.globalProperties.$user = props.initialPage.props?.auth?.user
		app.config.globalProperties.$date = date
		app.directive('tooltip', Tooltip)
		app.mount(el)
	},
})

$(() => {
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
		},
	})
})

export const error = (e: AxiosError, view?: ViewModel) => {
	console.error(e)
	if (view?.message) {
		//@ts-ignore
		view.message = e.response.data?.message
	}

	if (view?.form) {
		//@ts-ignore
		view.form?.setErrors(e.response.data?.errors)
	}

}

interface ViewModel {
	message: string
	form: InstanceType<typeof Form>
}
