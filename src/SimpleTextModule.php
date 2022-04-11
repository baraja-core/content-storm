<?php

declare(strict_types=1);

namespace Baraja\ContentStorm;


final class SimpleTextModule implements Module
{
	public function render(string $haystack, array $parameters = []): string
	{
		return '<div>'
			. ($parameters !== []
				? sprintf('Params: <code>%s</code><br>', htmlspecialchars(json_encode($parameters, JSON_THROW_ON_ERROR), ENT_QUOTES))
				: ''
			) . htmlspecialchars($haystack, ENT_QUOTES)
			. '</div>';
	}
}
