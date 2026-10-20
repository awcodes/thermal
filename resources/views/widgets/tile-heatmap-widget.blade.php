<x-filament-widgets::widget class="fi-thermal-tile-heatmap-widget">
    <x-filament::section :heading="$this->getHeading()" :description="$this->getDescription()">
        <x-thermal::tiles :heatmap="$heatmap" :interactive="$interactive" />
    </x-filament::section>
</x-filament-widgets::widget>
