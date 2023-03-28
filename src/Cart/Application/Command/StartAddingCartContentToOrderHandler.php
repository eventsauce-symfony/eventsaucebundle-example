<?php

declare(strict_types=1);

namespace App\Cart\Application\Command;

use App\Cart\Domain\Cart;
use App\Cart\Domain\Checkout\StartAddingCartContentToOrder;
use EventSauce\EventSourcing\Snapshotting\AggregateRootRepositoryWithSnapshotting;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(
    bus: 'commandBus',
    fromTransport: 'normal_priority'
)]
final readonly class StartAddingCartContentToOrderHandler
{
    /**
     * @param AggregateRootRepositoryWithSnapshotting<Cart> $cartRepository
     */
    public function __construct(
        #[Target('cartRepository')] private AggregateRootRepositoryWithSnapshotting $cartRepository
    )
    {
    }

    public function __invoke(StartAddingCartContentToOrder $command): void
    {
        $cart = $this->cartRepository->retrieveFromSnapshot($command->getCartId());

        $cart->startAddingContent($command);

        $this->cartRepository->persist($cart);
        $this->cartRepository->storeSnapshot($cart);
    }
}