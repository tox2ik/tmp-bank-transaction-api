<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;

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
     * @ORM\Column(name="amount", type="decimal", precision=22, scale=2, nullable=true)
     */
    private $amount;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="booking_date", type="datetime", nullable=true)
     */
    private $bookingDate;


    /** @OneToMany(targetEntity="BankTransactionPart", mappedBy="bankTransaction") */
    private $parts;


}
