<?php

/**
 * This file is part of the Nette Tester.
 * Copyright (c) 2009 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace Tester;

use function count, func_get_args, func_num_args, is_array, is_string, sprintf;


/**
 * Single test case.
 */
class TestCase
{
	/** @internal */
	public const
		ListMethods = 'nette-tester-list-methods',
		MethodPattern = '#^test[A-Z0-9_]#';

	private bool $handleErrors = false;

	/** @var callable|false|null */
	private $prevErrorHandler = false;


	/**
	 * Runs all test methods in this test case or a specific test method if provided.
	 */
	public function run(): void
	{
		if (func_num_args()) {
			throw new \LogicException('Calling TestCase::run($method) is deprecated. Use TestCase::runTest($method) instead.');
		}

		$methods = array_values(preg_grep(
			self::MethodPattern,
			array_map(fn(\ReflectionMethod $rm): string => $rm->getName(), (new \ReflectionObject($this))->getMethods()),
		));

		if (isset($_SERVER['argv']) && ($tmp = preg_filter('#--method=([\w-]+)$#Ai', '$1', $_SERVER['argv']))) {
			$method = reset($tmp);
			if ($method === self::ListMethods) {
				$this->sendMethodList($methods);
				return;
			}

			try {
				$this->runTest($method);
			} catch (TestCaseSkippedException $e) {
				Environment::skip($e->getMessage());
			}
		} else {
			foreach ($methods as $method) {
				try {
					$this->runTest($method);
					Environment::print(Dumper::color('lime', '√') . " $method");
				} catch (TestCaseSkippedException $e) {
					Environment::print("s $method {$e->getMessage()}");
				} catch (\Throwable $e) {
					Environment::print(Dumper::color('red', '×') . " $method\n\n");
					throw $e;
				}
			}
		}
	}


	/**
	 * Executes a specified test method within this test case, handling data providers and errors.
	 * @param  ?array  $args  arguments provided for the test method, bypassing data provider if provided.
	 */
	public function runTest(string $method, ?array $args = null): void
	{
		if (!method_exists($this, $method)) {
			throw new TestCaseException("Method '$method' does not exist.");
		} elseif (!preg_match(self::MethodPattern, $method)) {
			throw new TestCaseException("Method '$method' is not a testing method.");
		}

		$method = new \ReflectionMethod($this, $method);
		if (!$method->isPublic()) {
			throw new TestCaseException("Method {$method->getName()} is not public. Make it public or rename it.");
		}

		$info = Helpers::parseDocComment((string) $method->getDocComment()) + ['throws' => null];

		if ($info['throws'] === '') {
			throw new TestCaseException("Missing class name in @throws annotation for {$method->getName()}().");
		} elseif (is_array($info['throws'])) {
			throw new TestCaseException("Annotation @throws for {$method->getName()}() can be specified only once.");
		} else {
			$throws = is_string($info['throws']) ? preg_split('#\s+#', $info['throws'], 2) : [];
		}

		$data = $args === null
			? $this->prepareTestData($method, (array) ($info['dataprovider'] ?? []))
			: [$args];

		if ($this->prevErrorHandler === false) {
			$this->prevErrorHandler = set_error_handler(function (int $severity): ?bool {
				if ($this->handleErrors && ($severity & error_reporting()) === $severity) {
					$this->handleErrors = false;
					$this->silentTearDown();
				}

				return $this->prevErrorHandler
					? ($this->prevErrorHandler)(...func_get_args())
					: false;
			});
		}

		foreach ($data as $k => $params) {
			try {
				$this->setUp();

				$this->handleErrors = true;
				$params = array_values($params);
				try {
					if ($info['throws']) {
						$e = Assert::error(function () use ($method, $params): void {
							[$this, $method->getName()](...$params);
						}, ...$throws);
						if ($e instanceof AssertException) {
							throw $e;
						}
					} else {
						[$this, $method->getName()](...$params);
					}
				} catch (\Throwable $e) {
					$this->handleErrors = false;
					$this->silentTearDown();
					throw $e;
				}

				$this->handleErrors = false;

				$this->tearDown();

			} catch (AssertException $e) {
				throw $e->setMessage(sprintf(
					'%s in %s(%s)%s',
					$e->origMessage,
					$method->getName(),
					substr(Dumper::toLine($params), 1, -1),
					is_string($k) ? (" (data set '" . explode('-', $k, 2)[1] . "')") : '',
				));
			}
		}
	}


