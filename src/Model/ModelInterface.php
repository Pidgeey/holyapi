<?php

namespace Src\Model;

/**
 * Interface ModelInterface
 *
 * @package Src\Model
 */
interface ModelInterface
{
    /** @return string */
    public function getModelTable(): string;

    /** @return string|array */
    public function getPrimaryKey();
}
