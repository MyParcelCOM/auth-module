# MyParcel.com Auth Module
Shared library with Authentication modules for 

## Installation
The library uses Docker to run php and composer. To install Docker, follow the steps in the [documentation](https://docs.myparcel.com/development/#docker).

### Setup
To setup the project, run:
```bash
./mp.sh setup
```

## Commands
The following commands are available for development:

`./mp.sh composer <args>` - Run composer inside the container.

`./mp.sh php <args>` - Run any command on the php container.

`./mp.sh test <args>` - Run the PHPUnit tests.

A few composer scripts have been defined, you can call these using the following commands:

`./mp.sh composer check-style` - Check if the code is PSR-2 compliant.

`./mp.sh composer fix-style` - Automatically fix non-PSR-2 code (not all errors can be automatically fixed).

## License
All software by MyParcel.com is licensed under the [MyParcel.com general terms and conditions](https://www.myparcel.com/terms). 
