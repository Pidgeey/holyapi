<?php

namespace Src\Helpers;

/**
 * Class Arr
 *
 * @package Src\Helpers
 * @noinspection PhpUnused
 */
class Arr
{
    /**
     * Get matches into two array
     *
     * @noinspection PhpUnused
     *
     * @param array $defaultArray
     * @param array $matchedArray
     *
     * @return array
     */
    public static function getMatches(array $defaultArray, array $matchedArray): array
    {
        $matches = [];

        foreach ($matchedArray as $key => $attribute) {
            if (in_array($key, $defaultArray)) {
                $matches[$key] = $attribute;
            }
        }

        return $matches;
    }
}