	protected function getData(string $provider)
	{
		if (!str_contains($provider, '.')) {
			return $this->$provider();
		} else {
			$rc = new \ReflectionClass($this);
			[$file, $query] = DataProvider::parseAnnotation($provider, $rc->getFileName());
			return DataProvider::load($file, $query);
		}
	}


	/**
	 * Setup logic to be executed before each test method. Override in subclasses for specific behaviors.
	 * @return void
	 */
	protected function setUp()
	{
	}


	/**
	 * Teardown logic to be executed after each test method. Override in subclasses to release resources.
	 * @return void
	 */
	protected function tearDown()
	{
	}


	/**
	 * Executes the tearDown method and suppresses any errors, ensuring clean teardown in all cases.
	 */
	private function silentTearDown(): void
	{
		set_error_handler(fn() => null);
		try {
			$this->tearDown();
		} catch (\Throwable $e) {
		}

		restore_error_handler();
	}


	/**
	 * Skips the current test, optionally providing a reason for skipping.
	 */
	protected function skip(string $message = ''): void
	{
		throw new TestCaseSkippedException($message);
	}


	/**
	 * Outputs a list of all test methods in the current test case. Used for Runner.
	 */
	private function sendMethodList(array $methods): void
	{
		Environment::$checkAssertions = false;
		header('Content-Type: text/plain');
		echo "\n";
		echo 'TestCase:' . get_debug_type($this) . "\n";
		echo 'Method:' . implode("\nMethod:", $methods) . "\n";

		$dependentFiles = [];
		$reflections = [new \ReflectionObject($this)];
		while (count($reflections)) {
			$rc = array_shift($reflections);
			$dependentFiles[$rc->getFileName()] = 1;

			if ($rpc = $rc->getParentClass()) {
				$reflections[] = $rpc;
			}

			foreach ($rc->getTraits() as $rt) {
				$reflections[] = $rt;
			}
		}

		echo 'Dependency:' . implode("\nDependency:", array_keys($dependentFiles)) . "\n";
	}


	/**
	 * Prepares test data from specified data providers or default method parameters if no provider is specified.
	 */
	private function prepareTestData(\ReflectionMethod $method, array $dataprovider): array
	{
		$data = $defaultParams = [];

		foreach ($method->getParameters() as $param) {
			$defaultParams[$param->getName()] = $param->isDefaultValueAvailable()
				? $param->getDefaultValue()
				: null;
		}

		foreach ($dataprovider as $i => $provider) {
			$res = $this->getData($provider);
			if (!is_array($res) && !$res instanceof \Traversable) {
				throw new TestCaseException("Data provider $provider() doesn't return array or Traversable.");
			}

			foreach ($res as $k => $set) {
				if (!is_array($set)) {
					$type = get_debug_type($set);
					throw new TestCaseException("Data provider $provider() item '$k' must be an array, $type given.");
				}

				$data["$i-$k"] = is_string(key($set))
					? array_merge($defaultParams, $set)
					: $set;
			}
		}

		if (!$dataprovider) {
			if ($method->getNumberOfRequiredParameters()) {
				throw new TestCaseException("Method {$method->getName()}() has arguments, but @dataProvider is missing.");
			}

			$data[] = [];
		}

		return $data;
	}
}

/**
 * Errors specific to TestCase operations.
 */
class TestCaseException extends \Exception
{
}

/**
 * Exception thrown when a test case or a test method is skipped.
 */
class TestCaseSkippedException extends \Exception
{
}
