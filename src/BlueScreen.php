<?php

declare(strict_types=1);

namespace Baraja\ContentStorm;


final class BlueScreen
{
	/** @throws \Error */
	public function __construct()
	{
		throw new \Error('Class ' . static::class . ' is static and cannot be instantiated.');
	}


	/**
	 * @return array{tab: string, panel: string}|null
	 */
	public static function render(?\Throwable $e): ?array
	{
		if ($e instanceof ParserException) {
			return [
				'tab' => 'Invalid content',
				'panel' => '<pre>' . \Tracy\BlueScreen::highlightLine(htmlspecialchars($e->getContent()), $e->getContentLine()) . '</pre>',
			];
		}

		return null;
	}
}
