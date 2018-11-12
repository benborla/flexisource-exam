<?php
declare(strict_types=1);

namespace Tests\App\TestCases\MailChimp;

use App\Database\Entities\MailChimp\MailChimpList;
use App\Database\Entities\MailChimp\MailChimpMember;
use Illuminate\Http\JsonResponse;
use Mailchimp\Mailchimp;
use Mockery;
use Mockery\MockInterface;
use Tests\App\TestCases\WithDatabaseTestCase;

abstract class MemberTestCase extends WithDatabaseTestCase
{
    protected const MAILCHIMP_EXCEPTION_MESSAGE = 'MailChimp exception';
    /**
     * @var array
     */
    protected static $listData = [
        'name' => 'New list for member testing',
        'permission_reminder' => 'Apple Weekly Newsletter',
        'email_type_option' => false,
        'contact' => [
            'company' => 'Apple Inc.',
            'address1' => 'Apple Campus',
            'address2' => '',
            'city' => 'Cupertino',
            'state' => 'California',
            'zip' => '95014',
            'country' => 'US',
            'phone' => '4086065775',
        ],
        'campaign_defaults' => [
            'from_name' => 'Steve Jobs',
            'from_email' => 'stevejobs@apple.com',
            'subject' => 'Hello from Apple!',
            'language' => 'US',
        ],
        'visibility' => 'pub',
        'use_archive_bar' => false,
        'notify_on_subscribe' => 'apple@apple.com',
        'notify_on_unsubscribe' => 'apple@apple.com',
    ];
    protected static $memberData = [
        'list_id' => '98776d78-e634-11e8-9f32-f2801f1b9fd1',
        'mail_chimp_id' => '272ed4eff3',
        'email_address' => 'benborla@icloud.com',
        'status' => 'subscribed',
        'subscriber_hash' => 'b31ea795c9628e8a4d0d0a516b961e8d',
    ];
    /**
     * @var array
     */
    protected static $notRequired = [
        'list_id',
        'mail_chimp_id',
        'subscriber_hash',
    ];
    /**
     * @var array
     */
    protected $createdMemberIds = [];

    /**
     * Call MailChimp to delete list created during test (deleting the list will delete the members)
     *
     * @return void
     */
    public function tearDown(): void
    {
        /** @var Mailchimp $mailChimp */
        $mailChimp = $this->app->make(Mailchimp::class);

        if (isset($this->createdListIds)) {
            foreach ($this->createdListIds as $listId) {
                // Delete list on MailChimp after test
                $mailChimp->delete(\sprintf('lists/%s', $listId));
            }
        }

        parent::tearDown();
    }

    /**
     * Asserts error response when member not found.
     *
     * @param string $memberId
     *
     * @return void
     */
    protected function assertMemberNotFoundResponse(string $memberId): void
    {
        $content = \json_decode($this->response->content(), true);

        $this->assertResponseStatus(404);
        self::assertArrayHasKey('message', $content);
        self::assertEquals(\sprintf('MailChimpMember[%s] not found', $memberId), $content['message']);
    }

    /**
     * Asserts error response when MailChimp exception is thrown.
     *
     * @param \Illuminate\Http\JsonResponse $response
     *
     * @return void
     */
    protected function assertMailChimpExceptionResponse(JsonResponse $response): void
    {
        $content = \json_decode($response->content(), true);

        self::assertEquals(400, $response->getStatusCode());
        self::assertArrayHasKey('message', $content);
        self::assertEquals(self::MAILCHIMP_EXCEPTION_MESSAGE, $content['message']);
    }

    /**
     * Create MailChimp list into database.
     *
     * @param array $data
     *
     * @return \App\Database\Entities\MailChimp\MailChimpList
     */
    protected function createList(array $data): MailChimpList
    {
        $list = new MailChimpList($data);

        $this->entityManager->persist($list);
        $this->entityManager->flush();

        return $list;
    }

    /**
     * Create MailChimp member into database.
     *
     * @param array $data
     *
     * @return \App\Database\Entities\MailChimp\MailChimpMember
     */
    protected function createMember(array $data): MailChimpMember
    {
        $member = new MailChimpMember($data);

        $this->entityManager->persist($member);
        $this->entityManager->flush();

        return $member;
    }

    /**
     * Returns mock of MailChimp to trow exception when requesting their API.
     *
     * @param string $method
     *
     * @return \Mockery\MockInterface
     *
     * @SuppressWarnings(PHPMD.StaticAccess) Mockery requires static access to mock()
     */
    protected function mockMailChimpForException(string $method): MockInterface
    {
        $mailChimp = Mockery::mock(Mailchimp::class);

        $mailChimp
            ->shouldReceive($method)
            ->once()
            ->withArgs(function (string $method, ?array $options = null) {
                return !empty($method) && (null === $options || \is_array($options));
            })
            ->andThrow(new \Exception(self::MAILCHIMP_EXCEPTION_MESSAGE));

        return $mailChimp;
    }
}
