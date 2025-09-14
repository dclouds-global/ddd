<?php

declare(strict_types=1);

namespace Dclouds\Ddd\Contracts\Aggregate;

interface EventDispatcherInterface
{
    public function dispatch(AggregateRootInterface $aggregate): void;
}
