<?php

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
#use Webmozart\Assert\Assert;
use Assert\Assert;
use Symfony\Component\HttpKernel\Kernel;

//use PHPUnit\Framework\Assert;

/**
 */
class HttpApiContext implements \Behat\Behat\Context\Context
{

    use CreatesDatabaseTrait;

    /**
     * @var TransactionController
     */
    public $transactionController;

    /**
     * @var EntityManagerInterface
     */
    public $em;

    public function __construct(
        Kernel $kernel,
        TransactionController $transactionController,
        EntityManagerInterface $em
    )
    {
        $this->transactionController = $transactionController;
        $this->em = $em;
        $this->kernel = $kernel;
    }

    /**
     * @BeforeScenario
     */
    public function before(BeforeScenarioScope $scope)
    {
        $this->runMigrations();
    }

    /**
     * @When Client sends a transaction with parts
     */
    public function clientSendsATransactionWithParts(PyStringNode $string)
    {
        $payload = implode(PHP_EOL, $string->getStrings());
        $request = Request::create('/api/v1/transaction', 'POST', [], [], [], [], $payload);
        $this->response = $resp = $this->transactionController->create($request);

    }

    /**
     * @Then Transaction and its parts are properly stored in the database
     */
    public function transactionAndItsPartsAreProperlyStoredInTheDatabase()
    {

        $transaction = $this->em->find(BankTransaction::class, 1);
        Assertion::notNull($transaction, 'The transaction was persisted in the controller');
        Assertion::eq(9.99, $transaction->amount(), 'The stored amount is 9.99');
        Assertion::eq(count($this->em->getRepository(BankTransaction::class)->findAll()), 1);
        Assertion::isInstanceOf(
            $transaction->bookingDate(),
            DateTimeInterface::class,
            'We accept sloppy dates (non-iso8601)'
        );
    }

    /**
     * @Then Client receives a proper response from API
     */
    public function clientReceivesAProperResponseFromApi()
    {
        throw new PendingException();
    }

    /**
     * @Given that there is a transaction identified by :arg1
     */
    public function thatThereIsATransactionIdentifiedBy($arg1, PyStringNode $string)
    {
        throw new PendingException();
    }

    /**
     * @When Client requests the transaction :arg1
     */
    public function clientRequestsTheTransaction($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then Client receives the transaction
     */
    public function clientReceivesTheTransaction(TableNode $table)
    {
        throw new PendingException();
    }

    /**
     * @Then the transaction enumerates the parts
     */
    public function theTransactionEnumeratesTheParts(TableNode $table)
    {
        throw new PendingException();
    }

    /**
     * @Then the total number of parts is :arg1
     */
    public function theTotalNumberOfPartsIs($arg1)
    {
        throw new PendingException();
    }



}
