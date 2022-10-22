<template>
	<div class="wrapper d-flex vh-100">
		<div class="wrapper-content w-50 m-auto">
			<slot />
		</div>
	</div>
</template>

<script lang="ts">
import { Vue, Prop } from 'vue-decorator'

export default class Blank extends Vue {
	@Prop(String)
	readonly title!: string
}
</script>

<style lang="scss">
.wrapper {
	background-color: rgba(0, 0, 0, 0.7);
}
body {
	background-image: url('@/images/jpg/blank/background.jpg');
	background-origin: content-box;
	background-position: center;
	background-size: cover;
	background-repeat: no-repeat;
}
</style>