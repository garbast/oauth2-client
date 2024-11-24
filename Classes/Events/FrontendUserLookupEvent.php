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

namespace Waldhacker\Oauth2Client\Events;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessTokenInterface;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;

final class FrontendUserLookupEvent
{
    public function __construct(
        private readonly string $providerId,
        private readonly AbstractProvider $provider,
        private readonly AccessTokenInterface $accessToken,
        private readonly ResourceOwnerInterface $remoteUser,
        private ?array $typo3User,
        private readonly ?SiteInterface $site,
        private readonly ?SiteLanguage $language
    ) {
    }

    public function getProviderId(): string
    {
        return $this->providerId;
    }

    public function getProvider(): AbstractProvider
    {
        return $this->provider;
    }

    public function getAccessToken(): AccessTokenInterface
    {
        return $this->accessToken;
    }

    public function getRemoteUser(): ResourceOwnerInterface
    {
        return $this->remoteUser;
    }

    public function getTypo3User(): ?array
    {
        return $this->typo3User;
    }

    public function setTypo3User(array $typo3User): void
    {
        $this->typo3User = $typo3User;
    }

    public function getSite(): ?SiteInterface
    {
        return $this->site;
    }

    public function getLanguage(): ?SiteLanguage
    {
        return $this->language;
    }
}
