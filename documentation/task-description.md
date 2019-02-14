# Billie technical task, backend engineer

Feature: Create a simple RESTful API service to manage bank transactions

_I don't want to write a simple app, I want to write an impressive app so you will hire me_

## work breakdown structure (aka backlog)

- [x] install symfony {1:10m}
- [x] configure doctrine {1:1h}
     - [x] entities (annotations) {1:10m}
- [ ] configure object, metadata-caching on production {1:30m}
- [.] set up error logging {1:30m}
- [ ] handle exceptions {1:0.4h}
    - [ ] doctrine {1:10m}
    - [x] http-404 {1:10m}
    - [x] param-converter {1:10m}
    - [x] no such route {1:10m}
    - [x] no such method {1:10m}
    - [x] random-exeption {1:10m}
- [ ] write the models {1:1h}
    - [x] write or generate entities {1:1.5h}
    - [x] generate migrations {1:1h}
    - [x] define foreign keys {1:0.4h}
    - [ ] write BankTransaction {1:30m}
        - [x] declare valid types and field-formats (validation) {1:30m}
    - [ ] write abstract part {1:30m}
        - [ ] declare valid types and field-formats (validation) {1:30m}
        - [ ] write DebtorPayback {1:10m}
        - [ ] write BankCharge {1:10m}
        - [ ] write PaymentRequest {1:10m}
        - [ ] write Unidentified {1:10m}
    - [x] define "polymorphic" relationship with class-table-inheritance [^parts] {2:1h}
- [ ] define api-v1 {1:3h}
    - [ ] api-scenarios (behat) {1:1h}
        - [x] configure behat {1:30m}
        - [x] inject controllers, entity manager {1:40m}
        - [ ] test random exception -> HTTP 555 {2:20m}
        - [.] test POST /transaction {1:1h}
            - [x] write scenario {1:20m}
            - [.] create a valid transaction {1:1h}
            - [x] reject invalid transactions {1:30m}
            - [ ] reject unbalanced transactions {1:30m}
            - [ ] reject transactions with undeclared (undocumented) parts  {1:20m}
        - [x] test GET /transaction/{uuid} {1:0h}
            - [ ] create with parts -> request by uuid -> verify stored parts {1:40m}
            - [ ] parts are present {1:20m}
            - [x] write scenario {1:20m}
            - [x] unknown transaction (invalid id) {1:10m)
    - [ ] unit: transactions are balanced {1:2h}
- [ ] write transaction controller {1:30m}
    - [x] POST transaction with parts {1:1h}
    - [x] GET transaction {1:1h}
    - [ ] handle (sanity) validation before request reaches controller {1:2h}
        - [x] invalid json {1:30m}
        - [x] invalid format (ints, strings, dates, lengths) {1:1h}
    - [ ] handle type-to-json serialization outside of controller
          (use JSONAPI.org format) {1:2h}
        - [ ] maybe as a middleware
- [ ] write docker-files {1:1h}
    - [ ] memcached {1:1h}
    - [ ] mysql {1:30m}
    - [ ] apache {1:1h}
    - [ ] varnish {1:1h}
- [ ] write asciidoc for API calls {1:2h}

### initial estimation

    $ sum `grep -oEe '\{1:[0-9]+[mh]}' task-description.md | sed -e 's/{1://; s/}//' | sed -e 's/m//; s/h/*60/' | bc`
    2000

34 hours.

Since the preseent time is Tue Feb 12 21:18:09 CET 2019, and I can (reasonably)
only work 10-11 hours per day, I will have to work very effectively or cut some
features before the delivery on the 14th.

## Specification

### specific requirements


- [ ] data model
    - [ ] store common parts in `bank_transaction`
    - [ ] store parts in `bank_transaction_part`
    - [ ] each transaction has a reason
    - [ ] each transaction is represented by a type
    - [ ] transactions have at least one part
    - [ ] uuids are unique in the database
- [ ] implementation 
    - [ ] http-create transaction with parts
    - [ ] each transaction part has its own type
    - [ ] handle exceptions
    - [ ] cover code with tests
    - [ ] accept sloppily-formated dates (yyyy-mm-dd hh:mm)

### loose requirements

code 

- [ ] use all best practices that you know about
    - [ ] from the top of my head
		- [ ] defined code style (PSR2, PSR4)
		- [ ] separation of concerns
		- [ ] thin controllers
		- [ ] predictable HTTP responses
		- [ ] error handling 
		- [ ]  (reversable) migrations
		- [ ] concise documentation
		- [ ] minimal or no code duplication
		- [ ] short operation bodies
		- [ ] predictable and consistent return types
		- [ ] inversion of control
		- [ ] low wtf factor (principle of least astonishment)
		- [ ] logical grouping of code (by feature aka package boundaries)
		- [ ] open-closed principle
		- [ ] pridictable class names (PSR0, PSR4, aka Java packages)
		- [ ] naming conventions class - noun, method - verb
		- [ ] doc comments - signature, return, parameter types
		- [ ] composition over inheritance
		- [ ] code to interface not implementation
		- [ ] shallow hierarchies
		- [ ] unit tests
		- [ ] integration tests
		- [ ] test driven development
		- [ ] domain driven design
		- [ ] factories and static factory methods
		- [ ] atomic transactions where necessary
	- [ ] also useful
	    - [ ] use git, feature branches and versioned releases (e.g. git-flow, semver)
	    - [ ] build your app in accordance with the principles in the 12-factor-app document
	    - [ ] index dtabased that are commonly used in queries
