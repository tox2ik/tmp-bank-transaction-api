<?php

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Webmozart\Assert\Assert;

//use PHPUnit\Framework\Assert;


/**
 */
class HttpApiContext implements \Behat\Behat\Context\Context
{

    /**
     * @BeforeScenario
     */
    public function before(BeforeScenarioScope $scope)
    {


    }

    /**
     * @When Client sends a transaction with parts
     */
    public function clientSendsATransactionWithParts(PyStringNode $string)
    {
        throw new PendingException();
    }

    /**
     * @Then Transaction and its parts are properly stored in the database
     */
    public function transactionAndItsPartsAreProperlyStoredInTheDatabase()
    {
        throw new PendingException();
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
