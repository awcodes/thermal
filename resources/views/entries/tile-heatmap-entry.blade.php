<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <x-thermal::tiles
        :heatmap="$getHeatmap()"
        :label="strip_tags((string) $getLabel()) ?: null"
        :attributes="\Filament\Support\prepare_inherited_attributes($getExtraAttributeBag())->class(['fi-thermal-tile-heatmap-entry'])"
    />
</x-dynamic-component>
