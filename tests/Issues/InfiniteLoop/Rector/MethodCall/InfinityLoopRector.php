<?php

declare(strict_types=1);

namespace Rector\Core\Tests\Issues\InfiniteLoop\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\MethodCall;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Rector\Core\Tests\Issues\InfiniteLoop\InfiniteLoopTest
 * @extends AbstractRector<MethodCall>
 */
final class InfinityLoopRector extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    /**
     * @return Assign|null
     */
    public function refactor(Node $node): ?Node
    {
        if (! $this->isName($node->name, 'modify')) {
            return null;
        }

        return new Assign($node->var, $node);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Road to left... to left... to lefthell..', []);
    }
}
