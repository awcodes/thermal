<x-filament-widgets::widget class="fi-thermal-calendar-heatmap-widget">
    <x-filament::section :heading="$this->getHeading()" :description="$this->getDescription()">
        <x-thermal::calendar :heatmap="$heatmap" :interactive="$interactive" :label="$this->getHeading()" />
    </x-filament::section>
</x-filament-widgets::widget>
