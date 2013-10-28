OrkestraWebServiceBundle
========================

Provides basic support for WSSE authentication.

## Installation

The easiest way to add OrkestraWebServiceBundle to your project is using composer.

Add orkestra/webservice-bundle to your `composer.json` file:

``` json
{
    "require": {
        "orkestra/webservice-bundle": "1.0.x-dev"
    }
}
```

Then run `composer install` or `composer update`.


## Configuration

OrkestraWebServiceBundle adds a new entity called Token.

Modify your application security configuration (`security.yml`)

1. Add a plaintext encoder for the Token entity

    *NOTE:* Only plaintext is supported currently. This is a serious flaw, but because of the way the digest is
    generated, no work around exists except to implement some two-way encryption mechanism at the database
    level.

2. Add a new entity provider for the Token entity

3. Add a new firewall with the options: `stateless: true` and `wsse: true`
