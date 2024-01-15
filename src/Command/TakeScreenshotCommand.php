<?php

declare(strict_types=1);

namespace SpomkyLabs\PwaBundle\Command;

use Facebook\WebDriver\WebDriverDimension;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\Panther\Client;
use function count;

#[AsCommand(
    name: 'pwa:take-screenshot',
    description: 'Take a screenshot of the application store it in your asset folder'
)]
final class TakeScreenshotCommand extends Command
{
    private readonly Client $webClient;

    public function __construct(
        private readonly Filesystem $filesystem,
        #[Autowire('@pwa.web_client')]
        null|Client $webClient = null,
    ) {
        parent::__construct();
        if ($webClient === null) {
            $webClient = Client::createChromeClient();
        }
        $this->webClient = $webClient;
    }

    public function isEnabled(): bool
    {
        return class_exists(Client::class) && class_exists(WebDriverDimension::class) && class_exists(MimeTypes::class);
    }

    protected function configure(): void
    {
        $this->addArgument('url', InputArgument::REQUIRED, 'The URL to take a screenshot from');
        $this->addArgument('output', InputArgument::REQUIRED, 'The output directory of the screenshot');
        $this->addArgument(
            'filename',
            InputArgument::OPTIONAL,
            'The output name of the screenshot',
            'screenshot',
            ['homeage-android', 'feature1']
        );
        $this->addOption('width', null, InputOption::VALUE_OPTIONAL, 'The width of the screenshot');
        $this->addOption('height', null, InputOption::VALUE_OPTIONAL, 'The height of the screenshot');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('PWA - Take a screenshot');

        $url = $input->getArgument('url');
        $height = $input->getOption('height');
        $width = $input->getOption('width');

        $client = clone $this->webClient;
        $client->request('GET', $url);
        $tmpName = $this->filesystem
            ->tempnam('', 'pwa-');
        if ($width !== null && $height !== null) {
            $client->manage()
                ->window()
                ->setSize(new WebDriverDimension((int) $width, (int) $height));
        }
        $client->manage()
            ->window()
            ->fullscreen();
        $client->takeScreenshot($tmpName);

        $mime = MimeTypes::getDefault();
        $mimeType = $mime->guessMimeType($tmpName);
        $extensions = $mime->getExtensions($mimeType);
        if (count($extensions) === 0) {
            $io->error(sprintf('Unable to guess the extension for the mime type "%s".', $mimeType));
            return self::FAILURE;
        }
        $sizes = '';
        if ($width !== null && $height !== null) {
            $sizes = sprintf('-%dx%d', (int) $width, (int) $height);
        }

        $format = current($extensions);
        $filename = sprintf(
            '%s/%s%s.%s',
            $input->getArgument('output'),
            $input->getArgument('filename'),
            $sizes,
            $format
        );

        $this->filesystem->copy($tmpName, $filename, true);
        $this->filesystem->remove($tmpName);
        $io->success('Screenshot saved');

        return self::SUCCESS;
    }
}