<div x-data="{
        popoverOpen: false,
        popoverArrow: true,
        popoverPosition: 'top',
        popoverHeight: 0,
        popoverOffset: 8,
        popoverHeightCalculate() {
            this.$refs.popover.classList.add('invisible'); 
            this.popoverOpen=true; 
            let that=this;
            $nextTick(function(){ 
                that.popoverHeight = that.$refs.popover.offsetHeight;
                that.popoverOpen=false; 
                that.$refs.popover.classList.remove('invisible');
                that.$refs.popoverInner.setAttribute('x-transition', '');
                that.popoverPositionCalculate();
            });
        },
        popoverPositionCalculate(){
            this.popoverPosition = 'top';
        }
    }"
    x-init="
        that = this;
        window.addEventListener('resize', function(){
            popoverPositionCalculate();
        });
        $watch('popoverOpen', function(value){
            if(value){ popoverPositionCalculate(); document.getElementById('width')?.focus();  }
        });
        Livewire.on('settingsActionCompleted', () => {
            setTimeout(() => {
                popoverOpen = false;
            }, 100);
        });
    "
    class="relative">
    
    <button x-ref="popoverButton" @click="popoverOpen=!popoverOpen" class="flex items-center justify-center w-10 h-10 bg-transparent cursor-pointer hover:text-neutral-600 dark:hover:text-neutral-400">
        <x-icon-setting class="size-5" />
    </button>

    <div x-ref="popover"
        x-show="popoverOpen"
        x-init="setTimeout(function(){ popoverHeightCalculate(); }, 100);"
        x-trap.inert="popoverOpen"
        @click.away="popoverOpen=false;"
        @keydown.escape.window="popoverOpen=false"
        :class="{ 'top-0 mt-12' : popoverPosition == 'bottom', 'bottom-0 mb-12' : popoverPosition == 'top' }"
        class="absolute w-[300px] max-w-lg -translate-x-1/2 left-1/2 z-10" x-cloak>
        <div x-ref="popoverInner" x-show="popoverOpen" class="w-full py-4 px-2 bg-white dark:bg-neutral-700 border rounded-md shadow-sm border-neutral-200/70 dark:border-neutral-400/70">
            <div x-show="popoverArrow && popoverPosition == 'bottom'" class="absolute top-0 inline-block w-5 mt-px overflow-hidden -translate-x-2 -translate-y-2.5 left-1/2"><div class="w-2.5 h-2.5 origin-bottom-left transform rotate-45 bg-white dark:bg-neutral-700 border-t border-l rounded-sm"></div></div>
            <div x-show="popoverArrow  && popoverPosition == 'top'" class="absolute bottom-0 inline-block w-5 mb-px overflow-hidden -translate-x-2 translate-y-2.5 left-1/2"><div class="w-2.5 h-2.5 origin-top-left transform -rotate-45 bg-white dark:bg-neutral-700 border-b border-l rounded-sm"></div></div>
            <div class="grid gap-4">
                <!-- <div class="space-y-2 px-2">
                    <h4 class="font-medium leading-none">Settings</h4>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400">Configure the chat interface.</p>
                </div> -->
                <div class="grid gap-2">
                    <button wire:click="newChat" class="flex justify-between items-center gap-4 hover:bg-neutral-100 dark:hover:bg-neutral-600 p-2 rounded-md">
                        <span class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                            Chat baru
                        </span>
                        <x-icon-message-square-plus wire:loading.remove wire:target="newChat" class="size-5" />
                        <div wire:loading wire:target="newChat"> 
                            <x-icon-loader class="animate-spin" />
                        </div>
                    </button>
                    <button wire:click="clearMessages" class="flex justify-between items-center gap-4 hover:bg-neutral-100 dark:hover:bg-neutral-600 p-2 rounded-md">
                        <span class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                            Hapus percakapan
                        </span>
                        <x-icon-trash wire:loading.remove wire:target="clearMessages" class="size-5" />
                        <div wire:loading wire:target="clearMessages"> 
                            <x-icon-loader class="animate-spin" />
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>