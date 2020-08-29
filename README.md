# API plugin to [Question2Answer](https://question2answer.org/)

Creates simple API and serve some data as JSON.

Tested on **Q2A version >= 1.7.0 and PHP >= 7.0**. Code style adjusted to Q2A style.

## Installation

Clone or download this repository or selected [release](https://github.com/awaluk/q2a-api/releases) to *qa-plugin* directory in your Q2A.
 
## Available endpoints

- **/api/favorites** - get favorites data for logged user: users, questions, tags and categories
    ```json
    {
        "questions": ["2", "1"],
        "users": ["Example username"],
        "tags": ["Example tag 1", "Example tag 2"],
        "categories": ["Example category"]
    }
    ```
