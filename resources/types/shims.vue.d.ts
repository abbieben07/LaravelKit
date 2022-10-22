declare module '*.vue' {
	import type { DefineComponent } from 'vue'
	const Vue: DefineComponent<{}, {}, any>
	export default Vue
}
