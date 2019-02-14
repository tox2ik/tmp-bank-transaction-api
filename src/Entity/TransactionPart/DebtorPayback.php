<?php

namespace App\Entity\TransactionPart;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;


/**
 * @Entity
 *
 * @ORM\Table(name="bank_transaction_part_debtor_payback")
 */
class DebtorPayback extends BankTransactionPart
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
