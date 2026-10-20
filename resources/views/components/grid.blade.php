@props([
    'interactive' => false,
])

{{--
    Interactive grids are a single tab stop: the arrow keys move focus to the nearest cell in
    the same row or column (cells carry their position as data-row and data-column), and
    Home and End jump to the first and last cell.
--}}
<div
    @if ($interactive)
        x-data="{
            move(event) {
                const current = event.target.closest('[data-row]')

                if (! current) {
                    return
                }

                const cells = [...this.$root.querySelectorAll('[data-row]')]
                const at = (cell) => [Number(cell.dataset.row), Number(cell.dataset.column)]
                const [row, column] = at(current)
                const forward = getComputedStyle(this.$root).direction === 'rtl' ? -1 : 1

                const nearest = (matches, distance) => cells
                    .filter((cell) => matches(...at(cell)))
                    .sort((a, b) => distance(...at(a)) - distance(...at(b)))[0]

                const target = {
                    ArrowRight: () => nearest((r, c) => r === row && (c - column) * forward > 0, (r, c) => Math.abs(c - column)),
                    ArrowLeft: () => nearest((r, c) => r === row && (column - c) * forward > 0, (r, c) => Math.abs(c - column)),
                    ArrowDown: () => nearest((r, c) => c === column && r > row, (r) => r - row),
                    ArrowUp: () => nearest((r, c) => c === column && r < row, (r) => row - r),
                    Home: () => cells[0],
                    End: () => cells[cells.length - 1],
                }[event.key]?.()

                if (! target) {
                    return
                }

                event.preventDefault()
                current.tabIndex = -1
                target.tabIndex = 0
                target.focus()
            },
        }"
        x-on:keydown="move($event)"
    @endif
    {{ $attributes }}
>
    {{ $slot }}
</div>
