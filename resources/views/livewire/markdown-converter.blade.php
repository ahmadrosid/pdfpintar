<div>
    <h1 class="text-3xl font-bold mb-6 text-center text-neutral-900 dark:text-white">
        {{__('Markdown to PDF Converter')}}
    </h1>

    <form class="space-y-6 max-w-4xl mx-auto">
        @csrf
        <div>
            <label for="markdown" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                {{__('Markdown Content')}}
            </label>
            <textarea wire:model="text" name="markdown" rows="10" class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-neutral-500 focus:ring-neutral-500 dark:bg-neutral-700 dark:border-neutral-600 dark:text-white" placeholder="{{__('Enter your Markdown text here...')}}"></textarea>
        </div>

        <div class="flex justify-end space-x-4">
            <button wire:click.prevent="downloadAsPdf"  type="submit" name="action" value="preview" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-neutral-600 hover:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-neutral-500">
                {{__('Convert to PDF')}}
            </button>
        </div>
    </form>
</div>