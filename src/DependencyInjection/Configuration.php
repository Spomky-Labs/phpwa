<?php

declare(strict_types=1);

namespace SpomkyLabs\PwaBundle\DependencyInjection;

use SpomkyLabs\PwaBundle\ImageProcessor\GDImageProcessor;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use function assert;

final readonly class Configuration implements ConfigurationInterface
{
    public function __construct(
        private string $alias
    ) {
    }

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder($this->alias);
        $rootNode = $treeBuilder->getRootNode();
        assert($rootNode instanceof ArrayNodeDefinition);
        $rootNode->addDefaultsIfNotSet();

        $this->setupSimpleOptions($rootNode);
        $this->setupIcons($rootNode);
        $this->setupScreenshots($rootNode);
        $this->setupFileHandlers($rootNode);
        $this->setupLaunchHandler($rootNode);
        $this->setupProtocolHandlers($rootNode);
        $this->setupRelatedApplications($rootNode);
        $this->setupShortcuts($rootNode);
        $this->setupSharedTarget($rootNode);
        $this->setupWidgets($rootNode);
        $this->setupServiceWorker($rootNode);

        return $treeBuilder;
    }

    private function setupShortcuts(ArrayNodeDefinition $node): void
    {
        $node->children()
            ->arrayNode('shortcuts')
                ->treatFalseLike([])
                ->treatTrueLike([])
                ->treatNullLike([])
                ->info('The shortcuts of the application.')
                ->arrayPrototype()
                    ->children()
                        ->scalarNode('name')
                            ->isRequired()
                            ->info('The name of the shortcut.')
                            ->example('Awesome shortcut')
                        ->end()
                        ->scalarNode('short_name')
                            ->info('The short name of the shortcut.')
                            ->example('Awesome shortcut')
                        ->end()
                        ->scalarNode('description')
                            ->info('The description of the shortcut.')
                            ->example('Awesome shortcut')
                        ->end()
                        ->scalarNode('url')
                            ->isRequired()
                            ->info('The URL of the shortcut.')
                            ->example('https://example.com')
                        ->end()
                        ->append($this->getIconsNode())
                    ->end()
                ->end()
            ->end()
        ->end()
        ;
    }

    private function setupScreenshots(ArrayNodeDefinition $node): void
    {
        $node->children()
            ->append($this->getScreenshotsNode())
        ;
    }

    private function setupFileHandlers(ArrayNodeDefinition $node): void
    {
        $node->children()
            ->arrayNode('file_handlers')
                ->info(
                    'It specifies an array of objects representing the types of files an installed progressive web app (PWA) can handle.'
                )
                ->treatFalseLike([])
                ->treatTrueLike([])
                ->treatNullLike([])
                ->arrayPrototype()
                    ->children()
                        ->scalarNode('action')
                            ->isRequired()
                            ->info('The action to take.')
                            ->example('/handle-audio-file')
                        ->end()
                        ->arrayNode('accept')
                            ->requiresAtLeastOneElement()
                            ->useAttributeAsKey('name')
                            ->arrayPrototype()
                                ->scalarPrototype()->end()
                            ->end()
                            ->info('The file types that the action will be applied to.')
                            ->example('image/*')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end()
        ;
    }

    private function setupSharedTarget(ArrayNodeDefinition $node): void
    {
        $node->children()
            ->arrayNode('share_target')
                ->treatFalseLike([])
                ->treatTrueLike([])
                ->treatNullLike([])
                ->info('The share target of the application.')
                ->children()
                    ->scalarNode('action')
                        ->isRequired()
                        ->info('The action of the share target.')
                        ->example('/shared-content-receiver/')
                    ->end()
                    ->scalarNode('method')
                        ->info('The method of the share target.')
                        ->example('GET')
                    ->end()
                    ->scalarNode('enctype')
                        ->info('The enctype of the share target. Ignored if method is GET.')
                        ->example('multipart/form-data')
                    ->end()
                    ->arrayNode('params')
                        ->isRequired()
                        ->info('The parameters of the share target.')
                        ->children()
                            ->scalarNode('title')
                                ->info('The title of the share target.')
                                ->example('name')
                            ->end()
                            ->scalarNode('text')
                                ->info('The text of the share target.')
                                ->example('description')
                            ->end()
                            ->scalarNode('url')
                                ->info('The URL of the share target.')
                                ->example('link')
                            ->end()
                            ->arrayNode('files')
                                ->info('The files of the share target.')
                                ->scalarPrototype()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end()
        ;
    }

    private function setupIcons(ArrayNodeDefinition $node): void
    {
        $node->children()
            ->append($this->getIconsNode())
        ->end()
        ;
    }

    private function setupProtocolHandlers(ArrayNodeDefinition $node): void
    {
        $node->children()
            ->arrayNode('protocol_handlers')
                ->treatFalseLike([])
                ->treatTrueLike([])
                ->treatNullLike([])
                ->info('The protocol handlers of the application.')
                ->arrayPrototype()
                    ->children()
                        ->scalarNode('protocol')
                            ->isRequired()
                            ->info('The protocol of the handler.')
                            ->example('web+jngl')
                        ->end()
                        ->scalarNode('url')
                            ->isRequired()
                            ->info('The URL of the handler.')
                            ->example('/lookup?type=%s')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function setupLaunchHandler(ArrayNodeDefinition $node): void
    {
        $node->children()
            ->arrayNode('launch_handler')
                ->treatFalseLike([])
                ->treatTrueLike([])
                ->treatNullLike([])
                ->info('The launch handler of the application.')
                ->children()
                    ->arrayNode('client_mode')
                        ->info('The client mode of the application.')
                        ->example(['focus-existing', 'auto'])
                        ->scalarPrototype()->end()
                        ->beforeNormalization()
                            ->castToArray()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function setupRelatedApplications(ArrayNodeDefinition $node): void
    {
        $node->children()
            ->booleanNode('prefer_related_applications')
                ->info('The prefer related native applications of the application.')
            ->end()
            ->arrayNode('related_applications')
                ->treatFalseLike([])
                ->treatTrueLike([])
                ->treatNullLike([])
                ->info('The related applications of the application.')
                ->arrayPrototype()
                    ->children()
                        ->scalarNode('platform')
                            ->isRequired()
                            ->info('The platform of the application.')
                            ->example('play')
                        ->end()
                        ->scalarNode('url')
                            ->isRequired()
                            ->info('The URL of the application.')
                            ->example('https://play.google.com/store/apps/details?id=com.example.app1')
                        ->end()
                        ->scalarNode('id')
                            ->info('The ID of the application.')
                            ->example('com.example.app1')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end()
        ;
    }

    private function setupSimpleOptions(ArrayNodeDefinition $node): void
    {
        $node->children()
            ->scalarNode('image_processor')
                ->defaultNull()
                ->info('The image processor to use to generate the icons of different sizes.')
                ->example(GDImageProcessor::class)
            ->end()
            ->scalarNode('icon_folder')
                ->defaultValue('%kernel.project_dir%/public/pwa')
                ->info('The folder where the icons will be generated.')
            ->end()
            ->scalarNode('icon_prefix_url')
                ->defaultValue('/pwa')
                ->info('The URL prefix to use to generate the icons.')
            ->end()
            ->scalarNode('shortcut_icon_folder')
                ->defaultValue('%kernel.project_dir%/public/pwa')
                ->info('The folder where the shortcut icons will be generated.')
            ->end()
            ->scalarNode('shortcut_icon_prefix_url')
                ->defaultValue('/pwa')
                ->info('The URL prefix to use to generate the icons.')
            ->end()
            ->scalarNode('screenshot_folder')
                ->defaultValue('%kernel.project_dir%/public/pwa')
                ->info('The folder where the screenshots will be generated.')
            ->end()
            ->scalarNode('screenshot_prefix_url')
                ->defaultValue('/pwa')
                ->info('The URL prefix to use to generate the icons.')
            ->end()
            ->scalarNode('manifest_filepath')
                ->defaultValue('%kernel.project_dir%/public/site.webmanifest')
                ->info('The filename where the manifest will be generated.')
            ->end()
            ->scalarNode('background_color')
                ->info(
                    'The background color of the application. It  should match the background-color CSS property in the sites stylesheet for a smooth transition between launching the web application and loading the site\'s content.'
                )
                ->example('red')
            ->end()
            ->arrayNode('categories')
                ->info('The categories of the application.')
                ->example([['news', 'sports', 'lifestyle']])
                ->scalarPrototype()->end()
            ->end()
            ->scalarNode('description')
                ->info('The description of the application.')
                ->example('My awesome application')
            ->end()
            ->scalarNode('display')
                ->info('The display mode of the application.')
                ->example('standalone')
            ->end()
            ->arrayNode('display_override')
                ->info('A sequence of display modes that the browser will consider before using the display member.')
                ->example([['fullscreen', 'minimal-ui']])
                ->scalarPrototype()->end()
            ->end()
            ->scalarNode('id')
                ->info('A string that represents the identity of the web application.')
                ->example('?homescreen=1')
            ->end()
            ->scalarNode('orientation')
                ->info('The orientation of the application.')
                ->example('portrait-primary')
            ->end()
            ->scalarNode('dir')
                ->info('The direction of the application.')
                ->example('rtl')
            ->end()
            ->scalarNode('lang')
                ->info('The language of the application.')
                ->example('ar')
            ->end()
            ->scalarNode('name')
                ->info('The name of the application.')
                ->example('My awesome application')
            ->end()
            ->scalarNode('short_name')
                ->info('The short name of the application.')
                ->example('My awesome application')
            ->end()
            ->scalarNode('scope')
                ->info('The scope of the application.')
                ->example(['/app/', 'https://app.foo.com'])
            ->end()
            ->scalarNode('start_url')
                ->info('The start URL of the application.')
                ->example('https://example.com')
            ->end()
            ->scalarNode('theme_color')
                ->info('The theme color of the application.')
                ->example('red')
            ->end()
            ->arrayNode('edge_side_panel')
                ->info('Specifies whether or not your app supports the side panel view in Microsoft Edge.')
                ->children()
                    ->integerNode('preferred_width')
                        ->info('Specifies the preferred width of the side panel view in Microsoft Edge.')
                    ->end()
                ->end()
            ->end()
            ->scalarNode('iarc_rating_id')
                ->info(
                    'Specifies the International Age Rating Coalition (IARC) rating ID for the app. See https://www.globalratings.com/how-iarc-works.aspx for more information.'
                )
            ->end()
            ->arrayNode('scope_extensions')
                ->info(
                    'Specifies a list of origin patterns to associate with. This allows for your app to control multiple subdomains and top-level domains as a single entity.'
                )
                ->arrayPrototype()
                    ->children()
                        ->scalarNode('origin')
                            ->isRequired()
                            ->info('Specifies the origin pattern to associate with.')
                            ->example('*.foo.com')
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->scalarNode('handle_links')
                ->info('Specifies the default link handling for the web app.')
                ->example(['auto', 'preferred', 'not-preferred'])
            ->end()
        ->end()
        ;
    }

    private function getIconsNode(): ArrayNodeDefinition
    {
        $treeBuilder = new TreeBuilder('icons');
        $node = $treeBuilder->getRootNode();
        assert($node instanceof ArrayNodeDefinition);
        $node
            ->info(
                'An array of icons to be used for the widget. If missing, the icons manifest member is used instead. Icons larger than 1024x1024 are ignored.'
            )
            ->treatFalseLike([])
            ->treatTrueLike([])
            ->treatNullLike([])
            ->arrayPrototype()
                ->children()
                    ->scalarNode('src')
                        ->isRequired()
                        ->info('The path to the icon.')
                        ->example('icon/logo.svg')
                    ->end()
                    ->arrayNode('sizes')
                        ->info(
                            'The sizes of the icon. 16 means 16x16, 32 means 32x32, etc. 0 means "any" (i.e. it is a vector image).'
                        )
                        ->example([['16', '32']])
                        ->integerPrototype()->end()
                    ->end()
                    ->scalarNode('format')
                        ->info('The icon format output.')
                        ->example(['webp', 'png'])
                    ->end()
                    ->scalarNode('purpose')
                        ->info('The purpose of the icon.')
                        ->example(['any', 'maskable', 'monochrome'])
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }

    private function getScreenshotsNode(): ArrayNodeDefinition
    {
        $treeBuilder = new TreeBuilder('screenshots');
        $node = $treeBuilder->getRootNode();
        assert($node instanceof ArrayNodeDefinition);
        $node
            ->treatFalseLike([])
            ->treatTrueLike([])
            ->treatNullLike([])
            ->arrayPrototype()
                ->children()
                    ->scalarNode('src')
                        ->isRequired()
                        ->info('The path to the screenshot.')
                        ->example('screenshot/lowres.webp')
                    ->end()
                    ->scalarNode('form_factor')
                        ->info('The form factor of the screenshot. Will guess the form factor if not set.')
                        ->example(['wide', 'narrow'])
                    ->end()
                    ->scalarNode('label')
                        ->info('The label of the screenshot.')
                        ->example('Homescreen of Awesome App')
                    ->end()
                    ->scalarNode('platform')
                        ->info('The platform of the screenshot.')
                        ->example(
                            [
                                'android',
                                'windows',
                                'chromeos',
                                'ipados',
                                'ios',
                                'kaios',
                                'macos',
                                'windows',
                                'xbox',
                                'any',
                            ]
                        )
                    ->end()
                    ->scalarNode('format')
                        ->info('The format of the screenshot. Will convert the file if set.')
                        ->example(['jpg', 'png', 'webp'])
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }

    private function setupServiceWorker(ArrayNodeDefinition $node): void
    {
        $node->children()
            ->arrayNode('serviceworker')
                ->info('EXPERIMENTAL. Specifies a serviceworker that is registered.')
                ->treatFalseLike([])
                ->treatTrueLike([
                    'generate' => true,
                ])
                ->treatNullLike([])
                ->validate()
                    ->ifTrue(
                        static fn (array $v): bool => basename((string) $v['filepath']) !== basename((string) $v['src'])
                    )
                    ->thenInvalid('The filename from the "filepath" and the "src" must be the same.')
                ->end()
                ->children()
                    ->booleanNode('generate')
                        ->defaultFalse()
                        ->info('Whether the service worker should be generated.')
                    ->end()
                    ->scalarNode('filepath')
                        ->defaultValue('%kernel.project_dir%/public/sw.js')
                        ->info('The filename where the service worker will be generated.')
                    ->end()
                    ->scalarNode('src')
                        ->cannotBeEmpty()
                        ->defaultValue('/sw.js')
                        ->info('The path to the service worker.')
                        ->example('/sw.js')
                    ->end()
                    ->scalarNode('scope')
                        ->cannotBeEmpty()
                        ->defaultValue('/')
                        ->info('The scope of the service worker.')
                        ->example('/app/')
                    ->end()
                    ->booleanNode('use_cache')
                        ->defaultTrue()
                        ->info('Whether the service worker should use the cache.')
                    ->end()
                ->end()
            ->end()
        ->end();
    }

    private function setupWidgets(ArrayNodeDefinition $node): void
    {
        $node->children()
            ->arrayNode('widgets')
                ->info(
                    'EXPERIMENTAL. Specifies PWA-driven widgets. See https://learn.microsoft.com/en-us/microsoft-edge/progressive-web-apps-chromium/how-to/widgets for more information'
                )
                ->arrayPrototype()
                    ->children()
                        ->scalarNode('name')
                            ->isRequired()
                            ->info('The title of the widget, presented to users.')
                        ->end()
                        ->scalarNode('short_name')
                            ->info('An alternative short version of the name.')
                        ->end()
                        ->scalarNode('description')
                            ->isRequired()
                            ->info('The description of the widget.')
                            ->example('My awesome widget')
                        ->end()
                        ->append($this->getIconsNode())
                        ->append($this->getScreenshotsNode()->cannotBeEmpty())
                        ->scalarNode('tag')
                            ->isRequired()
                            ->info('A string used to reference the widget in the PWA service worker.')
                        ->end()
                        ->scalarNode('template')
                            ->info(
                                'The template to use to display the widget in the operating system widgets dashboard. Note: this property is currently only informational and not used. See ms_ac_template below.'
                            )
                        ->end()
                        ->scalarNode('ms_ac_template')
                            ->isRequired()
                            ->info(
                                'The URL of the custom Adaptive Cards template to use to display the widget in the operating system widgets dashboard. See Define a widget template below.'
                            )
                        ->end()
                        ->scalarNode('data')
                            ->info(
                                'The URL where the data to fill the template with can be found. If present, this URL is required to return valid JSON.'
                            )
                        ->end()
                        ->scalarNode('type')
                            ->info('The MIME type for the widget data.')
                        ->end()
                        ->booleanNode('auth')
                            ->info('A boolean indicating if the widget requires authentication.')
                        ->end()
                        ->integerNode('update')
                            ->info(
                                'The frequency, in seconds, at which the widget will be updated. Code in your service worker must perform the updating; the widget is not updated automatically. See Access widget instances at runtime.'
                            )
                        ->end()
                        ->booleanNode('multiple')
                            ->defaultTrue()
                            ->info(
                                'A boolean indicating whether to allow multiple instances of the widget. Defaults to true.'
                            )
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();
    }
}
