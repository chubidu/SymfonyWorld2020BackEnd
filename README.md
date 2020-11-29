# SymfonyWorld2020

Sylius API workshop - initial backend application

## Project setup
1. Install dependencies:
    ```bash
    composer install
    ```

1. Duplicate content of .env to .env.local
    ```bash
    cp .env .env.local
    ```

1. Adjust your configuration in `.env.local`

1. Source the configuration file
    ```bash
    source .env.local
    ```

1. Install JWT:
    ```
    mkdir -p consfig/jwt
    openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096 -pass pass:$JWT_PASSPHRASE
    openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout -passin pass:$JWT_PASSPHRASE
    ```

1. Setup project:
   ```bash
   bin/console sylius:install -s default -n
   ```

### Running application

It is recommended to use Symfony binary to run the project. This library can be found [here](https://symfony.com/download).

Once library is installed, you should install TLS certificate locally
```
symfony server:ca:install
```

Then serve application:
```
symfony serve
```

### Customize configuration
Documentation is available at [docs.sylius.com](http://docs.sylius.com).
