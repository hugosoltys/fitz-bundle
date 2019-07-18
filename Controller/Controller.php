<?php

namespace HugoSoltys\FitzBundle\Controller;

use HugoSoltys\FitzBundle\Installer\InstallerInterface;
use HugoSoltys\FitzBundle\Model\AvailableBundles;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class Controller extends AbstractController
{
    /** @var array */
    private $bundles;

    public function __construct(array $bundles)
    {
        $this->bundles = $bundles;
    }

    public function install(Request $request)
    {
        if ($request->isMethod(Request::METHOD_POST)) {
            $bundles = $request->request->all();
            foreach ($bundles as $bundle) {
                $installer = $this->get(AvailableBundles::BUNDLES[$bundle]['service']);
                if (!$installer instanceof InstallerInterface) {
                    throw new \Exception(\sprintf("Installer for bundle %s was not found.", $bundle));
                }
                $installer->setBundleName($bundle);
                if (!$installer->isQueued()) {
                    $installer->queue();
                }
            }

            return $this->redirectToRoute('hugo_soltys_fitz_install');
        }

        return $this->render('index.html.twig', [
            'installedBundles' => \array_keys($this->bundles),
            'availableBundles' => AvailableBundles::BUNDLES,
            'installing' => AvailableBundles::installingBundles(),
        ]);
    }
}