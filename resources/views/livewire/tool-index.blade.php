<div>
    <div class="max-w-6xl w-full mx-auto p-6 lg:p-8 my-16">
        <div class="mx-auto max-w-6xl space-y-4 text-center mb-12">
            <h1 class="text-4xl font-bold !leading-tight sm:text-5xl lg:text-6xl text-balance">
                {{__('Your all-in-one PDF toolkit')}}
            </h1>
            <p class="text-lg text-neutral-800 dark:text-white lg:text-xl text-balance">
                {{__('Transform your PDF experience with our cost-free toolkit. From merging to compression, access a full spectrum of easy-to-use features.')}}
            </p>
        </div>
        <div class="grid grid-cols-1 gap-4 sm:mt-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 my-12">
            @foreach ($services as $index => $card)
            <x-tools.card :href="$card['href']" :title="$card['title']" :description="$card['description']" :imageUrl="$card['imageUrl']"></x-tools.card>
            @endforeach
        </div>
    </div>
</div>