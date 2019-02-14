<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use App\Entity\TransactionPart\BankTransactionPart;

/**
 * BankTransaction
 *
 * @ORM\Table(name="bank_transaction", indexes={@ORM\Index(name="bt_uuid", columns={"uuid"})})
 * @ORM\Entity
 */
class BankTransaction
{
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
     *
     * @ORM\Column(name="uuid", type="string", length=36, nullable=true)
     */
    private $uuid;

    /**
     * @var string|null
     *
     * todo: move validation to "MoneyAmount"
     * @Assert\Regex(
     *     "/^-*([0-9]+)?(\.[0-9]+)?/",
     *      message="The amount must be a number such as: 1, 1.0, 0.3, -1, -1.2, 2203.3313, .3"
     * )
     * @ORM\Column(name="amount", type="decimal", precision=22, scale=2, nullable=true)
     */
    private $amount;

    /**
     * @var \DateTime|null
     * @Assert\DateTime()
     *
     * @ORM\Column(name="booking_date", type="datetime", nullable=true)
     */
    private $bookingDate;




    ///**
    // * @OneToMany(targetEntity="App\Entity\TransactionPart\BankTransactionPart", mappedBy="bankTransaction")
    // */
    //private $parts;

    /**
     * @Assert\Callback
     */
    public function verifyTransactionMinimumOnePart(ExecutionContextInterface $context, $payload): void
    {

        $context->buildViolation('This name sounds totally fake!')
            ->atPath('firstName')
            ->addViolation();
    }


}
