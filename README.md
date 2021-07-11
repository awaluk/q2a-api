# API plugin to [Question2Answer](https://question2answer.org/)

Adds simple API to serve some data as JSON.

Tested on **Q2A version >= 1.7.0 and PHP >= 7.0**. Requires [Composer](https://getcomposer.org/) to installation. Code style adjusted to Q2A style.

## Installation

Clone or download this repository or selected [release](https://github.com/awaluk/q2a-api/releases) to *qa-plugin* directory in your Q2A.

Next, run `composer install --no-dev -o` inside plugin directory.
 
## Available endpoints

- User login
- General statistics
- List of categories
- Account data of logged user
- Favourite items of logged user
- …

For details, look to documentation in OpenAPI standard inside `docs/` directory.
