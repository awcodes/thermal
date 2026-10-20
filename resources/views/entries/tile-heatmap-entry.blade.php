<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <x-thermal::tiles
        :heatmap="$getHeatmap()"
        :attributes="\Filament\Support\prepare_inherited_attributes($getExtraAttributeBag())->class(['fi-thermal-tile-heatmap-entry'])"
    />
</x-dynamic-component>
