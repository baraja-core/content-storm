<?php

declare(strict_types=1);

namespace Baraja\ContentStorm;


final class ParserException extends \LogicException
{
	private string $content;

	private int $contentLine;


	public function __construct(string $message, string $content, int $contentLine)
	{
		parent::__construct($message);
		$this->content = $content;
		$this->contentLine = $contentLine;
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