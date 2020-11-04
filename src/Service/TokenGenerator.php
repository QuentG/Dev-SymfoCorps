<?php

namespace App\Service;

class TokenGenerator
{
    /**
     * Generate token depends on size
     */
    public function generate(int $length = 10): string
    {
        return sha1(random_bytes($length));
    }
}