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

use Illuminate\Support\Facades\Blade;
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

	protected static $statusClass = [
		self::DRAFT					=> 'text-secondary',
		self::INACTIVE 				=> 'text-warning',
		self::ACTIVE				=> 'text-teal',
		self::UNAVAILABLE 			=> 'text-orange',
		self::RETIRED 				=> 'text-danger',
	];

	protected static $statusIcons = [
		self::DRAFT					=> 'far fa-question-circle',
		self::INACTIVE 				=> 'far fa-arrow-alt-circle-right',
		self::ACTIVE				=> 'far fa-check-circle',
		self::UNAVAILABLE 			=> 'far fa-dot-circle',
		self::RETIRED 				=> 'far fa-times-circle',
	];

	protected static $statusV2Colors = [
		self::DRAFT         => 'text-zinc-500 dark:text-zinc-400',
		self::INACTIVE      => 'text-orange-500',
		self::ACTIVE        => 'text-teal-600',
		self::UNAVAILABLE   => 'text-red-500',
		self::RETIRED       => 'text-gray-400',
	];

	protected static $statusV2Icons = [
		self::DRAFT					=> 'question-mark-circle',
		self::INACTIVE 				=> 'pause-circle',
		self::ACTIVE				=> 'check-circle',
		self::UNAVAILABLE 			=> 'exclamation-circle',
		self::RETIRED 				=> 'x-circle',
	];

	protected static $activeStates = [self::ACTIVE];
	protected static $listStates = [];
	protected static $unListStates = [self::DRAFT,self::INACTIVE,self::RETIRED];
	protected static $viewableStates = [];

	public function __construct($value = null)
	{
		parent::__construct($value);

		static::$listStates = explode(',', Cache::get('settings.products.list-states', self::ACTIVE));
		static::$viewableStates = explode(',', Cache::get('settings.products.view-states', self::ACTIVE));
	}

	protected static function boot()
	{
		static::$listStates = explode(',', Cache::get('settings.products.list-states', self::ACTIVE));
		static::$viewableStates = explode(',', Cache::get('settings.products.view-states', self::ACTIVE));

		static::$labels = [
			self::DRAFT			=> __('backoffice.product.states.draft'),
			self::INACTIVE 		=> __('backoffice.product.states.inactive'),
			self::ACTIVE		=> __('backoffice.product.states.active'),
			self::UNAVAILABLE 	=> __('backoffice.product.states.unavailable'),
			self::RETIRED 		=> __('backoffice.product.states.retired'),
		];
	}

	public static function choices(): array
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

	public static function selfChoices(): array
	{
		$result = [];
		$choices = parent::choices();
		foreach ($choices as $key => $value) {
			if (self::$visibility[$key]) {
				$result[$key] = [
					'label' => $value,
					'icon' => self::$statusV2Icons[$key],
					'color' => self::$statusV2Colors[$key],
				];
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

	public static function getUnListableStates(): array
	{
		self::boot();
		return static::$unListStates;
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

		foreach ($choices as $key => $choice) {
			if (!in_array($key, static::$listStates)) {
				$result[] = $key;
			}
		}

		return $result;
	}

	/**
	 * @inheritdoc
	 */
	public static function getViewableStates(): array
	{
		self::boot();
		return static::$viewableStates;
	}

	/**
	 * @inheritdoc
	 */
	public function isViewable(): bool
	{
		self::boot();
		return in_array($this->value, static::$viewableStates);
	}

	public function getIcon(): string
	{
		return static::$statusIcons[$this->value];
	}

	public static function getStatusIcon(string $status): string
	{
		return self::$statusIcons[$status];
	}

	public function getClass(): string
	{
		return static::$statusClass[$this->value];
	}

	public static function getStatusClass(string $status): string
	{
		return self::$statusClass[$status];
	}

	/**
	 * Get V2 status icons SVG markup.
	 *
	 * @return array Full SVG HTML elements ready for output
	 */
	public static function getV2IconMarkup(): array
	{
		return [
			'question-mark-circle' => Blade::render('<flux:icon.question-mark-circle class="size-4" />'),
			'pause-circle' => Blade::render('<flux:icon.pause-circle class="size-4" />'),
			'check-circle' => Blade::render('<flux:icon.check-circle class="size-4" />'),
			'exclamation-circle' => Blade::render('<flux:icon.exclamation-circle class="size-4" />'),
			'x-circle' => Blade::render('<flux:icon.x-circle class="size-4" />'),
		];
	}

	/**
	 * Get V2 status icons for all states.
	 *
	 * @return array
	 */
	public static function getV2Icons(): array
	{
		return self::$statusV2Icons;
	}

	/**
	 * Get V2 status icon key for the given state.
	 *
	 * @return string
	 */
	public function getV2Icon(): string
	{
		return self::_getV2Icon($this->value);
	}

	/**
	 * Get V2 status icon key for the given state.
	 *
	 * @return string
	 */
	public static function _getV2Icon(self|string $value): string
	{
		if ($value instanceof self) {
			$value = $value->value();
		}

		return self::$statusV2Icons[$value];
	}

	/**
	 * Get V2 status icon key for the given state.
	 *
	 * @return array
	 */
	public static function getV2IconKeys(): array
	{
		return self::$statusV2Icons;
	}

	/**
	 * Get V2 status color for the given state.
	 *
	 * @param string|null $status The state value (e.g., 'active', 'draft')
	 * @return string Tailwind CSS class for text color
	 */
	public function getV2Color(): string
	{
		return self::_getV2Color($this->value);
	}

	/**
	 * Get V2 status icon color for the given state.
	 *
	 * @return string
	 */
	public static function _getV2Color(self|string $value): string
	{
		if ($value instanceof self) {
			$value = $value->value();
		}

		return self::$statusV2Colors[$value];
	}

	/**
	 * Get V2 status icon key for the given state.
	 *
	 * @return array
	 */
	public static function getV2IconColors(): array
	{
		return self::$statusV2Colors;
	}
}