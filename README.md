# PHP Use Case Pattern.

Base classes and interfaces for implementing the Use Case pattern along with entities and entity emitting iterators.

# UseCases

A UseCase is a single class, implementing one 'use case' of the application business logic. It co-ordinates the manipulation of application state to acheive it's purpose.

## Key objectives

#### 1. Be portable

By using only simple objects and primitive types as parameters and return types the use cases of your application are a permanent representation of your business logic and moving to a new language or framework should **not** require any significant recoding.

##### 2. Integrate with framework as a plugin

Where use cases need to interact with external layers (for example to persist changes in state) this should always be acheived by wrapping the framework functionality as a service and injecting it as a dependancy. This ensures that the UseCases can always be moved to a new framework and the interop with the framework is easily assessed, rewritten and replaced with no changes to the UseCase code or tests.

#### 3. Be testable

As UseCases only deal with pure and simple PHP classes they should be 100% testable and so TDD should be the favoured approach to development.

#### 4. Do one thing

A UseCase should be focused on completing one business goal - for example creation of an invoice. Where the action requires a concert of other changes this can be acheived by calling other UserCases.

## Signature of a UseCase

Generally a UseCase has a single method 'execute' which takes arguments and returns a response value. It may also have a constructor through which service dependancies are injected.

```php
class DispatchOrderUseCase extends UseCase
{
  private $emailProvider;
  
  public function __construct(EmailProvider $email)
  {
    $this->emailProvider = $email;    
  }
  
  public function execute(Order $order)
  {
    // ... Do something to despatch the order
    $this->emailProvider->send(new DispatchEmail($order));
  }
}
```

## Calling a UseCase

A UseCase should not be instantiated directly except in unit tests. In production code the static `create()` method ensure dependancies are injected using the DI container.

```php
// Note no mention of the EmailProvider here...
DispatchOrderUseCase::create()->execute($order);
```

# Entities

An entity is a simple POPO (Plain Old PHP Object) with no frills that represents the data passed into and out of UseCases. Generally an entity can be regarded as a model for a business object.

This library defines a base `Entity` class only to allow for basic type recognition (e.g. $arg instanceof Entity)

# EntityEmittingIterator

ORMs generally support representing collections as iterable objects creating objects lazily as required. This is good practice to keep memory usage to the minimum possible. To represent a list we've provided an EntityEmittingIterator that extends the base Iterator PHP interface and can be used to classify parameter and return types.
