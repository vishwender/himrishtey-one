<?php

if (! function_exists('formatMemberHeight')) {

    function formatMemberHeight($height): string
    {
        return \App\Support\HeightFormatter::format($height);
    }
}
