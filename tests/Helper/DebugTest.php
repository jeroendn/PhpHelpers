<?php

declare(strict_types=1);

namespace Helper;

use jeroendn\PhpHelpers\Helper\Debug;
use PHPUnit\Framework\TestCase;
use Symfony\Component\VarDumper\VarDumper;

final class DebugTest extends TestCase
{
    public function testRawPrintsVariableWrappedInPreTags(): void
    {
        $this->expectOutputString('<pre>hello</pre>');

        Debug::raw('hello');
    }

    public function testRawPrintsArrays(): void
    {
        ob_start();
        Debug::raw(['a' => 1]);
        $output = (string) ob_get_clean();

        self::assertStringStartsWith('<pre>', $output);
        self::assertStringEndsWith('</pre>', $output);
        self::assertStringContainsString('[a] => 1', $output);
    }

    public function testDForwardsAllVariablesToVarDumper(): void
    {
        $captured = [];
        VarDumper::setHandler(static function (mixed $var) use (&$captured): void {
            $captured[] = $var;
        });

        try {
            Debug::d('foo', 123);
        } finally {
            VarDumper::setHandler(null);
        }

        self::assertSame([['foo', 123]], $captured);
    }
}
