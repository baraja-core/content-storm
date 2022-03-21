<?php

declare(strict_types=1);

namespace Baraja\ContentStorm;


final class ParserException extends \LogicException
{
	public function __construct(
		string $message,
		private string $content,
		private int $contentLine,
	) {
		parent::__construct($message);
	}


	public function getContent(): string
	{
		return $this->content;
	}


	public function getContentLine(): int
	{
		return $this->contentLine;
	}
}
