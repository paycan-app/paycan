<?php

namespace App\Services\Settings\Concerns;

use Illuminate\Support\HtmlString;

trait HasStatusIndicator
{
    /**
     * Create a section heading with a status indicator
     */
    protected function sectionWithIndicator(string $title, bool $isEnabled): HtmlString|string
    {
        if (! $isEnabled) {
            return $title;
        }

        return new HtmlString(
            '<span style="display:inline-block;width:8px;height:8px;background-color:rgb(34,197,94);border-radius:50%;margin-right:8px;vertical-align:middle;"></span>'.$title
        );
    }

    /**
     * Create a section heading with a conditional status indicator
     */
    protected function sectionWithConditionalIndicator(string $title, bool $condition, bool $isActive = true): HtmlString|string
    {
        if (! $condition) {
            return $title;
        }

        $color = $isActive ? 'rgb(34,197,94)' : 'rgb(156,163,175)';

        return new HtmlString(
            '<span style="display:inline-block;width:8px;height:8px;background-color:'.$color.';border-radius:50%;margin-right:8px;vertical-align:middle;"></span>'.$title
        );
    }
}
