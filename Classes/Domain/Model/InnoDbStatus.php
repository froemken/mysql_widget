<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysql-widget.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\MySqlWidget\Domain\Model;

readonly class InnoDbStatus
{
    public function __construct(
        private int $innodbBufferPoolPagesData,
        private int $innodbBufferPoolPagesFree,
        private int $innodbBufferPoolPagesMisc,
        private int $innodbBufferPoolPagesTotal,
        private int $innodbBufferPoolReadRequests,
        private int $innodbBufferPoolReads,
        private int $innodbBufferPoolWaitFree,
        private int $innodbPageSize,
    ) {}

    public function getInnodbBufferPoolPagesData(): int
    {
        return $this->innodbBufferPoolPagesData;
    }

    public function getInnodbBufferPoolPagesFree(): int
    {
        return $this->innodbBufferPoolPagesFree;
    }

    public function getInnodbBufferPoolPagesMisc(): int
    {
        return $this->innodbBufferPoolPagesMisc;
    }

    public function getInnodbBufferPoolPagesTotal(): int
    {
        return $this->innodbBufferPoolPagesTotal;
    }

    public function getInnodbBufferPoolReadRequests(): int
    {
        return $this->innodbBufferPoolReadRequests;
    }

    public function getInnodbBufferPoolReads(): int
    {
        return $this->innodbBufferPoolReads;
    }

    public function getInnodbBufferPoolWaitFree(): int
    {
        return $this->innodbBufferPoolWaitFree;
    }

    public function getInnodbPageSize(): int
    {
        return $this->innodbPageSize;
    }

    public function getInnoDbPoolDataTotalBytes(): int
    {
        return $this->getInnodbBufferPoolPagesTotal() * $this->getInnoDbPageSize();
    }

    public function getInnoDbPoolDataUsedBytes(): int
    {
        $data = $this->getInnodbBufferPoolPagesData() * $this->getInnoDbPageSize();
        $misc = $this->getInnodbBufferPoolPagesMisc() * $this->getInnoDbPageSize();

        return $data + $misc;
    }
}
