<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Comment\HeaderCommentFixer;
use PhpCsFixer\Fixer\ControlStructure\TrailingCommaInMultilineFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitExpectationFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->sets([__DIR__.'/tools/ecs/vendor/contao/easy-coding-standard/config/contao.php']);
    $ecsConfig->parallel();

    $ecsConfig->ruleWithConfiguration(HeaderCommentFixer::class, [
        'header' => "This file is part of cgoit\\calendar-extended-bundle.\n\n(c) Kester Mielke\n\n(c) Carsten Götzinger\n\n@license LGPL-3.0-or-later"
    ]);

    if (PHP_VERSION_ID < 80000) {
        $ecsConfig->ruleWithConfiguration(TrailingCommaInMultilineFixer::class, ['elements' => ['arrays'], 'after_heredoc' => true]);
        $ecsConfig->skip([PhpUnitExpectationFixer::class]); // see https://github.com/symplify/symplify/issues/3130
    }

    // Adjust the configuration according to your needs.
};
