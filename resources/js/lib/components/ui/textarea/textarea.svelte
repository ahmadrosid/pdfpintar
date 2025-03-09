<script>
	import { cn } from "$lib/utils.js";

	let { ref = $bindable(null), value = $bindable(), class: className, ...restProps } = $props();
</script>

<textarea
	bind:this={ref}
	class={cn(
		"border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring flex w-full px-3 py-2 disabled:cursor-not-allowed disabled:opacity-50",
		className
	)}
	bind:value
	{...restProps}
></textarea>
