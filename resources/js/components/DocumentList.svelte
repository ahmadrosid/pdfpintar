<script>
    import {Input} from "$lib/components/ui/input/index.js";
    import Trash2Icon from "lucide-svelte/icons/trash-2";
    import FileIcon from "lucide-svelte/icons/file";
    import * as AlertDialog from "$lib/components/ui/alert-dialog/index.js";
    import DocumentUpload from "./DocumentUpload.svelte";

    let {wire, dataset} = $props();
    let search = $state('');
    let documents = $state(dataset.documents);
    let documentToDelete = $state(null);
    let isDeleteDialogOpen = $state(false);

    async function deleteDocument() {
        await wire.deleteDocument(documentToDelete.id);
        documentToDelete = null;
    }
</script>

<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700/50 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="flex justify-between items-center border-b border-neutral-200 dark:border-neutral-700/50 p-4">
            <div class="w-[300px]">
                <Input placeholder="{dataset.labels.search_document}" />
            </div>
            <DocumentUpload />
        </div>
        <div class="text-neutral-900 dark:text-neutral-300">
            {#if documents.length === 0}
            <p class="text-center text-neutral-500 dark:text-neutral-400 p-6">{dataset.labels.no_documents}</p>
            {/if}
            {#each documents as document}
                <div class="flex flex-1 flex-col sm:flex-row sm:items-center gap-3 px-3 py-2 border-b dark:border-white/10 hover:bg-black/5 group">
                    <div class="flex-grow min-w-0 flex gap-1 relative isolate">
                        <div class="flex items-center gap-2 p-2">
                            <FileIcon class="flex-shrink-0 w-5 h-5 opacity-50" />
                            <span class="font-medium text-neutral-900 dark:text-neutral-100 truncate">
                                <a href="#" class="group-hover:underline">
                                    <span class="absolute inset-0"></span>
                                    { document.file_name }
                                </a>
                            </span>
                            <span class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                                {document.created_at}
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 mt-2 sm:mt-0">
                        <button onclick={() => {
                                isDeleteDialogOpen = true;
                                documentToDelete = document
                            }} class="text-neutral-400 hover:text-rose-500 transition-colors duration-300">
                            <Trash2Icon class="size-5" />
                        </button>
                    </div>
                </div>
            {/each}
            <p class="text-neutral-400 px-6 pb-4 dark:text-neutral-500 mt-4 text-sm">{dataset.labels.click_to_chat}</p>
        </div>
    </div>
</div>

<AlertDialog.Root bind:open={isDeleteDialogOpen}>
    <AlertDialog.Content>
        <AlertDialog.Header>
            <AlertDialog.Title>Do you really want to delete this document?</AlertDialog.Title>
            <AlertDialog.Description>
                By clicking this button, you will permanently delete the document <strong>{documentToDelete.file_name}</strong>.
            </AlertDialog.Description>
        </AlertDialog.Header>
        <AlertDialog.Footer>
            <AlertDialog.Cancel>Cancel</AlertDialog.Cancel>
            <AlertDialog.Action onclick={deleteDocument}>Delete</AlertDialog.Action>
        </AlertDialog.Footer>
    </AlertDialog.Content>
</AlertDialog.Root>
