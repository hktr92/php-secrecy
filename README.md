# php-secrecy

Secrecy holds your sensitive data as safe as possible from leaking.

It provides safety for following events:
- accidental `__toString()` conversion, e.g. when logging;
- disallows `clone` to keep the data in a single place;
- prevents leaking in `\serialize()`, `var_dump()` and `var_export()`;
- rejects unsafe input from `\unserialize()`;


## Disclaimer

This library **does not** provide any password hashing functionality, nor any sort of in-memory "encryption" of the data.
It only tries its best to prevent leaking the sensitive data in various modes and places.

It's not 100% bullet-proof, since it requires a separate stream to keep the password, which can be accessed at any time.


## Usage

```php
<?php

use Hktr92\Secrecy\Secrecy;
use Hktr92\Secrecy\SecrecyFactory;

/** @var Secrecy $password */
$password = SecrecyFactory::create("my super secret value")->unwrap();

echo "User password is $password"; // outputs "User password is <redacted>"

$pdo = new PDO(...);
$stmt = $pdo->prepare("INSERT INTO `users` SET `email` = ?, `password` = ?");
$stmt->execute(['user@example.com', password_hash($password->expose())]); // works as expected
```

## Major caveat: `var_export()`
Secrecy uses stream resource to hide the secret value from being leaked by `var_export()`.


## How it behaves
This library was built from grounds-up to prevent as much as possible any chaotic behavior of PHP. Thus, the library
**guarantees** that you'll receive either a valid `string` or a `Throwable` in case of error.

The library only panics (e.g. throws an `InvalidArgumentException`) when the constructor 
doesn't receive a valid resource.
