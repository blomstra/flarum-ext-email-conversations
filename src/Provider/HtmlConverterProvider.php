<?php

/*
 * This file is part of blomstra/email-conversations.
 *
 * Copyright (c) 2022 Blomstra Ltd.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blomstra\EmailConversations\Provider;

use Flarum\Extension\ExtensionManager;
use Flarum\Foundation\AbstractServiceProvider;
use League\HTMLToMarkdown\Converter;
use League\HTMLToMarkdown\Environment;
use League\HTMLToMarkdown\HtmlConverter;

class HtmlConverterProvider extends AbstractServiceProvider
{
    public function register()
    {
        $this->container->singleton('blomstra.html-converter', function (): HtmlConverter {
            /** @var ExtensionManager $extensions */
            $extensions = resolve(ExtensionManager::class);

            $environment = new Environment([
                'strip_tags'    => true,
                'use_autolinks' => false,
                'remove_nodes'  => 'script xml head',
                'hard_break'    => false,
                'header_style'  => 'atx',
                'bold_style'    => '**',
                'italic_style'  => '*',
            ]);

            $environment->addConverter(new Converter\HeaderConverter());
            $environment->addConverter(new Converter\EmphasisConverter());
            $environment->addConverter(new Converter\LinkConverter());
            $environment->addConverter(new Converter\ImageConverter());
            $environment->addConverter(new Converter\PreformattedConverter());
            $environment->addConverter(new Converter\CodeConverter());
            $environment->addConverter(new Converter\ParagraphConverter());
            $environment->addConverter(new Converter\BlockquoteConverter());
            $environment->addConverter(new Converter\HorizontalRuleConverter());
            $environment->addConverter(new Converter\ListBlockConverter());
            $environment->addConverter(new Converter\ListItemConverter());
            $environment->addConverter(new Converter\DivConverter());

            if ($extensions->isEnabled(('askvortsov/flarum-markdown-tables'))) {
                $environment->addConverter(new Converter\TableConverter());
            }

            return new HtmlConverter($environment);
        });

        $this->container->alias('blomstra.html-converter', HtmlConverter::class);
    }
}
