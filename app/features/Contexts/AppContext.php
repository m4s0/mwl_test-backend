<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use App\Service\DropAndCreateEventStore;
use App\Service\DropAndCreateReadModel;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;
use Broadway\CommandHandling\CommandBus;
use Coduo\PHPMatcher\Factory\MatcherFactory;
use Domain\Basket\Command\PickUpBasket;
use Domain\Basket\ValueObject\BasketId;
use Domain\Product\Repository\FileSystemInterface;
use RuntimeException;

class AppContext implements Context
{
    private Session $session;
    private CommandBus $commandBus;
    private DropAndCreateEventStore $dropAndCreateEventStore;
    private DropAndCreateReadModel $dropAndCreateReadModel;
    private FileSystemInterface $fileSystem;
    /**
     * @var string
     */
    private string $productsPath;

    public function __construct(
        Session $session,
        CommandBus $commandBus,
        DropAndCreateEventStore $dropAndCreateEventStore,
        DropAndCreateReadModel $dropAndCreateReadModel,
        FileSystemInterface $fileSystem,
        string $productsPath
    ) {
        $this->session = $session;
        $this->commandBus = $commandBus;
        $this->dropAndCreateEventStore = $dropAndCreateEventStore;
        $this->dropAndCreateReadModel = $dropAndCreateReadModel;
        $this->fileSystem = $fileSystem;
        $this->productsPath = $productsPath;
    }

    /**
     * @BeforeScenario
     */
    public function before($event): void
    {
        $this->dropAndCreateEventStore->execute();
        $this->dropAndCreateReadModel->execute();
    }

    /**
     * @Given the JSON response should match:
     */
    public function theJSONResponseShouldMatch(PyStringNode $jsonPattern): void
    {
        $matcherFactory = new MatcherFactory();
        $matcher = $matcherFactory->createMatcher();

        $content = $this->session->getPage()->getContent();

        if (!$matcher->match($content, (string) $jsonPattern)) {
            throw new RuntimeException($matcher->getError());
        }
    }

    /**
     * @Given There is a Basket with uuid :uuid
     */
    public function thereIsABasketWithUuid($uuid): void
    {
        $basketId = new BasketId($uuid);
        $command = new PickUpBasket($basketId);

        $this->commandBus->dispatch($command);
    }

    /**
     * @Given There are Products
     */
    public function thereAreProducts(TableNode $table): void
    {
        $products = ['items' => $table->getHash()];

        $this->fileSystem->setFileContent($this->productsPath, json_encode($products));
    }

    /**
     * @Given the database is clean
     */
    public function theDatabaseIsClean(): void
    {
    }

    /**
     * @Given there is a User with email :email
     */
    public function thereIsAUserWithEmail($email): void
    {
    }

    /**
     * @Given I am logged as user :email
     */
    public function iAmLoggedAsUser($email): void
    {
    }
}