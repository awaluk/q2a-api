# API plugin to [Question2Answer](https://question2answer.org/)

Adds simple API to serve some data as JSON.

Tested on **Q2A version >= 1.7.0 and PHP >= 7.0**. Requires [Composer](https://getcomposer.org/) to installation. Code style adjusted to Q2A style.

## Available endpoints

- User login
- General statistics
- List of categories
- Account data of logged user
- Favourite items of logged user
- …

For details, look to documentation in OpenAPI standard inside `docs/` directory.

## Installation

Clone or download this repository or selected [release](https://github.com/awaluk/q2a-api/releases) to *qa-plugin* directory in your Q2A.

Next, run `composer install --no-dev -o` inside plugin directory.

Done! API should be available via `/api` prefix added to your Q2A URL.

## Configuration

Go to Q2A admin panel, open "Plugins" tab (`/admin/plugins`), find "Q2A API" and click "settings". You can configure:
- Value of "Access-Control-Allow-Origin" header

## Development information

- Plugin metadata are store in `metadata.json` and `qa-plugin.php`
- Plugin should work on PHP version 7.0 and higher
- Code should follow to PSR-12 standard, run `composer run phpcs` to check it
