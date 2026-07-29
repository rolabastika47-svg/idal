<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 * (c) Armin Ronacher
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Breakdance\Lib\Vendor\Twig\Node\Expression\Unary;

use Breakdance\Lib\Vendor\Twig\Compiler;

class NotUnary extends AbstractUnary
{
    public function operator(Compiler $compiler): Compiler
    {
        return $compiler->raw('!');
    }
}
