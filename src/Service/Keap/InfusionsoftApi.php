<?php

namespace App\Service\Keap;

use Infusionsoft\Http\HttpException;
use Infusionsoft\TokenExpiredException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class InfusionsoftApi
{
    private const SLEEP = 500 * 1000;  // 0,5 second in microseconds
    private const EXPIRATION_TIME = 60 * 60 * 24 * 7; // 1 week in seconds

    private TagAwareCacheInterface $cache;

    public function __construct(
        private readonly InfusionsoftGenerator $infusionsoftGenerator,
        TagAwareCacheInterface $myCachePool
    ) {
        $this->cache = $myCachePool;
    }

    public function getCachedSubscriptionsData(string $crm_email): array
    {
        try {
            return $this->cache->get(
                'contact_subscriptions_data_'.str_replace('@', '_', $crm_email),
                function (ItemInterface $item) use ($crm_email) {

                    $item->expiresAfter(self::EXPIRATION_TIME);
                    $item->tag('infusionsoft');

                    try {
                        return $this->getSubscriptionsData($crm_email);
                    } catch (HttpException|TokenExpiredException) {
                        $item->expiresAfter(0);

                        return [];
                    }
                }
            );
        } catch (InvalidArgumentException) {
            return [];
        }
    }

    public function getCachedCompanyData(string $crm_email): array
    {
        try {
            return $this->cache->get(
                'contact_company_data_'.str_replace('@', '_', $crm_email),
                function (ItemInterface $item) use ($crm_email) {

                    $item->expiresAfter(self::EXPIRATION_TIME);
                    $item->tag('infusionsoft');

                    try {
                        return $this->getCompanyData($crm_email);
                    } catch (HttpException|TokenExpiredException) {
                        $item->expiresAfter(0);

                        return [];
                    }
                }
            );
        } catch (InvalidArgumentException) {
            return [];
        }
    }

    public function getSubscriptionsData(string $crm_email): array
    {
        $users = [];
        $page = 0;

        while (true) {
            while (true) {

                usleep(self::SLEEP);

                $data = $this->infusionsoftGenerator
                    ->getInfObject()
                    ->data()
                    ->query(
                        'RecurringOrderWithContact',
                        1000,
                        $page,
                        ['Email' => $crm_email],
                        ['Email', 'ProductId', 'Status'],
                        'Email',
                        true
                    );

                if ((!empty($data) && isset($data[0]['Email'])) || empty($data)) {
                    break;
                }
            }

            if (!empty($data)) {
                foreach ($data as $user) {
                    $users[mb_strtolower($user['Email'])][$user['Status']][] = $user['ProductId'];
                }
            }

            if (count($data) < 1000) {
                break;
            }

            $page++;
        }

        return $users;
    }

    public function getCompanyData(string $crm_email): array
    {
        $users = [];
        $page = 0;

        while (true) {
            while (true) {

                usleep(self::SLEEP);

                $data = $this->infusionsoftGenerator
                    ->getInfObject()
                    ->data()
                    ->query(
                        'Contact',
                        1000,
                        $page,
                        ['Email' => $crm_email],
                        ['Email', 'Company'],
                        'Email',
                        true
                    );

                if ((!empty($data) && isset($data[0]['Email'])) || empty($data)) {
                    break;
                }
            }

            if (!empty($data)) {
                foreach ($data as $user) {
                    $users[mb_strtolower($user['Email'])] = $user['Company'] ?? null;
                }
            }

            if (count($data) < 1000) {
                break;
            }

            $page++;
        }

        return $users;
    }

    public function getContactActiveSubscriptions(string $crm_email): int
    {
        $data = $this->getCachedSubscriptionsData($crm_email);

        if (isset($data[$crm_email]['Active'])) {
            return count($data[$crm_email]['Active']);
        }

        return 0;
    }

    public function isExSubscriber(string $crm_email): int
    {
        $data = $this->getCachedSubscriptionsData($crm_email);

        if (isset($data[$crm_email]['Inactive']) && count($data[$crm_email]['Inactive']) > 0) {
            return 1;
        }

        return 0;
    }

    public function getContactCompanyExists(string $crm_email): int
    {
        $data = $this->getCachedCompanyData($crm_email);

        if (isset($data[$crm_email]) && !empty($data[$crm_email])) {
            return 1;
        }

        return 0;
    }
}