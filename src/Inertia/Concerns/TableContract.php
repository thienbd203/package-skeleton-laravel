<?php

namespace InertiaKit\InertiaTableBuilder\Inertia\Concerns;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use InertiaKit\InertiaTableBuilder\Inertia\Tables\Table;

interface TableContract
{
    public static function build(): Table;

    public static function actions(): RedirectResponse|BinaryFileResponse|Response;
}
