# MyParcel.com Auth Module
Shared library with Authentication modules for validating JWT authorization headers and checking its scopes

## Installation
The library uses Docker to run php and composer. To install Docker, follow the steps in the [documentation](https://docs.myparcel.com/development/#docker).

### Setup
To setup the project, run:
```bash
./mp.sh setup
```

### Providers
Add a provider to set the Public Key
```
$this->app->singleton(JwtAuthenticator::class, function () {
    return (new JwtAuthenticator())->setPublicKey(config('auth.public_key'));
});
```

## Commands
The following commands are available for development:

`./mp.sh composer <args>` - Run composer inside the container.

`./mp.sh php <args>` - Run any command on the php container.

`./mp.sh test <args>` - Run the PHPUnit tests.

## License
All software by MyParcel.com is licensed under the [MyParcel.com general terms and conditions](https://www.myparcel.com/terms). 
