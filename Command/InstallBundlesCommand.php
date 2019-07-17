<?php

namespace HugoSoltys\FitzBundle\Command;

use HugoSoltys\FitzBundle\Installer\InstallerInterface;
use HugoSoltys\FitzBundle\Model\AvailableBundles;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class InstallBundlesCommand extends Command
{
    protected static $defaultName = 'fitz:install';

    /** @var string */
    private $composerPath;

    /** @var array */
    private $bundles;

    /** @var string */
    private $projectDir;

    public function __construct(?string $name = null, $composerPath, $bundles, $projectDir)
    {
        parent::__construct($name);
        $this->composerPath = $composerPath;
        $this->bundles = $bundles;
        $this->projectDir = $projectDir;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('FitzBundle install command');

        $bundles = \explode(';', \file_get_contents(AvailableBundles::QUEUE_FILE));

        foreach ($bundles as $bundle) {
            if (empty($bundle)) {
                continue;
            }

            $installerClass = AvailableBundles::BUNDLES[$bundle]['installer_class'];
            /** @var InstallerInterface $installer */
            $installer = new $installerClass($this->composerPath, $this->bundles, $this->projectDir);

            if (!$installer->isInstalled()) {
                $io->section(\sprintf('Now installing %s', $bundle));

                $installer->install();

                $io->success(\sprintf('%s installed successfully', $bundle));
            }
        }

        $io->success('All bundles are now installed. Enjoy !');
    }
}