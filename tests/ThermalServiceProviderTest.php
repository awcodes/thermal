<?php

declare(strict_types=1);

it('registers the thermal view namespace', function () {
    expect(app('view')->getFinder()->getHints())->toHaveKey('thermal');
});
