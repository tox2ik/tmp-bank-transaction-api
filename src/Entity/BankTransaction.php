<?php


namespace App\Entity;

use App\Entity\TransactionPart\BankTransactionPart;
use App\Traits\SetOrGetTrait;
use Doctrine\ORM\Mapping as ORM;
use Faker\Provider\Base;
use Faker\Provider\Uuid;
use PhpParser\Node\Stmt\Unset_;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
// use JMS\Serializer\Annotation\Expose;

/**
 * BankTransaction
 *
 * @ORM\Table(name="bank_transaction", indexes={@ORM\Index(name="bt_uuid", columns={"uuid"})})
 * @ORM\Entity
 *
 * @ORM\HasLifecycleCallbacks
 *
 */
class BankTransaction implements \JsonSerializable
{

    /*
     *
     * NOTE: Usually, I generate the set-or-get mutators by swapping out the entity-generator in doctrine.
     *
     * NOTE: This approach (set-or-get) is not always appropriate, for example when treating (related) aggregates.
     *       In those cases we may want to return (immutable) clones.
     *
     */
    use SetOrGetTrait;


    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * $ Expose()
     */
    private $id;

    /**
     * @var string|null
     *
     * $ ORM\GeneratedValue(strategy="UUID") maybe-todo: generated value is reserved for primary key.
     *                                                   write a custom annotation to acheive the same thing
     * @ORM\Column(name="uuid", type="guid")
     *
     * ORM\Column(name="uuid", type="string", length=36, nullable=true)
     */
    private $uuid;

    /**
     * @var string|null
     *
     * todo: move validation to "MoneyAmount"
     * @Assert\Regex(
     *     "/^-*([0-9]+)+(\.[0-9]+)?/",
     *      message="The amount must be a number such as: 1, 1.0, 0.3, -1, -1.2, 2203.3313, .3"
     * )
     * @ORM\Column(name="amount", type="decimal", precision=22, scale=2, nullable=true)
     * $ Expose()
     */

    private $amount;

    /**
     * @var \DateTime|null
     * @Assert\DateTime()
     *
     * @ORM\Column(name="booking_date", type="datetime", nullable=true)
     * $ Expose
     */
    private $bookingDate;




    /**
     * todo: move the factory-method somewhere else.
     */
    public static function createFromJson(string $json, array $overrideJson = []): BankTransaction
    {
        //$jsondecoder = new JsonDecode(); // todo: don't call new here, move to factory
        $params = json_decode($json, $asArray=true, 10); // CLEVER: anticipate DDOS, use shallow depth.
        if (null === $params && ! empty($json)) {
            throw new \RuntimeException(sprintf('Failed to parse json: %s', json_last_error_msg()));
        }



        $params = array_merge($params, $overrideJson);
        $params['booking_date'] = createDateTimeFromIso8601Format(
            $params['booking_date'] ?? null,
            $timeZone=null,
            [ 'Y-m-d H:i:s' ]
        );

        $snake_to_camel = [
            'booking_date' => 'bookingDate'

        ];


        $transaction = new BankTransaction;

        $parts = [];
        $partParams = $params['parts'] ?? null;

        if ($partParams) {
            unset($params['parts']);
        }

        foreach ($partParams as $part) {
            $parts[] = $part = BankTransactionPart::creareFromParams($part);
            $part->transaction($transaction);
        }
        $transaction->parts($parts);


        foreach ($params as $i => $e) {
            $validPropName = null;
            if (property_exists($transaction, $i)) {
                $validPropName = $i;
            } elseif (property_exists($transaction, $snake_to_camel[$i] ?? null)) {
                $validPropName = $snake_to_camel[$i];
            }

            if ($validPropName) {
                $transaction->{$validPropName} = $e;
            }
        }

        return $transaction;
    }

    /**
     * todo: move the factory-method somewhere else.
     * @throws \RuntimeException
     */
    public static function createFromRequest(\Symfony\Component\HttpFoundation\Request $request): BankTransaction
    {
        return BankTransaction::createFromJson($request->getContent());
    }


    /**
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\TransactionPart\BankTransactionPart",
     *     mappedBy="transaction",
     *     cascade={"persist"}
     * )
     */
    private $parts;




//    /**
//     * @Assert\Callback
//     */
//    public function verifyTransactionMinimumOnePart(ExecutionContextInterface $context, $payload): void
//    {
//
//        $context->buildViolation('This name sounds totally fake!')
//            ->atPath('firstName')
//            ->addViolation();
//    }



    /** @return self|integer */ public function id() { return $this->setOrGet(func_get_args(), $this->id); }
    /** @return self|float */ public function amount() { return $this->setOrGet(func_get_args(), $this->amount); }
    /** @return self|string */ public function uuid() { return $this->setOrGet(func_get_args(), $this->uuid); }
    /** @return self|\DateTimeInterface */ public function bookingDate() { return $this->setOrGet(func_get_args(), $this->bookingDate); }
    /** @return self|float */ public function parts() { return $this->setOrGet(func_get_args(), $this->parts); }




    /**
     * todo: encode-json-response: pass through middleware or serializer to support custom or version-specific output
     *       (YAGNI for now)
     *
     * todo: check out "jms/serializer": "^1.13",

     */
    public function jsonSerialize(): array
    {
        $pp = $this->parts ? $this->parts->toArray() : [];
        return [
            'id' => $this->id,
            'type' => basename(str_replace('\\', '/', get_class($this))),
            'uuid' => $this->uuid,
            'amount' => $this->amount, // should not need to round because we defined the precision in the database.
            'parts' => $pp,
            'bookingDate' => $this->bookingDate instanceof \DateTimeInterface
                ? $this->bookingDate->format(\DateTIme::ISO8601)
                : null
        ];
    }




     /**
      * todo: move id generation to an identity-value-object
      * (because: abusing the lifecycle events is not cool,
      * becasue: we are violating the least astonishment principle)
      *
      * @ORM\PrePersist()
      */
     public function generateUuid()
     {
         $this->uuid = $this->uuid ?: Uuid::uuid();
     }

}
