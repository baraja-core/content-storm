<?php

declare(strict_types=1);

namespace Baraja\ContentStorm;


final class BlueScreen
{

	/** @throws \Error */
	public function __construct()
	{
		throw new \Error('Class ' . get_class($this) . ' is static and cannot be instantiated.');
	}


	/**
	 * @param \Throwable|null $e
	 * @return string[]|null
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
