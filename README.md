<h1 align="center">Throwless PHP</h1>

A PHP implementation of the Result type pattern, inspired by Rust's `Result` and JavaScript's `neverthrow` library. This library provides a type-safe way to handle errors without relying on exceptions, making your code more predictable and easier to reason about.

## Features

- Type-safe error handling without exceptions
- Immutable Result type with `Ok` and `Err` variants
- Comprehensive set of methods for working with Results
- Pure functional programming approach
- PHP 8.2+ with strict typing support

## Installation

```bash
composer require martyrer/throwless-php
```

## Available Methods

- [**Type Checking**](#type-checking)
  - `isOk()` - Check if the Result contains a success value
  - `isErr()` - Check if the Result contains an error value
  - `isOkAnd(callable $fn)` - Check if Result is Ok and the value matches a predicate
  - `isErrAnd(callable $fn)` - Check if Result is Err and the error matches a predicate

- [**Transformation**](#transformation-methods)
  - [`map(callable $fn)`](#map) - Transform the success value
  - [`mapErr(callable $fn)`](#maperr) - Transform the error value
  - [`andThen(callable $fn)`](#andthen) - Chain Result-returning operations

- [**Matching**](#matching)
  - [`match(callable $okFn, callable $errFn)`](#match) - Handle both success and error cases

- [**Inspection**](#inspection-methods)
  - [`inspect(callable $fn)`](#inspect-and-inspecterr) - Inspect the success value
  - [`inspectErr(callable $fn)`](#inspect-and-inspecterr) - Inspect the error value

- [**Unwrapping**](#unwrapping-methods)
  - [`unwrap()`](#unwrapping-values) - Get the success value or throw
  - [`unwrapErr()`](#unwrapping-values) - Get the error value or throw
  - [`unwrapOr(mixed $default)`](#unwrapor) - Get the success value or a default
  - [`unwrapOrElse(callable $fn)`](#unwraporelse) - Get the success value or compute a default
  - [`unwrapOrDefault(DefaultValueProvider $provider)`] - Get the success value or type's default
  - [`expect(string $msg)`](#expect) - Get the success value or throw with custom message
  - [`expectErr(string $msg)`] - Get the error value or throw with custom message
  - [`unwrapUnchecked()`] - Get the success value without checking (unsafe)
  - [`unwrapErrUnchecked()`] - Get the error value without checking (unsafe)

## Basic Usage

```php
use Martyrer\Throwless\Ok;
use Martyrer\Throwless\Err;
use Martyrer\Throwless\Result;

// Creating Results
$success = new Ok(42); // Ok<int, mixed>
$failure = new Err("something went wrong"); // Err<mixed, string>

// Checking Result type
$success->isOk(); // true
$success->isErr(); // false
$failure->isOk(); // false
$failure->isErr(); // true

// Unwrapping values
$success->unwrap(); // 42
$failure->unwrapErr(); // "something went wrong"

// Using default values
$success->unwrapOr(0); // 42
$failure->unwrapOr(0); // 0
```

## Core Methods

### Type Checking

#### Basic Type Checks
Check if a Result contains a success or error value:

```php
$result = new Ok(42);

$result->isOk(); // true
$result->isErr(); // false

$error = new Err("failed");
$error->isOk(); // false
$error->isErr(); // true
```

#### Predicate Checks
Check if a Result matches a condition:

```php
$result = new Ok(42);
$isEven = $result->isOkAnd(fn($x) => $x % 2 === 0); // true
$isNegative = $result->isOkAnd(fn($x) => $x < 0); // false

$error = new Err("invalid input");
$isInputError = $error->isErrAnd(fn($e) => str_contains($e, "input")); // true
$isTimeout = $error->isErrAnd(fn($e) => str_contains($e, "timeout")); // false
```

### Transformation Methods

#### `map`
Transform the success value while preserving the error:

```php
$result = new Ok(5);
$doubled = $result->map(fn($x) => $x * 2); // Ok(10)

$error = new Err("failed");
$doubled = $error->map(fn($x) => $x * 2); // Err("failed")
```

#### `mapErr`
Transform the error value while preserving the success:

```php
$result = new Err("error");
$mapped = $result->mapErr(fn($e) => "Error: " . $e); // Err("Error: error")

$success = new Ok(42);
$mapped = $success->mapErr(fn($e) => "Error: " . $e); // Ok(42)
```

#### `andThen`
Chain operations that might fail:

```php
function divide($x, $y): Result {
    return $y === 0 ? new Err("division by zero") : new Ok($x / $y);
}

$result = new Ok(10)
    ->andThen(fn($x) => divide($x, 2)) // Ok(5)
    ->andThen(fn($x) => divide($x, 0)); // Err("division by zero")
```

### Matching

#### `match`
Handle both success and error cases:

```php
$result = new Ok(42);
$value = $result->match(
    fn($value) => "Success: $value",
    fn($error) => "Error: $error"
); // "Success: 42"
```

### Inspection Methods

#### `inspect` and `inspectErr`
Perform side effects without consuming the Result:

```php
$result = new Ok(42);
$result->inspect(fn($value) => print("Got value: $value"))
       ->map(fn($x) => $x * 2);
```

### Unwrapping Methods

#### `unwrapOr`
Get the value or a default:

```php
$result = new Err("error");
$value = $result->unwrapOr(42); // 42
```

#### `unwrapOrElse`
Get the value or compute a default:

```php
$result = new Err("error");
$value = $result->unwrapOrElse(fn($error) => strlen($error)); // 5
```

#### `expect`
Unwrap with a custom error message:

```php
$result = new Ok(42);
$value = $result->expect("This should never fail"); // 42

$error = new Err("oops");
$value = $error->expect("Critical error"); // throws UnwrapException with message "Critical error"
```

## Real-world Examples

### Example 1: User Authentication

```php
class UserAuthenticator {
    public function authenticate(string $username, string $password): Result {
        $user = $this->findUser($username);
        
        if ($user === null) {
            return new Err("User not found");
        }
        
        return password_verify($password, $user->password)
            ? new Ok($user)
            : new Err("Invalid password");
    }
}

// Usage
$auth = new UserAuthenticator();
$result = $auth->authenticate("john", "password123")
    ->map(fn($user) => $user->toArray())
    ->mapErr(fn($error) => ["error" => $error]);
```

### Example 2: API Response Handling

```php
class ApiClient {
    public function fetchData(string $url): Result {
        try {
            $response = file_get_contents($url);
            $data = json_decode($response, true);
            
            return $data === null 
                ? new Err("Invalid JSON response")
                : new Ok($data);
        } catch (Throwable $e) {
            return new Err($e->getMessage());
        }
    }
}

// Usage
$client = new ApiClient();
$result = $client->fetchData("https://api.example.com/data")
    ->andThen(function($data) {
        return isset($data['items'])
            ? new Ok($data['items'])
            : new Err("Missing items in response");
    })
    ->map(fn($items) => array_map(fn($item) => $item['name'], $items));
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request. Make sure to read the contributing guidelines before submitting your PR.

## License

This project is licensed under the MIT License - see the LICENSE file for details.
