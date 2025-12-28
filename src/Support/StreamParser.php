<?php

namespace Rahasistiyak\LaravelAiIntegration\Support;

class StreamParser
{
    public static function readLine($stream)
    {
        $buffer = '';
        while (!$stream->eof()) {
            $byte = $stream->read(1);
            if ($byte === "\n")
                break;
            $buffer .= $byte;
        }
        return $buffer;
    }
}
