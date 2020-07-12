<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\DataFixtures\AppFixtures;
use Hautelook\AliceBundle\PhpUnit\RecreateDatabaseTrait;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TestBase extends WebTestCase
{
    use FixturesTrait;
    use RecreateDatabaseTrait;

    protected const FORMAT = 'jsonld';

    protected const IDS = [
        'admin_id' => 'd781a25f-da6c-42ee-9261-7cc8ddd97001',
        'user_id' => 'd781a25f-da6c-42ee-9261-7cc8ddd97002',
        'admin_group_id' => 'd781a25f-da6c-42ee-9261-7cc8ddd97003',
        'user_group_id' => 'd781a25f-da6c-42ee-9261-7cc8ddd97004',
        'admin_category_id' => 'd781a25f-da6c-42ee-9261-7cc8ddd97005',
        'user_category_id' => 'd781a25f-da6c-42ee-9261-7cc8ddd97006',
    ];

    private static ?KernelBrowser $client = null;
    protected static ?KernelBrowser $admin = null;
    protected static ?KernelBrowser $user = null;

    public function setUp(): void
    {
        //$this->resetDatabase();

        if (null === self::$client) {
            self::$client = static::createClient();
        }

        //  Load fixtures in test database using RecreateDatabaseTrait
        $this->postFixtureSetup();
        $this->loadFixtures([AppFixtures::class]);

        if (null === self::$admin) {
            self::$admin = clone self::$client;
            $this->createAuthenticatedUser(self::$admin, 'admin@api.com', 'password');
        }

        if (null === self::$user) {
            self::$user = clone self::$client;
            $this->createAuthenticatedUser(self::$user, 'user@api.com', 'password');
        }
    }

    private function createAuthenticatedUser(KernelBrowser &$client, string $username, string $password): void
    {
        $client->request(
            'POST',
            '/api/v1/login_check',
            [
                '_email' => $username,
                '_password' => $password,
            ]
        );

        $data = json_decode($client->getResponse()->getContent(), true);

        $client->setServerParameters([
            'HTTP_Authorization' => \sprintf('Bearer %s', $data['token']),
            'CONTENT_TYPE' => 'application/json',
        ]);
    }

    protected function getResponseData(Response $response): array
    {
        return  json_decode($response->getContent(), true);
    }
}
