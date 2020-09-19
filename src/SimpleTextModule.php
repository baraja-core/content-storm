<?php

declare(strict_types=1);

namespace Baraja\ContentStorm;


final class SimpleTextModule implements Module
{
	public function render(string $haystack, array $parameters = []): string
	{
		return '<div>'
			. ($parameters ? 'Params: <code>' . htmlspecialchars(json_encode($parameters), ENT_QUOTES) . '</code><br>' : '')
			. htmlspecialchars($haystack, ENT_QUOTES)
			. '</div>';
	}
}
