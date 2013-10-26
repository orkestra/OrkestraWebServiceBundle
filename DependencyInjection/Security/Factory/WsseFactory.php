<?php

/*
 * This file is part of the OrkestraWebServiceBundle package.
 *
 * Copyright (c) Orkestra Community
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Orkestra\Bundle\WebServiceBundle\DependencyInjection\Security\Factory;

use Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\DependencyInjection\Reference,
    Symfony\Component\DependencyInjection\DefinitionDecorator,
    Symfony\Component\Config\Definition\Builder\NodeDefinition,
    Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;

class WsseFactory implements SecurityFactoryInterface
{
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        $providerId = 'security.authentication.provider.wsse.' . $id;
        $provider = $container->setDefinition($providerId, new DefinitionDecorator('orkestra.wsse.security.authentication.provider'));
        $provider->replaceArgument(0, new Reference($userProvider))
                 ->replaceArgument(1, $config['cache_dir'])
                 ->replaceArgument(2, $config['lifetime']);

        $listenerId = 'security.authentication.listener.wsse.' . $id;
        $listener = $container->setDefinition($listenerId, new DefinitionDecorator('orkestra.wsse.security.authentication.listener'));

        return array($providerId, $listenerId, $defaultEntryPoint);
    }

    public function getPosition()
    {
        return 'pre_auth';
    }

    public function getKey()
    {
        return 'wsse';
    }

    public function addConfiguration(NodeDefinition $node)
    {
        $node->children()
                 ->scalarNode('lifetime')->defaultValue(300)->end()
                 ->scalarNode('cache_dir')->defaultValue('%kernel.cache_dir%/security/nonces')->end()
             ->end();
    }
}
