<?php

declare(strict_types=1);

namespace Baraja\ContentStorm;


interface Module
{
	/**
	 * @param mixed[] $parameters
	 * @return string
	 */
	public function render(string $haystack, array $parameters = []): string;
}
