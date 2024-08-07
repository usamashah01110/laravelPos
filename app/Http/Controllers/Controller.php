<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="BoilerPlate API",
 *     description="BoilerPlate API",
 *     @OA\Contact(
 *         name="BoilerPLate API",
 *         email="info@techesthete.net"
 *     ),
 *     @OA\License(
 *         name="Apache 2.0",
 *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *     )
 * ),
 * @OA\Server(
 *     url="/api/v1",
 * ),
* @OA\SecurityScheme(
 *      type="http",
 *      securityScheme="bearerAuth",
 *      in="header",
 *      name="Authorization",
 *      scheme="bearer",
 *      bearerFormat="JWT",
 *  ),
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
