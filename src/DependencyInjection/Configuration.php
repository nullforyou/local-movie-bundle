<?php


namespace Jayson\Movie\LocalMovie\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('local_movie');

        $rootNode = $treeBuilder->getRootNode();

        $rootNode->children()
                ->arrayNode('storage_dir')
                    ->prototype('variable')->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}