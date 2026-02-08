<?php

declare(strict_types=1);

use App\Domain\Shared\Entity;

final class ConcreteEntity extends Entity {}

it('retorna o id correto', function () {
    $entity = new ConcreteEntity('abc-123');

    expect($entity->id())->toBe('abc-123');
});

it('é igual a outra entity com mesmo id e tipo', function () {
    $entity1 = new ConcreteEntity('abc-123');
    $entity2 = new ConcreteEntity('abc-123');

    expect($entity1->equals($entity2))->toBeTrue();
});

it('é diferente de outra entity com id diferente', function () {
    $entity1 = new ConcreteEntity('abc-123');
    $entity2 = new ConcreteEntity('xyz-789');

    expect($entity1->equals($entity2))->toBeFalse();
});
