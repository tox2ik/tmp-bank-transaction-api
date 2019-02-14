<?php

use App\Controller\TransactionController;
use App\Entity\BankTransaction;
use Assert\Assertion;
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
 * @property \Symfony\Component\HttpFoundation\Response response
 * @property stdClass jsonResponse
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
     *
     * @Then Client receives a proper response from API
     */
    public function clientReceivesAProperResponseFromApi()
    {
        Assertion::eq(201, $this->response->getStatusCode(), 'Received HTTP status code 201 created');
        $response = json_decode($this->response->getContent());
        Assertion::notEmpty($response->data->uuid ?? null, 'The uuid was auto-generated');
        // REQUIREMENT: auto-generate UUID for HTTP POST /transaction
    }

    /**
     * @Given that there is a transaction identified by :uuid
     */
    public function thatThereIsATransactionIdentifiedBy($uuid, PyStringNode $string)
    {
        /** @var BankTransaction $transaction */
        $transaction = BankTransaction::createFromJson(implode(PHP_EOL, $string->getStrings()), [
            'uuid' => $uuid
        ]);
        $this->em->persist($transaction);
        $this->em->flush();

        Assertion::eq($transaction->id(), 1, 'The transaction exists');
        Assertion::eq($transaction->uuid(), $uuid, 'The explicit uuid was used');
        #Assertion::(1, $transaction->id(), 'The transaction exists');
    }

    /**
     * @When Client requests the transaction :uuid
     */
    public function clientRequestsTheTransaction($uuid)
    {

        $this->response = $response = $this->transactionController->show($uuid, new Request);
        $this->jsonResponse = $json = json_decode($this->response->getContent());
    }

    /**
     * @Then Client receives the transaction
     */
    public function clientReceivesTheTransaction(TableNode $table)
    {
        $assertions = 0;
        foreach ($table as $row) {
            Assertion::eq($this->jsonResponse->data->{$row['field']}, $row['value']);
            $assertions++;
        }

        Assertion::greaterOrEqualThan($assertions, 1);
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
        $parts = $this->jsonResponse->data->parts ?? [];
        Assertion::count($parts, 0+$arg1, "Should find x parts ($arg1)");
    }

    /**
     * @Given an invalid transaction payload
     */
    public function aTransactionWithAnInvalidAmountField(PyStringNode $string)
    {
        $payload = implode(PHP_EOL, $string->getStrings());
        $request = Request::create('/api/v1/transaction', 'POST', [], [], [], [], $payload);
        $this->response = $response = $this->transactionController->create($request);
        $this->jsonResponse = $json = json_decode($this->response->getContent());
    }

    /**
     * @Then the transaction is rejected with http-code :arg1
     */
    public function theTransactionIsRejectedWithHttpCode($statusCode)
    {
        foreach ($this->em->getRepository(BankTransaction::class)->findAll() as $e) {
            $this->em->refresh($e);
        }
        // db($this->em->getConnection()->getWrappedConnection());
        // $a = qAssocAll('select * from bank_transaction');
        Assertion::null($this->jsonResponse->data->amount ?? null);
        Assertion::eq($this->response->getStatusCode(), $statusCode);
    }




}
