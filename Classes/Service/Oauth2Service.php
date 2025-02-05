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

namespace Waldhacker\Oauth2Client\Service;

use Exception;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Log\LogManager;
use Waldhacker\Oauth2Client\Session\SessionManager;

readonly class Oauth2Service
{
    private LoggerInterface $logger;

    public function __construct(
        private Oauth2ProviderManager $oauth2ProviderManager,
        private SessionManager $sessionManager,
        private LogManager $logManager,
    ) {
        $this->logger = $this->logManager->getLogger(__CLASS__);
    }

    public function buildGetResourceOwnerAuthorizationUrl(
        string $providerId,
        ?string $callbackUrl = null,
        ServerRequestInterface $request = null
    ): string {
        $provider = $this->oauth2ProviderManager->createProvider($providerId, $callbackUrl);
        $authorizationUrl = $provider->getAuthorizationUrl();
        $this->sessionManager->setAndSaveSessionData(
            SessionManager::SESSION_NAME_STATE,
            $provider->getState(),
            $request
        );

        return $authorizationUrl;
    }

    public function buildGetResourceOwnerProvider(
        string $state,
        string $providerId,
        ?string $callbackUrl = null,
        ServerRequestInterface $request = null
    ): ?AbstractProvider {
        $oauth2StateFromSession = $this->sessionManager->getSessionData(SessionManager::SESSION_NAME_STATE, $request);

        $this->sessionManager->setAndSaveSessionData(SessionManager::SESSION_NAME_STATE, null, $request);
        if (empty($oauth2StateFromSession) || $oauth2StateFromSession !== $state) {
            return null;
        }

        return $this->oauth2ProviderManager->createProvider($providerId, $callbackUrl);
    }

    public function buildGetResourceOwnerAccessToken(AbstractProvider $provider, string $code): ?AccessToken
    {
        try {
            $accessToken = $provider->getAccessToken(
                'authorization_code',
                [
                    'code' => $code,
                ]
            );
            if ($accessToken instanceof AccessToken) {
                return $accessToken;
            }
        } catch (Exception $e) {
            $this->logger->warning($e->getMessage());
        }
        return null;
    }

    public function getResourceOwner(AbstractProvider $provider, AccessToken $accessToken): ?ResourceOwnerInterface
    {
        $user = null;
        try {
            $user = $provider->getResourceOwner($accessToken);
        } catch (Exception $e) {
            $this->logger->warning($e->getMessage());
        }
        return $user;
    }
}
