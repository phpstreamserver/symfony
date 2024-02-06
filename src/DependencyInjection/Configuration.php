<?php

declare(strict_types=1);

namespace Luzrain\PhpRunnerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @psalm-suppress UndefinedMethod
 */
final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('phprunner');

        $treeBuilder
            ->getRootNode()
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('user')
                    ->info('Unix user of processes. Default: current user')
                    ->defaultNull()
                    ->end()
                ->scalarNode('group')
                    ->info('Unix group of processes. Default: current group')
                    ->defaultNull()
                    ->end()
                ->integerNode('stop_timeout')
                    ->info('Max seconds of child process work before force kill')
                    ->defaultValue(5)
                    ->end()
                ->scalarNode('pid_file')
                    ->info('File to store master process PID')
                    ->cannotBeEmpty()
                    ->defaultValue('%kernel.project_dir%/var/run/phprunner.pid')
                    ->end()
                ->arrayNode('servers')
                    ->prototype('array')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('name')
                                ->info('Server process name')
                                ->isRequired()
                                ->cannotBeEmpty()
                                ->end()
                            ->scalarNode('listen')
                                ->info('Listen address. Supported protocols: http://, https://')
                                ->defaultNull()
                                ->example('http://0.0.0.0:80')
                                ->end()
                            ->scalarNode('local_cert')
                                ->info('Path to local certificate file on filesystem')
                                ->defaultNull()
                                ->end()
                            ->scalarNode('local_pk')
                                ->info('Path to local private key file on filesystem')
                                ->defaultNull()
                                ->end()
                            ->integerNode('processes')
                                ->info('Number of webserver worker processes. Default: number of CPU cores * 2')
                                ->defaultNull()
                                ->end()
                            ->integerNode('max_body_size')
                                ->info('The maximum allowed size of the client http request body in bytes')
                                ->defaultValue(10 * 1024 * 1024)
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->arrayNode('reload_strategy')
                    ->info('Reload strategies configuration')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('on_exception')
                            ->info('Reload worker each time that an exception is thrown')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('active')
                                    ->info('Is strategy active')
                                    ->defaultTrue()
                                    ->end()
                                ->arrayNode('allowed_exceptions')
                                    ->info('List of allowed exceptions that do not trigger a reload')
                                    ->prototype('scalar')->end()
                                    ->defaultValue([])
                                    ->end()
                                ->end()
                            ->end()
                        ->arrayNode('on_each_request')
                            ->info('Reload worker after each request')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('active')
                                    ->info('Is strategy active')
                                    ->defaultFalse()
                                    ->end()
                                ->end()
                            ->end()
                        ->arrayNode('on_ttl_limit')
                            ->info('Reload worker each ttl seconds')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('active')
                                    ->info('Is strategy active')
                                    ->defaultFalse()
                                    ->end()
                                ->integerNode('ttl')
                                    ->info('TTL in seconds after which the worker is reloaded')
                                    ->isRequired()
                                    ->end()
                                ->end()
                            ->end()
                        ->arrayNode('on_requests_limit')
                            ->info('Reload worker each N requests')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('active')
                                    ->info('Is strategy active')
                                    ->defaultFalse()
                                    ->end()
                                ->integerNode('requests')
                                    ->info('Maximum number of requests after which the worker is reloaded')
                                    ->isRequired()
                                    ->end()
                                ->integerNode('dispersion')
                                    ->info('Prevent simultaneous restart of all workers (1000 requests and 20% dispersion will restart between 800 and 1000)')
                                    ->defaultValue(20)
                                    ->end()
                                ->end()
                            ->end()
                        ->arrayNode('on_memory_limit')
                            ->info('Reload worker after memory consumption would increase N')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('active')
                                    ->info('Is strategy active')
                                    ->defaultFalse()
                                    ->end()
                                ->integerNode('memory')
                                    ->info('Memory in bytes after increase that the worker is reloaded')
                                    ->isRequired()
                                    ->end()
                                ->end()
                            ->end()
                        ->arrayNode('on_file_change')
                            ->info('Reload all workers each time that files is change')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('active')
                                    ->info('Is strategy active')
                                    ->defaultFalse()
                                    ->end()
                                ->arrayNode('source_dir')
                                    ->info('Source directories for file monitoring')
                                    ->prototype('scalar')->end()
                                    ->defaultValue([
                                        '%kernel.project_dir%/src',
                                        '%kernel.project_dir%/config',
                                    ])
                                    ->end()
                                ->arrayNode('file_pattern')
                                    ->info('Monitored file patterns')
                                    ->prototype('scalar')->end()
                                    ->defaultValue([
                                        '*.php',
                                        '*.yaml',
                                    ])
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end();

        return $treeBuilder;
    }
}
