<?php

declare(strict_types=1);

namespace Contributte\Webpack\Latte;

use Latte\Compiler\Node;
use Latte\Compiler\Nodes\Php\ExpressionNode;
use Latte\Compiler\Nodes\Php\ModifierNode;
use Latte\Compiler\Nodes\StatementNode;
use Latte\Compiler\PrintContext;
use Latte\Compiler\Tag;

/**
 * {webpack asset.name.js}
 */
final class WebpackNode extends StatementNode
{
	public ExpressionNode $expression;

	public ModifierNode $modifier;

	public static function create(Tag $tag): self
	{
		$tag->outputMode = $tag::OutputKeepIndentation;
		$tag->expectArguments();

		$node = new self();
		$node->expression = $tag->parser->parseUnquotedStringOrExpression();
		$node->modifier = $tag->parser->parseModifier();
		$node->modifier->escape = true;
		return $node;
	}

	public function print(PrintContext $context): string
	{
		return $context->format(
			"echo %modify(\$this->global->webpackAssetLocator->locateInPublicPath(%node)) %line;\n",
			$this->modifier,
			$this->expression,
			$this->position,
		);
	}

	/**
	 * @return \Generator<Node>
	 */
	public function &getIterator(): \Generator
	{
		yield $this->expression;
		yield $this->modifier;
	}
}
