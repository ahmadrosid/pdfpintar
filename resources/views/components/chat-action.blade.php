<div class="flex gap-2 justify-end">
    <button x-data x-on:click="$clipboard($wire.messages[{{$index}}].content)" class="opacity-70 hover:opacity-100 transition-opacity duration-300 text-xs flex gap-1 border p-1 rounded border-neutral-300 dark:border-neutral-600 bg-neutral-300 dark:bg-neutral-700">
        <x-icon-copy /> {{__('Copy')}}
    </button>
    <div x-data="{ open: false }" @click.outside="open=false" class="relative">
        <button @click="open=!open" class="opacity-70 hover:opacity-100 transition-opacity duration-300 text-xs flex gap-1 border p-1 rounded border-neutral-300 dark:border-neutral-600 bg-neutral-300 dark:bg-neutral-700">
            <x-icon-pdf class="text-neutral-600" /> {{__('Download')}}
        </button>
        <ul x-show="open" class="absolute top-0 right-0 w-40 px-3 py-2 mt-8 bg-white dark:bg-neutral-600 rounded-md shadow-sm border border-neutral-200 dark:border-neutral-600" x-transition x-cloak>
            <li class="hover:opacity-50 transition-colors duration-100 py-1">
                <button wire:click="downloadAsPdf({{ $index }})" class="text-left text-sm">
                    {{__('As PDF')}}
                </button>
            </li>
            <li class="hover:opacity-50 transition-colors duration-100 py-1">
                <button wire:click="downloadAsExcel({{ $index }})" class="text-left text-sm">
                    {{__('As Excel')}}
                </button>
            </li>
            <li class="hover:opacity-50 transition-colors duration-100 py-1">
                <button wire:click="downloadAsWord({{ $index }})" class="text-left text-sm">
                    {{__('As Microsoft word')}}
                </button>
            </li>
        </ul>
    </div>
</div>
