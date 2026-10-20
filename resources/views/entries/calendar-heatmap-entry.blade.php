<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <x-thermal::calendar
        :heatmap="$getHeatmap()"
        :attributes="\Filament\Support\prepare_inherited_attributes($getExtraAttributeBag())->class(['fi-thermal-calendar-heatmap-entry'])"
    />
</x-dynamic-component>
