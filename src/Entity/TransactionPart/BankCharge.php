<?php

namespace App\Entity\TransactionPart;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Entity
 *
 * @ORM\Table(name="bank_transaction_part_bank_charge")
 */
class BankCharge extends BankTransactionPart
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

}
