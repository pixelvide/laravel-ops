<?php

return [
    /*
     |---------------------------------------------------------------
     | Ops Path
     |---------------------------------------------------------------
     */
    'path'    => env('OPS_PATH', 'ops'),

    /*
     |---------------------------------------------------------------
     | Ops Master Switch
     |---------------------------------------------------------------
     |
     | This option may be used to disable all Ops function regardless
     | of their individual configuration
     */
    'enabled' => env('OPS_ENABLED', true),


    /*
     |--------------------------------------------------------------------------
     | Ops Route Middleware
     |--------------------------------------------------------------------------
     |
     | These middleware will be assigned to every Ops route, giving you
     | the chance to add your own middleware to this list or change any of
     | the existing middleware. Or, you can simply stick with this list.
     |
     */

    'middleware' => [
        // 'web',
    ],
];