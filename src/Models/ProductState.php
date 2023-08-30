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
	const __DEFAULT = self::DRAFT;

	const DRAFT 		= 'draft';
	const INACTIVE 		= 'inactive';
	const ACTIVE 		= 'active';
	const UNAVAILABLE 	= 'unavailable';
	const RETIRED 		= 'retired';

	// $labels static property needs to be defined
	public static $labels = [];

	protected static $visibility = [
		self::DRAFT			        => true,
		self::INACTIVE 				=> true,
		self::ACTIVE				=> true,
		self::UNAVAILABLE 			=> true,
		self::RETIRED 			    => true,
	];

	protected static $activeStates = [self::ACTIVE];
	protected static $listStates = [];

	public function __construct($value = null)
	{
		parent::__construct($value);

		static::$listStates = explode(',', Cache::get('settings.products.list-states', self::ACTIVE));
	}

	protected static function boot()
	{
		static::$listStates = explode(',', Cache::get('settings.products.list-states', self::ACTIVE));

		static::$labels = [
			self::DRAFT			=> __('backoffice.product.states.draft'),
			self::INACTIVE 		=> __('backoffice.product.states.inactive'),
			self::ACTIVE		=> __('backoffice.product.states.active'),
			self::UNAVAILABLE 	=> __('backoffice.product.states.unavailable'),
			self::RETIRED 		=> __('backoffice.product.states.retired'),
		];
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
		self::boot();
		return in_array($this->value, static::$listStates);
	}

	/**
	 * @inheritdoc
	 */
	public static function getListableStates(): array
	{
		self::boot();
		return static::$listStates;
	}

	/**
	 * @inheritdoc
	 */
	public static function getInverseListableStates(): array
	{
		self::boot();

		$result = [];
		$choices = self::choices();

		foreach ($choices as $choice) {
			if (!in_array($choice, static::$listStates)) {
				$result[] = $choice;
			}
		}

		return $result;
	}
}