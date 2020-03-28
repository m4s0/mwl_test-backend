Feature: Basket
  In order to buy products
  As a API client
  I need to be able to picks up a new a basket, adds products, updates quantity, removes products and checkout

  Background:
    Given There are Products
      | id | name       | price  | currency |
      | 1  | Smartphone | 299.90 | EUR      |
      | 2  | LCD TV     | 599.90 | EUR      |

  Scenario: Pick up a new Basket
    Given I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    When I send a POST request to "/basket"
    Then the response status code should be 200
    And the JSON response should match:
    """
    {
      "id": @uuid@
    }
    """
    When I send a GET request to "/basket"
    Then the response status code should be 200
    And the JSON response should match:
    """
    [
      {
        "basketId": @uuid@,
        "products": [],
        "removedProducts": [],
        "hasBeenCheckedOut": false,
        "total": {
          "amount": "0",
          "currency": "EUR"
        }
      }
    ]
    """

  Scenario: Add product to Basket
    Given There is a Basket with uuid "8bcf3261-0a19-46b2-ad23-c9f3d4c7e8ef"
    And I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a POST request to "/basket/8bcf3261-0a19-46b2-ad23-c9f3d4c7e8ef/add"
    """
    {
      "productId": 1
    }
    """
    And I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a POST request to "/basket/8bcf3261-0a19-46b2-ad23-c9f3d4c7e8ef/add"
    """
    {
      "productId": 2
    }
    """
    And I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a POST request to "/basket/8bcf3261-0a19-46b2-ad23-c9f3d4c7e8ef/add"
    """
    {
      "productId": 1
    }
    """
    Then the response status code should be 200
    When I send a GET request to "/basket/8bcf3261-0a19-46b2-ad23-c9f3d4c7e8ef"
    Then the response status code should be 200
    And the JSON response should match:
    """
    {
      "basketId": "8bcf3261-0a19-46b2-ad23-c9f3d4c7e8ef",
      "products": [
        {
          "productId": "1",
          "productName": "Smartphone",
          "productPrice": {
            "amount": "29990",
            "currency": "EUR"
          },
          "quantity": 2
        },
        {
          "productId": "2",
          "productName": "LCD TV",
          "productPrice": {
            "amount": "59990",
            "currency": "EUR"
          },
          "quantity": 1
        }
      ],
      "removedProducts": [],
      "hasBeenCheckedOut": false,
      "total": {
        "amount": "119970",
        "currency": "EUR"
      }
    }
    """

  Scenario: Remove product to Basket
    Given There is a Basket with uuid "8bcf3261-0a19-46b2-ad23-c9f3d4c7e8ef"
    And I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a DELETE request to "/basket/8bcf3261-0a19-46b2-ad23-c9f3d4c7e8ef/remove/1"
    And the response status code should be 404
    And I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a POST request to "/basket/8bcf3261-0a19-46b2-ad23-c9f3d4c7e8ef/add"
    """
    {
      "productId": 1
    }
    """
    And I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a DELETE request to "/basket/8bcf3261-0a19-46b2-ad23-c9f3d4c7e8ef/remove/1"
    And the response status code should be 202
    And I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    When I send a GET request to "/basket/8bcf3261-0a19-46b2-ad23-c9f3d4c7e8ef"
    Then the response status code should be 200
    And the JSON response should match:
    """
    {
      "basketId": "8bcf3261-0a19-46b2-ad23-c9f3d4c7e8ef",
      "products": [],
      "removedProducts": [
        {
          "productId": "1",
          "productName": "Smartphone",
          "productPrice": {
            "amount": "29990",
            "currency": "EUR"
          },
          "quantity": 1
        }
      ],
      "hasBeenCheckedOut": false,
      "total": {
        "amount": "0",
        "currency": "EUR"
      }
    }
    """
    And I add "Accept" header equal to "application/json"
    And I add "Content-Type" header equal to "application/json"
    When I send a GET request to "/admin/basket"
    Then the response status code should be 200
    And the JSON response should match:
    """
    [
      {
        "basketId": "8bcf3261-0a19-46b2-ad23-c9f3d4c7e8ef",
        "products": [],
        "removedProducts": [
          {
            "productId": "1",
            "productName": "Smartphone",
            "productPrice": {
              "amount": "29990",
              "currency": "EUR"
            },
            "quantity": 1
          }
        ],
        "hasBeenCheckedOut": false,
        "total": {
          "amount": "0",
          "currency": "EUR"
        }
      }
    ]
    """

  Scenario: Update product to Basket
    Given There is a Basket with uuid "8bcf3261-0a19-46b2-ad23-c9f3d4c7e8ef"
    And I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a POST request to "/basket/8bcf3261-0a19-46b2-ad23-c9f3d4c7e8ef/add"
    """
    {
      "productId": 1,
      "quantity": 2
    }
    """
    And I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a PUT request to "/basket/8bcf3261-0a19-46b2-ad23-c9f3d4c7e8ef/update"
    """
    {
      "productId": 1,
      "quantity": 1
    }
    """
    And the response status code should be 200
    And I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    When I send a GET request to "/basket/8bcf3261-0a19-46b2-ad23-c9f3d4c7e8ef"
    Then the response status code should be 200
    And the JSON response should match:
    """
    {
      "basketId": "8bcf3261-0a19-46b2-ad23-c9f3d4c7e8ef",
      "products": [
        {
          "productId": "1",
          "productName": "Smartphone",
          "productPrice": {
            "amount": "29990",
            "currency": "EUR"
          },
          "quantity": 1
        }
      ],
      "removedProducts": [],
      "hasBeenCheckedOut": false,
      "total": {
        "amount": "29990",
        "currency": "EUR"
      }
    }
    """

  Scenario: Checkout to Basket
    Given There is a Basket with uuid "8bcf3261-0a19-46b2-ad23-c9f3d4c7e8ef"
    And I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a POST request to "/basket/8bcf3261-0a19-46b2-ad23-c9f3d4c7e8ef/add"
    """
    {
      "productId": 1
    }
    """
    And I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a POST request to "/basket/8bcf3261-0a19-46b2-ad23-c9f3d4c7e8ef/add"
    """
    {
      "productId": 2
    }
    """
    And I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a POST request to "/basket/8bcf3261-0a19-46b2-ad23-c9f3d4c7e8ef/add"
    """
    {
      "productId": 1
    }
    """
    And the response status code should be 200
    And I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a POST request to "/basket/8bcf3261-0a19-46b2-ad23-c9f3d4c7e8ef/checkout"
    And the response status code should be 200
    And I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    When I send a GET request to "/basket/8bcf3261-0a19-46b2-ad23-c9f3d4c7e8ef"
    Then the response status code should be 200
    And the JSON response should match:
    """
    {
      "basketId": "8bcf3261-0a19-46b2-ad23-c9f3d4c7e8ef",
      "products": [
        {
          "productId": "1",
          "productName": "Smartphone",
          "productPrice": {
            "amount": "29990",
            "currency": "EUR"
          },
          "quantity": 2
        },
        {
          "productId": "2",
          "productName": "LCD TV",
          "productPrice": {
            "amount": "59990",
            "currency": "EUR"
          },
          "quantity": 1
        }
      ],
      "removedProducts": [],
      "hasBeenCheckedOut": true,
      "total": {
        "amount": "119970",
        "currency": "EUR"
      }
    }
    """