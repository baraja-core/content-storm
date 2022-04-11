<?php

declare(strict_types=1);

namespace Baraja\ContentStorm;


final class Renderer
{
	private ?Module $fallbackModule = null;

	private ?Module $outsideBlockModule = null;

	/** @var Module[] */
	private array $modules = [];


	public function __construct(
		private bool $allowOutsideBlockContent = true,
		private string $commentPrefix = 'brj',
	) {
		if (\class_exists('\Tracy\Debugger') === true) {
			\Tracy\Debugger::getBlueScreen()->addPanel([BlueScreen::class, 'render']);
		}
		if ($commentPrefix === '' || preg_match('/^[a-z]{1,16}$/', $commentPrefix) !== 1) {
			throw new \LogicException('Comment prefix is not valid, because "' . $commentPrefix . '" given.');
		}
	}


	/**
	 * @throws ParserException
	 */
	public function render(string $haystack): string
	{
		$return = '';
		$buffer = '';
		$contextBlockName = '';
		$contextBlockParameters = [];

		foreach (explode("\n", str_replace(["\r\n", "\r"], "\n", $haystack)) as $lineIndex => $lineContent) {
			$line = $lineIndex + 1;
			if (preg_match('/<!--\s(?<end>\/)?(?<prefix>[a-z]+):(?<name>\S+)\s(?<params>\{.+?\})?\s*-->/', $lineContent, $lineParser) === 1) {
				if ($lineParser['prefix'] !== $this->commentPrefix) {
					throw new ParserException('Parse error: Block prefix "' . $lineParser['prefix'] . '" is not allowed. Did you mean "' . $this->commentPrefix . '"?', $haystack, $line);
				}
				if (preg_match('/^[a-z0-9\-]+$/', $lineParser['name']) !== 1) {
					throw new ParserException('Parse error: Block name "' . $lineParser['name'] . '" is invalid. Please use [a-z], [0-9] and "-".', $haystack, $line);
				}
				if (($lineParser['end'] ?? '') !== '') { // end current context in block and write rendered content
					if ($contextBlockName === '') {
						throw new ParserException('Parse error: Empty context block name on line ' . $line, $haystack, $line);
					}
					if ($lineParser['name'] !== $contextBlockName) {
						throw new ParserException(
							'Parse error: Ending block name "' . $lineParser['name'] . '" and context block name "' . $contextBlockName . '" does not match on line ' . $line
							. "\n" . 'Did you nest multiple blocks?',
							$haystack,
							$line,
						);
					}

					try {
						$module = $this->getModuleByName($contextBlockName);
					} catch (\InvalidArgumentException $e) {
						if ($this->fallbackModule !== null) {
							$module = $this->fallbackModule;
						} else {
							throw $e;
						}
					}

					$return .= $module->render(trim($buffer), $contextBlockParameters) . "\n";
					$buffer = '';
					$contextBlockName = '';
					$contextBlockParameters = [];
				} else { // start new context in block
					if ($buffer !== '') {
						$return .= ($this->outsideBlockModule !== null
								? $this->outsideBlockModule->render(trim($buffer), [])
								: trim($buffer)) . "\n";
					}
					$buffer = '';
					$contextBlockName = $lineParser['name'];
					$params = $lineParser['params'] ?? '';
					$contextBlockParameters = $params !== ''
						? json_decode($params, true, 512, JSON_THROW_ON_ERROR)
						: [];
				}
			} else {
				if ($this->allowOutsideBlockContent === false && $contextBlockName === '') {
					if ($lineContent === '') { // allow empty lines between blocks
						continue;
					}
					throw new ParserException(
						'Parse error: Line ' . $line . ' have not block context annotation.'
						. "\n" . 'Line content: ' . $lineContent,
						$haystack,
						$line,
					);
				}
				$buffer .= $lineContent . "\n";
			}
		}

		return $return;
	}


	public function isContentValid(string $haystack): bool
	{
		try {
			$this->render($haystack);
		} catch (\Throwable) {
			return false;
		}

		return true;
	}


	public function getModuleByName(string $name): Module
	{
		if (isset($this->modules[$name]) === false) {
			throw new \InvalidArgumentException(sprintf('Module "%s" does not exist.', $name));
		}

		return $this->modules[$name];
	}


	public function addModule(string $name, Module $module): void
	{
		if (isset($this->modules[$name])) {
			throw new \RuntimeException(sprintf('Module "%s" already exist (service "%s").', $name, get_class($this->modules[$name])));
		}
		$this->modules[$name] = $module;
	}


	public function getCommentPrefix(): string
	{
		return $this->commentPrefix;
	}


	public function setFallbackModule(Module $module): void
	{
		$this->fallbackModule = $module;
	}


	public function setOutsideBlockModule(Module $module): void
	{
		$this->outsideBlockModule = $module;
	}
}
