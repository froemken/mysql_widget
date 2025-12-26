<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysql-widget.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\MySqlWidget\Domain\Model;

readonly class Handler
{
    public function __construct(
        private int $handlerReadRndNext,
        private int $handlerReadRnd,
        private int $handlerReadFirst,
        private int $handlerReadNext,
        private int $handlerReadKey,
        private int $handlerReadPrev,
    ) {}

    public function getHandlerReadRndNext(): int
    {
        return $this->handlerReadRndNext;
    }

    public function getHandlerReadRnd(): int
    {
        return $this->handlerReadRnd;
    }

    public function getHandlerReadFirst(): int
    {
        return $this->handlerReadFirst;
    }

    public function getHandlerReadNext(): int
    {
        return $this->handlerReadNext;
    }

    public function getHandlerReadKey(): int
    {
        return $this->handlerReadKey;
    }

    public function getHandlerReadPrev(): int
    {
        return $this->handlerReadPrev;
    }
}
