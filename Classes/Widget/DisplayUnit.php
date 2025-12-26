<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysql-widget.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\MySqlWidget\Widget;

enum DisplayUnit: string
{
    case MegaByte = 'mb';
    case GigaByte = 'gb';
    case Percentage = '%';
}
