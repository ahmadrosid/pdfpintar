<script>
    import { Button } from "$lib/components/ui/button";
    import * as Dialog from "$lib/components/ui/dialog";
    import { getCsrfToken } from "$lib/csrf";
    import FilePlusIcon from "lucide-svelte/icons/file-up";

    let {labels} = $props();
    let open = $state(false);
    let dragOver = $state(false);
    let file = $state(null);
    let uploading = $state(false);
    let uploadProgress = $state(0);

    function handleDragOver(e) {
        e.preventDefault();
        dragOver = true;
    }

    function handleDragLeave() {
        dragOver = false;
    }

    function handleDrop(e) {
        e.preventDefault();
        dragOver = false;
        const droppedFile = e.dataTransfer.files[0];
        if (droppedFile && droppedFile.type === 'application/pdf') {
            file = droppedFile;
        }
    }

    function handleFileSelect(e) {
        const selectedFile = e.target.files[0];
        if (selectedFile) {
            file = selectedFile;
        }
    }

    function closeModal() {
        open = false;
        file = null;
        uploadProgress = 0;
    }

    function handleUpload() {
        if (!file) return;

        uploading = true;
        uploadProgress = 0;
        const formData = new FormData();
        formData.append('document', file);

        const xhr = new XMLHttpRequest();
        xhr.open('POST', '/upload-documents', true);
        xhr.setRequestHeader('X-CSRF-TOKEN', getCsrfToken());

        xhr.upload.onprogress = (e) => {
            if (e.lengthComputable) {
                uploadProgress = Math.round((e.loaded * 100) / e.total);
            }
        };

        xhr.onload = async () => {
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.message === 'Documents uploaded successfully') {
                        closeModal();
                        window.location.href = '/documents';
                    } else {
                        console.error('Upload failed:', response.message);
                    }
                } catch (error) {
                    console.error('Failed to parse response:', error);
                }
            } else {
                console.error('Upload failed:', xhr.status);
            }
            uploading = false;
        };

        xhr.onerror = () => {
            console.error('Upload failed: Network error');
            uploading = false;
        };

        xhr.send(formData);
    }
</script>

<Button onclick={() => open = !open}>
    {labels.upload_pdf}
    <FilePlusIcon class="size-4 ml-1" />
</Button>

<Dialog.Root bind:open>
    <Dialog.Portal>
        <Dialog.Content>
            <Dialog.Header>
                <Dialog.Title>{labels.upload_document}</Dialog.Title>
                <Dialog.Description>
                    {labels.upload_document_description}
                </Dialog.Description>
            </Dialog.Header>

            <div
                role="button"
                tabindex="0"
                class="relative mt-4 grid place-items-center rounded-lg border-2 border-dashed p-8 transition-all"
                class:border-primary={dragOver}
                ondragover={handleDragOver}
                ondragleave={handleDragLeave}
                ondrop={handleDrop}
                aria-label="Drop zone for file upload"
            >
                <input
                    type="file"
                    accept=".pdf"
                    class="absolute inset-0 cursor-pointer opacity-0"
                    onchange={handleFileSelect}
                    aria-label="File input for PDF document"
                />
                <div class="flex flex-col items-center gap-2 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    <div class="text-sm text-muted-foreground">
                        <span class="font-medium">{labels.click_to_upload}</span> {labels.or_drag_and_drop}
                    </div>
                    <div class="text-xs text-muted-foreground">PDF (up to 10MB)</div>
                </div>
            </div>

            {#if file}
                <div class="mt-2">
                    <div class="flex items-center justify-between rounded-md border px-3 py-2">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <span class="text-sm">{file.name}</span>
                        </div>
                        <Button
                            variant="ghost"
                            class="h-8 w-8 p-0 text-destructive hover:text-destructive/90"
                            onclick={() => file = null}
                            aria-label={`Remove ${file.name}`}
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </Button>
                    </div>
                </div>
            {/if}

            {#if uploading}
                <div class="mt-4">
                    <div class="flex justify-between text-sm font-medium">
                        <span>Uploading...</span>
                        <span>{uploadProgress}%</span>
                    </div>
                    <div class="mt-2 h-2 w-full rounded-full bg-secondary">
                        <div
                            class="h-full rounded-full bg-primary transition-all duration-300"
                            style={`width: ${uploadProgress}%`}
                        ></div>
                    </div>
                </div>
            {/if}

            <Dialog.Footer>
                <Button variant="outline" onclick={closeModal}>
                    {labels.cancel}
                </Button>
                <Button
                    disabled={!file || uploading}
                    onclick={handleUpload}
                >
                    {#if uploading}
                        <svg class="animate-spin -ml-1 mr-1 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {labels.uploading}
                    {:else}
                        {labels.upload}
                    {/if}
                </Button>
            </Dialog.Footer>
        </Dialog.Content>
    </Dialog.Portal>
</Dialog.Root>
