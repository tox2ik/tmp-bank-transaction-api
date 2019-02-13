<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * BankTransactionPart
 *
 * @ORM\Table(name="bank_transaction_part", indexes={@ORM\Index(name="btt_transaction_fk", columns={"bank_transaction_id"})})
 * @ORM\Entity
 */
class BankTransactionPart
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
     * @ORM\Column(name="amount", type="decimal", precision=22, scale=2, nullable=true)
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="reason", type="string", length=80, nullable=false)
     */
    private $reason = '';

    /**
     * @var \BankTransaction
     *
     * @ORM\ManyToOne(targetEntity="BankTransaction")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="bank_transaction_id", referencedColumnName="id")
     * })
     */
    private $bankTransaction;


}
