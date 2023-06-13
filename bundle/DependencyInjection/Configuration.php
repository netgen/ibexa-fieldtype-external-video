<?php

declare(strict_types=1);

namespace Netgen\IbexaFieldTypeExternalVideoBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getAlias(): string
    {
        return 'netgen_ibexa_fieldtype_external_video';
    }

    public function getConfigTreeBuilder(): TreeBuilder
    {
        return new TreeBuilder('netgen_ibexa_fieldtype_external_video');
    }
}
