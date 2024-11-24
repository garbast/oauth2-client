<?php

declare(strict_types=1);

/*
 * This file is part of the OAuth2 Client extension for TYPO3
 * - (c) 2021 waldhacker UG (haftungsbeschränkt)
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace Waldhacker\Oauth2Client\Backend\LoginProvider;

use TYPO3\CMS\Backend\Controller\LoginController;
use TYPO3\CMS\Backend\LoginProvider\LoginProviderInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Fluid\View\StandaloneView;
use Waldhacker\Oauth2Client\Service\Oauth2ProviderManager;

readonly class Oauth2LoginProvider implements LoginProviderInterface
{
    public const PROVIDER_ID = '1616569531';

    public function __construct(
        private Oauth2ProviderManager $oauth2ProviderManager,
        private ExtensionConfiguration $extensionConfiguration
    ) {
    }

    public function render(StandaloneView $view, PageRenderer $pageRenderer, LoginController $loginController): void
    {
        $extensionConfiguration = $this->extensionConfiguration->get('oauth2_client');

        $view->setLayoutRootPaths(array_merge(
            $view->getLayoutRootPaths(),
            ['EXT:oauth2_client/Resources/Private/Layouts/Backend/'],
            $extensionConfiguration['view']['layoutRootPaths'] ?? []
        ));

        $view->setTemplateRootPaths(array_merge(
            $view->getTemplateRootPaths(),
            ['EXT:oauth2_client/Resources/Private/Templates/Backend/'],
            $extensionConfiguration['view']['templateRootPaths'] ?? []
        ));

        $view->setPartialRootPaths(array_merge(
            $view->getPartialRootPaths(),
            ['EXT:oauth2_client/Resources/Private/Partials/Backend/'],
            $extensionConfiguration['view']['partialRootPaths'] ?? []
        ));

        $view->setTemplate($extensionConfiguration['view']['template'] ?? 'Oauth2LoginProvider');

        $view->assign('providers', $this->oauth2ProviderManager->getConfiguredBackendProviders());
    }
}
