<?php

if (! function_exists('formatMemberHeight')) {

    function formatMemberHeight($height): string
    {
        if ($height === null || $height === '') {
            return '-';
        }

        $height = trim((string) $height);

        if ($height === '') {
            return '-';
        }

        $parts = explode('.', $height, 2);

        $feet = (int) $parts[0];

        $inches = isset($parts[1])
            ? (int) $parts[1]
            : 0;

        return "{$feet}ft {$inches}in";
    }
}
