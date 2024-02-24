<?php

namespace jeroendn\PhpHelpers\Wrapper;

use \DateTime as PhpDateTime;
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
        if (!$this->isFrozen()) {
            $this->dateTime = new PhpDateTime($dateTime);
        }
        else {
            $this->dateTime = new DateTimeImmutable($dateTime);
        }
    }

    public function getDateTime(): PhpDateTime|DateTimeImmutable
    {
        return $this->dateTime;
    }

    /**
     * @throws Exception
     */
    public function freeze(): void
    {
        if (!$this->isFrozen()) {
            $this->dateTime = new DateTimeImmutable($this->dateTime);
        }
    }

    /**
     * @throws Exception
     */
    public function unfreeze(): void
    {
        if ($this->isFrozen()) {
            $this->dateTime = new PhpDateTime($this->dateTime);
        }
    }

    public function isFrozen(): bool
    {
        return ($this->dateTime instanceof DateTimeImmutable);
    }
}
