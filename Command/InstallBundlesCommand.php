<?php

namespace HugoSoltys\FitzBundle\Command;

use HugoSoltys\FitzBundle\Helper\FileHelper;
use HugoSoltys\FitzBundle\Installer\InstallerInterface;
use HugoSoltys\FitzBundle\Model\AvailableBundles;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Hugo Soltys <hugo.soltys@gmail.com>
 */
class InstallBundlesCommand extends Command
{
    protected static $defaultName = 'fitz:install';

    /** @var string */
    private $composerPath;

    /** @var array */
    private $bundles;

    /** @var string */
    private $projectDir;

    /**
     * InstallBundlesCommand constructor.
     * @param null|string $name
     * @param $composerPath
     * @param $bundles
     * @param $projectDir
     */
    public function __construct(?string $name = null, $composerPath, $bundles, $projectDir)
    {
        parent::__construct($name);
        $this->composerPath = $composerPath;
        $this->bundles = $bundles;
        $this->projectDir = $projectDir;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        ini_set('memory_limit', -1);

        $io = new SymfonyStyle($input, $output);
        $io->title('FitzBundle install command');

        $bundles = \explode(';', FileHelper::getContent(\sprintf('%s/vendor/hugosoltys/fitz-bundle/%s', $this->projectDir, AvailableBundles::QUEUE_FILE)));

        foreach ($bundles as $bundle) {
            if (empty($bundle)) {
                continue;
            }

            $installerClass = AvailableBundles::BUNDLES[$bundle]['installer_class'];
            /** @var InstallerInterface $installer */
            $installer = new $installerClass($this->composerPath, $this->bundles, $this->projectDir);
            $installer->setBundleName($bundle);

            if (!$installer->isInstalled()) {
                $io->section(\sprintf('Now installing %s', $bundle));

                $installer->install();

                $io->success(\sprintf('%s installed successfully', $bundle));
            }
        }

        $io->success('All bundles are now installed. Enjoy !');
    }
}