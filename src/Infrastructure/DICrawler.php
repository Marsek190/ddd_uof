<?php

namespace App\Infrastructure;

use DI\Definition\Helper\DefinitionHelper;
use Generator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

final class DICrawler
{
    public function __construct(
        private readonly string $dirPath = __DIR__ . './../../src',
    ) {
    }

    /**
     * @return Generator<array<string, callable|DefinitionHelper>>
     */
    public function crawl(): Generator
    {
        $dirIt = new RecursiveDirectoryIterator($this->dirPath);
        $recursiveIt = new RecursiveIteratorIterator($dirIt);
        $regexIt = new RegexIterator($recursiveIt, '/^.+\\/di\.php$/i', RegexIterator::GET_MATCH);

        /** @var string $filePath */
        foreach ($regexIt as [$filePath,]) {
            /** @var array<string, callable|DefinitionHelper> $dependencyDefinitions */
            $dependencyDefinitions = require_once realpath($filePath);

            yield $dependencyDefinitions;
        }
    }
}
