<?php

declare(strict_types=1);
/**
 * Contains the ProductState enum class.
 *
 * @copyright   Copyright (c) 2017 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2017-10-07
 *
 */

namespace Vanilo\Product\Models;

use Illuminate\Support\Facades\Cache;
use Konekt\Enum\Enum;
use Vanilo\Product\Contracts\ProductState as ProductStateContract;

class ProductState extends Enum implements ProductStateContract
{
	public const __DEFAULT = self::DRAFT;

	public const DRAFT = 'draft';
	public const INACTIVE = 'inactive';
	public const ACTIVE = 'active';
	public const UNAVAILABLE = 'unavailable';
	public const RETIRED = 'retired';

	protected static $activeStates = [self::ACTIVE];
	protected static $listStates = [];

	protected static $visibility = [
		self::DRAFT			        => true,
		self::INACTIVE 				=> true,
		self::ACTIVE				=> true,
		self::UNAVAILABLE 			=> true,
		self::RETIRED 			    => true,
	];

	public function __construct($value = null)
	{
		parent::__construct($value);

		static::$listStates = explode(',', Cache::get('settings.products.list-states', self::ACTIVE));
	}

	public static function choices()
	{
		$result = [];
		$choices = parent::choices();
		foreach ($choices as $key => $value) {
			if (self::$visibility[$key]) {
				$result[$key] = $value;
			}
		}
		return $result;
	}

	/**
	 * @inheritdoc
	 */
	public function isActive(): bool
	{
		return in_array($this->value, static::$activeStates);
	}

	/**
	 * @inheritdoc
	 */
	public static function getActiveStates(): array
	{
		return static::$activeStates;
	}

	/**
	 * @inheritdoc
	 */
	public function isListable(): bool
	{
		return in_array($this->value, static::$listStates);
	}

	/**
	 * @inheritdoc
	 */
	public static function getListableStates(): array
	{
		return static::$listStates;
	}
}
