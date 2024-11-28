<x-filament::page>
    {{ \Filament\Support\Facades\FilamentView::renderHook('panels::page.start') }}

    <x-filament-widgets::widgets />

    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">User Growth</h2>
            <div class="h-80 mt-4">
                <div
                    x-data="{
                        chart: null,
                        init: function () {
                            const ctx = $refs.canvas.getContext('2d');
                            this.chart = new Chart(ctx, {
                                type: 'line',
                                data: @js($chartData),
                                options: @js($chartOptions)
                            });
                        }
                    }"
                >
                    <canvas x-ref="canvas" class="w-full"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{ \Filament\Support\Facades\FilamentView::renderHook('panels::page.end') }}

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @endpush
</x-filament::page>
