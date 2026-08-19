<?php
/**
 * Breakdance\Lib\Vendor\Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Breakdance\Lib\Vendor\Whoops\Inspector;

use Breakdance\Lib\Vendor\Whoops\Exception\Inspector;

class InspectorFactory implements InspectorFactoryInterface
{
    /**
     * @param \Throwable $exception
     * @return InspectorInterface
     */
    public function create($exception)
    {
        return new Inspector($exception, $this);
    }
}
