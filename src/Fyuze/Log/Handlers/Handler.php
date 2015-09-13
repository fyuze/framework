<?php
/**
 * Created by PhpStorm.
 * User: matthew
 * Date: 9/12/15
 * Time: 9:18 PM
 */

namespace Fyuze\Log\Handlers;


interface Handler
{
    /**
     * @return mixed
     */
    public function write();
}
