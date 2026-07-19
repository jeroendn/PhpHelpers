<?php

declare(strict_types=1);

namespace jeroendn\PhpHelpers\Wrapper;

use DateTime as PhpDateTime;
use DateTimeImmutable;
use Exception;

class DateTime
{
    private PhpDateTime|DateTimeImmutable $dateTime;

    /**
     * @throws Exception
     */
    public function __construct(PhpDateTime|DateTimeImmutable|null $dateTime = null)
    {
        if ($dateTime === null) {
            $this->dateTime = new PhpDateTime();
        } elseif ($dateTime instanceof DateTimeImmutable) {
            $this->dateTime = DateTimeImmutable::createFromInterface($dateTime);
        } else {
            $this->dateTime = PhpDateTime::createFromInterface($dateTime);
        }
    }

    public function getDateTime(): PhpDateTime|DateTimeImmutable
    {
        return $this->dateTime;
    }

    public function freeze(): void
    {
        if (!$this->isFrozen()) {
            $this->dateTime = DateTimeImmutable::createFromInterface($this->dateTime);
        }
    }

    public function unfreeze(): void
    {
        if ($this->isFrozen()) {
            $this->dateTime = PhpDateTime::createFromInterface($this->dateTime);
        }
    }

    public function isFrozen(): bool
    {
        return ($this->dateTime instanceof DateTimeImmutable);
    }
}
