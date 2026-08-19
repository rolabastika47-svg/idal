<?php
/**
 * Breakdance\Lib\Vendor\Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Breakdance\Lib\Vendor\Whoops\Inspector;

interface InspectorFactoryInterface
{
    /**
     * @param \Throwable $exception
     * @return InspectorInterface
     */
    public function create($exception);
}
