<?php

declare(strict_types=1);

namespace Helper;

use jeroendn\PhpHelpers\Helper\Env;
use PHPUnit\Framework\TestCase;

final class EnvTest extends TestCase
{
    /** @var list<string> */
    private array $tempFiles = [];

    protected function tearDown(): void
    {
        foreach ($this->tempFiles as $tempFile) {
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
        $this->tempFiles = [];

        putenv('PHP_HELPERS_TEST_KEY');
        putenv('PHP_HELPERS_TEST_BOOL');
    }

    public function testGetVarsParsesKeyValuePairs(): void
    {
        $file = $this->createEnvFile("PHP_HELPERS_TEST_KEY=value\nPHP_HELPERS_TEST_BOOL=123\n");

        self::assertSame(
            [
                'PHP_HELPERS_TEST_KEY'  => 'value',
                'PHP_HELPERS_TEST_BOOL' => '123',
            ],
            Env::getVars($file),
        );
    }

    public function testGetVarsParsesQuotedValues(): void
    {
        $file = $this->createEnvFile('PHP_HELPERS_TEST_KEY="hello world"' . "\n");

        self::assertSame(['PHP_HELPERS_TEST_KEY' => 'hello world'], Env::getVars($file));
    }

    public function testGetVarsIgnoresComments(): void
    {
        $file = $this->createEnvFile("; this is a comment\nPHP_HELPERS_TEST_KEY=value\n");

        self::assertSame(['PHP_HELPERS_TEST_KEY' => 'value'], Env::getVars($file));
    }

    public function testGetVarsNormalizesBooleansLikeIniFiles(): void
    {
        $file = $this->createEnvFile("PHP_HELPERS_TEST_BOOL=true\n");

        self::assertSame(['PHP_HELPERS_TEST_BOOL' => '1'], Env::getVars($file));
    }

    public function testGetVarsReturnsEmptyArrayWhenFileIsMissing(): void
    {
        self::assertSame([], Env::getVars(sys_get_temp_dir() . '/phphelpers-env-does-not-exist'));
    }

    public function testLoadExposesVariablesThroughGetenv(): void
    {
        $file = $this->createEnvFile("PHP_HELPERS_TEST_KEY=loaded\n");

        Env::load($file);

        self::assertSame('loaded', getenv('PHP_HELPERS_TEST_KEY'));
    }

    private function createEnvFile(string $contents): string
    {
        $file = tempnam(sys_get_temp_dir(), 'phphelpers-env');
        if ($file === false) {
            self::fail('Unable to create a temporary .env file');
        }

        file_put_contents($file, $contents);
        $this->tempFiles[] = $file;

        return $file;
    }
}
