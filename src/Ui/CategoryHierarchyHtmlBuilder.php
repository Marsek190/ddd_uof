<?php declare(strict_types=1);

namespace App\Ui;

use App\Domain\Category\CategoryHierarchy;
use App\Domain\Category\CategoryHierarchyBuilder;

final class CategoryHierarchyHtmlBuilder
{
    /**
     * @var string
     */
    private const HTML_TEMPLATE = <<<HTML
<div>
    <ul>
        %s
    </ul>
</div>
HTML;

    public function __construct(private readonly CategoryHierarchyBuilder $builder)
    {
    }

    public function build(CategoryHierarchy $hierarchy): string
    {
        $html = '';

        if ($hierarchy->getNested() === null) {
            return $html;
        }

        foreach ($hierarchy->getNested() as $nested) {
            var_dump($nested);
        }

        $html .= <<<HTML
<ul data-id="{$hierarchy->getParent()->getId()}"> {$hierarchy->getParent()->getTitle()} </ul>
<li>

</li>
HTML;

        return $html;
    }

    private function traverse(CategoryHierarchy $hierarchy): string
    {

    }
}