- [ ] use PHP7 and symfony
- [ ] persist data to MySQL

demo

- [ ] describe deployment
- [ ] publish on GitHub

bonus points

- [ ] orchistrate with docker compose
- [ ] document the api
- [ ] respond with cacheable HTTP documents
- [ ] validate input

### Questions and answers

Can the amount be negative in transaction and/or in transaction part?

- No

Is transaction.uuid unique or can several records describe one transaction?

- Unique

Are the fields transaction.id and part.id primary keys?

- Yes

Should the system record the timezone of the transaction?

- Not necessary

Are the values in transaction.amount -- sum(part\*.amount) supposed to be in balance?

- Yes

Should the system reject transactions that are not balanced?

- Not necessary, but nice to have as validation bonus point in description

What is the semantical meaning of the parts DebtorPayback, PaymentRequest, BankCharge?

- It does not matter in this case, they are just names

Which constraints and guidelines should be applied to arrive at the definition
of a "proper response" and "properly stored" (e.g. common HTTP responses?
jsonapi.org documents? up to me to determine?)

- for response: as it is REST endpoint, REST constraints
- for storage: you should use best practices for db structure and relations for stored data

Are the clients allowed to specify the primary keys and the uuid field? if no
then should they be autogenerated?

- Both should be generated unique, please look at the example of transaction in documentation

Should I implement any means of authentication for the clients?

- No, not needed


### Entities and tables

#### BankTransaction

    bank_transaction.id           <Int>
    bank_transaction.uuid         <Uuid>
    bank_transaction.amount       <Decimal>
    bank_transaction.booking_date <DateTime>

#### BankTransactionPart

    bank_transaction_part.id                    <Int>
    bank_transaction_part.bank_transaction_id   <BankTransaction>
    bank_transaction_part.amount                <Decimal>
    bank_transaction_part.reason                <String>

### TransactionReason

I am not going to create a separate table with all the available definitions,
because in my experience such tables are useless for the most part.

    debtor_payback   <DebtorPayback>
    bank_charge      <BankCharge>
    payment_request  <PaymentRequest>
    unidentified     <Unidentified>

### Original task description

#### Scenario 1

Scenario:
A client can create a bank transaction through the given API.
Bank transaction common data should be stored in a `bank transaction` table, each part of
the bank transaction should be stored in `bank transaction part` table.
Transactions have different parts, distinguished by reasons. Each part has its own type.
Transaction must have at least one part.

Given:

1. Entities:
	- a. `bank transaction` entity:
	```
	'id': <Int>
	'uuid': <Uuid>
	'amount': <Decimal>
	'booking date': <DateTime>
	```
	- b. 'bank transaction part' entities:
	```
	'id': <Int>
	'bank_transaction_id': <BankTransaction>
	'amount': <Decimal>
	'reason': <String>
	```
2. Reasons of transaction parts and their types:
	- a. `debtor_payback`: `<DebtorPayback>`
	- b. `bank_charge`: `<BankCharge>`
	- c. `payment_request`: `<PaymentRequest>`
	- d. `unidentified`: `<Unidentified>`
	
When: Client sends a transaction with parts  
Then: Transaction and its parts are properly stored in the database  
And: Client receives a proper response from AP

#### Scenario 2

Scenario: A client provides uuid of a transaction and wants to receive its data
together with parts and parts data.  


Each part in PHP code should be represented as a different type, according to
its reason (for example, `debtor_payback` bank transaction part is a
<DebtorPayback> type).

When: Client sends a request for an explicit transaction
Then: Client receives the transaction with its data
And: Transaction has a list of parts containing their data

Example of a transaction data sent to API:

    {
        'amount': 9.99,
        'booking_date': '2018-01-01 12:00:01',
        'parts': [
            { 'reason': 'debtor_payback', 'amount': 2.00 },
            { 'reason': 'bank_charge', 'amount': 1.00 },
            { 'reason': 'payment_request', 'amount': 1.50 },
            { 'reason': 'unidentified', 'amount': 1.50 },
            { 'reason': 'unidentified', 'amount': 2.00 },
            { 'reason': 'debtor_payback', 'amount': 1.99 }
        ]
    }

It means, that we should have array collection with:

- 2x DebtorPayback objects (and records in DB, with different data)
- 1x BankCharge object
- 1x PaymentRequest object
- 2x Unidentified objects

#### Other requirements 


##### Required functionality

• …is described in scenarios above

##### Technical constraints:

• Use all possible, known to you, best practices of coding.
• Use PHP 7.x and any framework you prefer
• Save the entities in a database. You can use any relational or NoSQL database.
• The exceptions need to be properly handled.
• Cover your code with tests. The technology and the type of testing is up-to-you to choose.

##### Delivering the task

• Provide a straight-forward method of running your project.
• Use Github to share your project, we would love to see your commits ;-)

##### Bonus points

• A working Dockerfile or Docker-compose configuration to run your project and any dependencies, like the database is provided.
• A proper API-Documentation is written.
• The Cache headers, including the Etag header, are used.
• The data which is sent by a client is properly validate

## Footnotes

[^parts]: The class table inheritance is preferable over single table inheritance in this case

    Because;
    I suspect there will be many different transaction parts in the future leading to a messy
    table with many columns.

    The performance impat of multiple joins for a single transaction is mitigated by the fact that
    the transactions are immutable, thus allowing for reliable caching.



