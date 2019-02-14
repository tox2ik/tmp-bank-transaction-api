<?php




namespace App\Entity\TransactionPart;


use App\Entity\BankTransaction;
use App\Traits\SetOrGetTrait;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\InheritanceType;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Table(name="bank_transaction_part", indexes={@ORM\Index(name="btt_transaction_fk", columns={"bank_transaction_id"})})
 * @ORM\Entity
 *
 * @InheritanceType("JOINED")
 * @DiscriminatorColumn(name="reason", type="string")
 * @DiscriminatorMap({
 *     "base" = "BankTransactionPart",
 *     "bank_charge" = "BankCharge",
 *     "debtor_payback" = "DebtorPayback",
 *     "payment_request" = "PaymentRequest",
 *     "unidentified" = "Unidentified"
 * })
 * CLEVER: please keep the above list lexicographically ordered
 * maybe-todo: RFE; move this sub-class definition to xml or yaml to facilitate adding parts later
 *                  (i.e. so we don't have to edit this class every time a new part is defined)
 */
class BankTransactionPart implements \JsonSerializable
{
    use SetOrGetTrait;

    protected static $types = [
        'debtor_payback' => DebtorPayback::class,
        'bank_charge' => BankCharge::class,
        'payment_request' => PaymentRequest::class,
        'unidentified' => Unidentified::class
    ];

    /**
     *
     * todo: map part name to Class dynamically (as opposed to hard-coded)
     * todo: support custom fields in parts (maybe use template pattern)
     *
     * @param array $params = [
     *   'reason' => '', // (string, required)
     *   'amount' => '', // (float, required)
     * ]
     *
     */
    public static function creareFromParams(array $params): BankTransactionPart
    {
        $type = static::$types[$params['reason']] ?: null;
        /** @var BankTransactionPart $instance */
        $instance = (new $type)->amount($params['amount']);
        return $instance;



    }

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     * todo: move validation to "MoneyAmount"
     * @Assert\Regex("/^-*([0-9]+)?(\.[0-9]+)?/", message="The amount must be a number such as: 1, 1.0, 0.3, -1, -1.2, 2203.3313, .3")
     * @ORM\Column(name="amount", type="decimal", precision=22, scale=2, nullable=true)
     */
    private $amount;

    ///**
    // * @var string
    // *
    // *
    // * Assert\Choice(
    // *     choices={ "yearly", "quarterly", "monthly", "weekly", "daily", "business_daily"},
    // *     message="Unknown recurrence period. try: y (yearly), m (monthly), q (quarterly), m (monthly), w (weeky), d //(daily), bd (business_daily)")
    // *
    // * @Assert\Callback({"isReasonValid"})
    // *
    // * @ORM\Column(name="reason", type="string", length=80, nullable=false)
    // *
    // *
    // * maybe-todo: CLEVER: query definintions in the descriminator-map and compare to defined values in that.
    // */
    private $reason = '';



    /**
     * @var BankTransaction
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\BankTransaction")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="bank_transaction_id", referencedColumnName="id")
     * })
     */
    private $transaction;


    /**
     * @Assert\Callback
     */
    protected function isReasonValid()
    {
        //$arg = func_get_args();
        $reason = null;
        $valid = [
            'DebtorPayback' => true,
            'BankCharge' => true,
            'PaymentRequest' => true,
            'Unidentified' => true
        ];
        throw new \RuntimeException('Not implemented.');


        return isset($valid[$reason]);

    }

    /** @return self|integer */ public function id() { return $this->setOrGet(func_get_args(), $this->id); }
    /** @return self|float */ public function amount() { return $this->setOrGet(func_get_args(), $this->amount); }
    /** @return self|BankTransaction */ public function transaction() { return $this->setOrGet(func_get_args(), $this->transaction); }

    public function jsonSerialize()
    {
        $snakeTypes = array_flip(static::$types);
        $reason = $snakeTypes[get_class($this)] ?? null;

        return [
            'id' => $this->id,
            'type' => basename(str_replace('\\', '/', get_class($this))),
            'amount' => $this->amount, // should not need to round because we defined the precision in the database.
            'reason' => $reason
        ];
    }
}
