Feature: Basic transaction operations
  In order to keep track of money
  Clients of the HTTP-API need to
  utilize the POST and GET verbs under the /api/v1/transaction endpoint




  Scenario: Create a transaction with parts (POST)
    When Client sends a transaction with parts
    """
    {
        "amount": 9.99,
        "booking_date": "2018-01-01 12:00:01",
        "parts": [
            { "reason": "debtor_payback", "amount": 2.00 },
            { "reason": "bank_charge", "amount": 1.00 },
            { "reason": "payment_request", "amount": 1.50 },
            { "reason": "unidentified", "amount": 1.50 },
            { "reason": "unidentified", "amount": 2.00 },
            { "reason": "debtor_payback", "amount": 1.99 }
        ]
    }
    """
    Then Transaction and its parts are properly stored in the database
    And Client receives a proper response from API

  Scenario: Extract information about a given transaction (GET)
    Given that there is a transaction identified by "313effb7-44f3-4d3b-a3e3-187dd127c64a"
    """
    {
        "amount": 3,
        "booking_date": "2019-02-02 12:00:00",
        "parts": [
            { "reason": "unidentified", "amount": 1 },
            { "reason": "unidentified", "amount": 1 },
            { "reason": "unidentified", "amount": 2 },
        ]
    }
    """
    When Client requests the transaction "313effb7-44f3-4d3b-a3e3-187dd127c64a"
    Then Client receives the transaction
    | field       | value                   |
    | amount      | 9.99                    |
    | bookingDate | Mon Jan 1 12:00:01 2018 |
    And the transaction enumerates the parts
    | partType           | count      |
    | DebtorPayback      | 2          |
    | BankCharge         | 1          |
    | PaymentRequest     | 1          |
    | Unidentified       | 2          |
    And the total number of parts is 7


