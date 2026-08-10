<?php

namespace App\Http\Controllers;

/**
 * Base controller. Intentionally empty — this app doesn't use
 * controller traits (AuthorizesRequests/ValidatesRequests) since
 * authorization is handled by route middleware (`role:*`, see
 * routes/web.php) rather than policy classes, and validation is done
 * inline per-request in each controller method.
 */
abstract class Controller
{
    //
}
