<?php

declare(strict_types=1);

namespace Contributte\Webpack\Latte;

use Latte\Extension;

final class WebpackExtension extends Extension
{
	public function getTags(): array
	{
		return [
			'webpack' => [WebpackNode::class, 'create'],
		];
	}
}
