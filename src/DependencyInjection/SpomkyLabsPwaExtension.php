<?php

declare(strict_types=1);

namespace SpomkyLabs\PwaBundle\DependencyInjection;

use SpomkyLabs\PwaBundle\ImageProcessor\ImageProcessor;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class SpomkyLabsPwaExtension extends Extension
{
    private const ALIAS = 'pwa';

    public function getAlias(): string
    {
        return self::ALIAS;
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $processor = new Processor();
        $config = $processor->processConfiguration($this->getConfiguration($configs, $container), $configs);
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.php');

        if ($config['image_processor'] !== null) {
            $container->setAlias(ImageProcessor::class, $config['image_processor']);
        }
        unset($config['image_processor']);
        $params = [
            'icon_folder',
            'icon_prefix_url',
            'shortcut_icon_folder',
            'shortcut_icon_prefix_url',
            'screenshot_folder',
            'screenshot_prefix_url',
            'manifest_filepath',
            'serviceworker_filepath',
        ];
        $container->setParameter('spomky_labs_pwa.dest', array_intersect_key($config, array_flip($params)));
        foreach ($params as $param) {
            unset($config[$param]);
        }

        $container->setParameter('spomky_labs_pwa.config', $config);
    }

    public function getConfiguration(array $config, ContainerBuilder $container): ConfigurationInterface
    {
        return new Configuration(self::ALIAS);
    }
}
