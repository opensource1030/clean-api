<?php

namespace WA\Helpers\Traits;

/**
 * Class DisableDebugBar.
 */
trait DisableDebugBar
{
    public function disableDebugBar()
    {
        if (class_exists('Barryvdh\Debugbar')) {
            \Barryvdh\Debugbar::disable();
        }
    }
}
