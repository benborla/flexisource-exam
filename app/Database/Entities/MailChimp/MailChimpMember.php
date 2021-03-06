<?php
declare(strict_types=1);

namespace App\Database\Entities\MailChimp;

use Doctrine\ORM\Mapping as ORM;
use EoneoPay\Utils\Str;

/**
 * @ORM\Entity()
 */
class MailChimpMember extends MailChimpEntity
{
    /**
     * @ORM\OneToOne(targetEntity="MailChimpList")
     * @ORM\JoinColumn(name="list_id", referencedColumnName="id")
     */
    protected $mailChimpList;
    /**
     * @ORM\Id()
     * @ORM\Column(name="id", type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @var string
     */
    private $memberId;
    /**
     * @ORM\Column(name="list_id", type="string", columnDefinition="CHAR(36)")
     *
     * @var string
     */
    private $listId;
    /**
     * @ORM\Column(name="mail_chimp_id", type="string", nullable=true)
     *
     * @var string
     */
    private $mailChimpId;
    /**
     * @ORM\Column(name="email_address", type="string")
     *
     * @var string
     */
    private $emailAddress;
    /**
     * @ORM\Column(name="status", type="string")
     *
     * @var string
     */
    private $status;
    /**
     * @ORM\Column(name="subscriber_hash", type="string", nullable=true)
     *
     * @var string
     */
    private $subscriberHash;

    /**
     * Get member's id
     *
     * @return null|string
     */
    public function getId(): ?string
    {
        return $this->memberId;
    }


    /**
     * Get member's list id
     *
     * @return null|string
     */
    public function getListId(): ?string
    {
        return $this->listId;
    }

    /**
     * Set list id of the member.
     *
     * @param string $listId
     *
     * @return MailChimpMember
     */
    public function setListid(string $listId): MailChimpMember
    {
        $this->listId = $listId;

        return $this;
    }

    /**
     * Get Mailchimp id of the list.
     *
     * @return null|string
     */
    public function getMailChimpId(): ?string
    {
        return $this->mailChimpId;
    }

    /**
     * Set Mailchimp id of the list.
     *
     * @param string $mailChimpId
     *
     * @return \App\Database\Entities\MailChimp\MailChimpList
     */
    public function setMailChimpId(string $mailChimpId): MailChimpMember
    {
        $this->mailChimpId = $mailChimpId;

        return $this;
    }

    /**
     * Get member's subscriber hash
     *
     * @return null|string
     */
    public function getSubscriberHash(): ?string
    {
        return $this->subscriberHash;
    }

    /**
     * Set status of the member.
     *
     * @param string $subscriberHash
     *
     * @return \App\Database\Entities\MailChimp\MailChimpMember
     */
    public function setSubscriberHash(string $subscriberHash): MailChimpMember
    {
        $this->subscriberHash = $subscriberHash;

        return $this;
    }

    /**
     * Get validation rules for mailchimp entity.
     *
     * @return array
     */
    public function getValidationRules(): array
    {
        return [
            'list_id' => 'nullable|string',
            'email_address' => 'required|email',
            'status' => 'required|string',
            'subscriber_hash' => 'nullable|string',
        ];
    }

    /**
     * Set Email Address of the member.
     *
     * @param string $emailAddress
     *
     * @return MailChimpMember
     */
    public function setEmailAddress(string $emailAddress): MailChimpMember
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    /**
     * Set status of the member.
     *
     * @param string $status
     *
     * @return MailChimpMember
     */
    public function setStatus(string $status): MailChimpMember
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get array representation of entity.
     *
     * @return array
     */
    public function toArray(): array
    {
        $array = [];
        $str = new Str();

        foreach (\get_object_vars($this) as $property => $value) {
            $array[$str->snake($property)] = $value;
        }

        return $array;
    }
}
