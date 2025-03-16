![Favicon - moner.ooo](/img/favicon-152x152.png)

# Revuo Monero Calc

Revuo Monero Calc is a web application for converting Monero (XMR) to various fiat currencies and vice versa. It provides almost real-time exchange rates and supports multiple languages based on the user's browser settings.

This is a fork of the original project by [nice42q](https://github.com/nice42q/moner.ooo) with some modifications, including:

- Improved webpack configuration.
- Support for JS-less operation.
- Improved translations and translation handling.
- Automatic exchange rate updates.
- Automatic update of available currencies.
- Configuration file for customizing the application.

## Table of Contents

- [Features](#features)
- [Usage](#usage)
- [Installation](#installation)
- [Configuration](#configuration)
- [Development](#development)
- [Contributing](#contributing)
- [Acknowledgements](#acknowledgements)

## Features

- Conversion between Monero (XMR) and multiple fiat currencies.
- Multi-language support.
- User-friendly interface.
- Fetches currencies and exchange rates from CoinGecko API.
- Fully functional without JavaScript. Even better with JavaScript enabled.
- Customizable configuration.

## Usage

### Convert XMR to Fiat

To convert XMR to a fiat currency, simply visit:

```
https://calc.revuo-xmr.com/?in=USD
```

Replace `USD` with your preferred currency code. You can also specify the amount of XMR to convert:

```
https://calc.revuo-xmr.com/?in=USD&xmr=1
```

The `xmr` parameter specifies the amount of XMR to convert.

### Convert Fiat to XMR

To convert a fiat currency to XMR, visit:

```
https://calc.revuo-xmr.com/?in=USD&fiat=1&direction=1
```

The `fiat` parameter specifies the amount of fiat currency to convert. The `direction` parameter is set to `1` to indicate conversion from fiat to XMR.

### Use XMR Prices Data in Office Applications

1. Select field A1.
2. Go to `Data` → `Link to external data...`.
3. Input the URL `calc.revuo-xmr.com` and confirm.
4. Confirm the import options and select `HTML_1`.

For an example, see [kuno.anne.media](https://kuno.anne.media/donate/onml/).

## Installation

### Prerequisites

- PHP
- Node.js and npm
- PHP-enabled web server (e.g. Caddy, Nginx, Apache)

### Steps

1. Clone the repository:

    ```sh
    git clone https://github.com/rottenwheel/moner.ooo.git
    cd moner.ooo
    ```

2. Install JavaScript dependencies:

    ```sh
    npm install
    ```

3. Build the project:

    ```sh
    npm run build
    ```

4. Point your web server to the public directory inside this repository.

## Configuration

Create a `config.php` file in the root directory to customize the application. Example:

```php
<?php
return [
    'servers_guru' => false,
    'attribution' => 'Powered by Moner.ooo',
    'preferred_currencies' => ['USD', 'EUR', 'GBP'],
    'github_url' => 'https://git.private.coffee/kumi/moner.ooo/',
    'footer_links' => [
        ['url' => 'https://example.com', 'text' => 'Example Link']
    ],
];
```

Create a `secrets.php` file in the root directory to store CoinGecko API keys. Example:

```php
<?php
return [
	'coingecko_api_key' => 'CG-xxxx',
	'coingecko_key_is_demo' => true,
];
```

**Note:** The `secrets.dist.php` file should not be accessible from the web server.

### Fetching Exchange Rates

Exchange rates are fetched from the CoinGecko API. The `coingecko.php` file handles the API requests and attempts to update exchange rates every 5 seconds. Due to the rate limits of the CoinGecko API, actual update intervals may vary and are closer to 60 seconds.

## Contributing

We welcome contributions! Here’s how you can help:

1. Fork the repository.
2. Create a new branch for your feature or bugfix:

    ```sh
    git checkout -b my-feature-branch
    ```

3. Make your changes.
4. Commit your changes:

    ```sh
    git commit -m "Description of my changes"
    ```

5. Push to the branch:

    ```sh
    git push origin my-feature-branch
    ```

6. Create a pull request.

## Acknowledgements

- [Bootstrap](https://getbootstrap.com/)
- [CoinGecko API](https://www.coingecko.com/en/api)

## License

Revuo Monero Calc is released under GNU General Public License v3.0. See [LICENSE](./LICENSE) for more information.
