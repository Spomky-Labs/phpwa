<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use SpomkyLabs\PwaBundle\Tests\DummyImageProcessor;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set(DummyImageProcessor::class);
    $container->extension('framework', [
        'test' => true,
        'secret' => 'test',
        'http_method_override' => true,
        'session' => [
            'storage_factory_id' => 'session.storage.factory.mock_file',
        ],
    ]);
    $container->extension('pwa', [
        'image_processor' => DummyImageProcessor::class,
        'icon_folder' => '%kernel.cache_dir%/samples/icons',
        'icon_prefix_url' => '/icons',
        'shortcut_icon_folder' => '%kernel.cache_dir%/samples/shortcut_icons',
        'shortcut_icon_prefix_url' => '/shortcut_icons',
        'screenshot_folder' => '%kernel.cache_dir%/samples/screenshots',
        'screenshot_prefix_url' => '/screenshots',
        'manifest_filepath' => '%kernel.cache_dir%/samples/manifest/my-pwa.json',
        'background_color' => 'red',
        'categories' => ['books', 'education', 'medical'],
        'description' => 'Awesome application that will help you achieve your dreams.',
        'display' => 'standalone',
        'display_override' => ['fullscreen', 'minimal-ui'],
        'file_handlers' => [
            [
                'action' => '/handle-audio-file',
                'accept' => [
                    'audio/wav' => ['.wav'],
                    'audio/x-wav' => ['.wav'],
                    'audio/mpeg' => ['.mp3'],
                    'audio/mp4' => ['.mp4'],
                    'audio/aac' => ['.adts'],
                    'audio/ogg' => ['.ogg'],
                    'application/ogg' => ['.ogg'],
                    'audio/webm' => ['.webm'],
                    'audio/flac' => ['.flac'],
                    'audio/mid' => ['.rmi', '.mid'],
                ],
            ],
        ],
        'icons' => [
            [
                'src' => sprintf('%s/images/1920x1920.svg', __DIR__),
                'sizes' => [48, 72, 96, 128, 256],
                'format' => 'webp',
            ],
            [
                'src' => sprintf('%s/images/1920x1920.svg', __DIR__),
                'sizes' => [48, 72, 96, 128, 256],
                'format' => 'png',
                'purpose' => 'maskable',
            ],
            [
                'src' => sprintf('%s/images/1920x1920.svg', __DIR__),
                'sizes' => [0],
            ],
        ],
        'id' => '?homescreen=1',
        'launch_handler' => [
            'client_mode' => ['focus-existing', 'auto'],
        ],
        'orientation' => 'portrait-primary',
        'prefer_related_applications' => true,
        'dir' => 'rtl',
        'lang' => 'ar',
        'name' => 'تطبيق رائع',
        'short_name' => 'رائع',
        'protocol_handlers' => [
            [
                'protocol' => 'web+jngl',
                'url' => '/lookup?type=%s',
            ],
            [
                'protocol' => 'web+jnglstore',
                'url' => '/shop?for=%s',
            ],
        ],
        'related_applications' => [
            [
                'platform' => 'play',
                'url' => 'https://play.google.com/store/apps/details?id=com.example.app1',
                'id' => 'com.example.app1',
            ],
            [
                'platform' => 'itunes',
                'url' => 'https://itunes.apple.com/app/example-app1/id123456789',
            ],
            [
                'platform' => 'windows',
                'url' => 'https://apps.microsoft.com/store/detail/example-app1/id123456789',
            ],
        ],
        'scope' => '/app/',
        'start_url' => 'https://example.com',
        'theme_color' => 'red',
        'screenshots' => [
            [
                'src' => sprintf('%s/images/screenshots', __DIR__),
                'platform' => 'android',
                'format' => 'png',
            ],
        ],
        'share_target' => [
            'action' => '/shared-content-receiver/',
            'method' => 'GET',
            'params' => [
                'title' => 'name',
                'text' => 'description',
                'url' => 'link',
            ],
        ],
        'shortcuts' => [
            [
                'name' => "Today's agenda",
                'url' => '/today',
                'description' => 'List of events planned for today',
            ],
            [
                'name' => 'New event',
                'url' => '/create/event',
            ],
            [
                'name' => 'New reminder',
                'url' => '/create/reminder',
                'icons' => [
                    [
                        'src' => sprintf('%s/images/1920x1920.svg', __DIR__),
                        'sizes' => [48, 72, 96, 128, 256],
                        'format' => 'webp',
                    ],
                    [
                        'src' => sprintf('%s/images/1920x1920.svg', __DIR__),
                        'sizes' => [48, 72, 96, 128, 256],
                        'format' => 'png',
                        'purpose' => 'maskable',
                    ],
                    [
                        'src' => sprintf('%s/images/1920x1920.svg', __DIR__),
                        'sizes' => [0],
                    ],
                ],
            ],
        ],
        'edge_side_panel' => [
            'preferred_width' => 480,
        ],
        'iarc_rating_id' => '123456',
        'scope_extensions' => [
            [
                'origin' => '*.foo.com',
            ],
            [
                'origin' => 'https://*.bar.com',
            ],
            [
                'origin' => 'https://*.baz.com',
            ],
        ],
        'handle_links' => 'auto',
        'serviceworker' => [
            'generate' => true,
            'src' => '/my-sw.js',
            'filepath' => '%kernel.cache_dir%/samples/sw/my-sw.js',
            'scope' => '/',
            'use_cache' => true,
        ],
    ]);
};
