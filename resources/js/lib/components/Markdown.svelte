<script>
    import { gfmPlugin } from "svelte-exmarkdown/gfm";
    import Markdown from "svelte-exmarkdown";
    import CodeBlock from "./CodeBlock.svelte";

    let { md } = $props();
    const plugins = [gfmPlugin(), { renderer: { pre: CodeBlock } }];

</script>

<Markdown {plugins} {md}/>
