<?php
/**
 * Contains the ProductState enum class.
 *
 * @copyright   Copyright (c) 2017 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2017-10-07
 *
 */


namespace Konekt\Product\Models;

use Konekt\Enum\Enum;
use Konekt\Product\Contracts\ProductState as ProductStateContract;

class ProductState extends Enum implements ProductStateContract
{
    const __default = self::DRAFT;

    const DRAFT       = 'draft';
    const INACTIVE    = 'inactive';
    const ACTIVE      = 'active';
    const UNAVAILABLE = 'unavailable';
    const RETIRED     = 'retired';

    protected $activeStates = [self::ACTIVE];

    /**
     * @inheritdoc
     */
    public function isActive()
    {
        return in_array($this->value, $this->activeStates);
    }
}