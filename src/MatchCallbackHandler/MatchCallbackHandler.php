<?php

declare(strict_types=1);

namespace SpomkyLabs\PwaBundle\MatchCallbackHandler;

interface MatchCallbackHandler
{
    public function supports(string $matchCallback): bool;

    public function handle(string $matchCallback): string;
}
