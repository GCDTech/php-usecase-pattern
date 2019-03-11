# php-usecase-pattern

Base classes and interfaces for implementing the Use Case pattern along with entities and entity emitting iterators.

## What is a UseCase?

A UseCase is a single class, implementing one 'use case' of the application business logic. It co-ordinates the manipulation of application state to acheive it's purpose.

## Key objectives

### 1. Be portable

By using only simple objects and primitive types as parameters and return types the use cases of your application are a permanent representation of your business logic and moving to a new language or framework should **not** require any significant recoding.

### 2. Integrate with framework as a plugin

