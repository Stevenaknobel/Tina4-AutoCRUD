<?php

\Tina4\Get::add("/shopifycloths", function (\Tina4\Response $response) {
    $accessToken = SHOPIFY_ACCESS_TOKEN;
    if (!$accessToken) {
        // This will help debug if the access token is not being read correctly
        echo 'Access Token not found in .env file.';
    }
    $shopUrl = "theme-architecture-steve.myshopify.com";
    $apiVersion = "2025-01";
//print the first 20 products
    $query = <<<GRAPHQL
{
  products(first: 21) {
    edges {
      node {
        id
        title
      }
    }
  }
}
GRAPHQL;

    $url = "https://{$shopUrl}/admin/api/{$apiVersion}/graphql.json";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "X-Shopify-Access-Token: {$accessToken}"
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(["query" => $query]));

    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);
    print_r($data);

    // --- Update Product Details Mutation ---
    $mutation = <<<GRAPHQL
    mutation {
      productUpdate(input: {
        id: "gid://shopify/Product/8996475175158",
        title: "This update works!"
      }) {
        product {
          id
          title
        }
        userErrors {
          field
          message
        }
      }
    }
GRAPHQL;

    // Initialize cURL for the product update
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "X-Shopify-Access-Token: {$accessToken}"
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(["query" => $mutation]));

    $response = curl_exec($ch);
    curl_close($ch);

    // Decode and display the response for the product update
    $updateProductResponse = json_decode($response, true);
    print_r($updateProductResponse);

// --- check inventory and then update levels ---
    // --- Fetch Locations ---
    $locationQuery = <<<GRAPHQL
    {
      locations(first: 10) {
        edges {
          node {
            id
            name
          }
        }
      }
    }
GRAPHQL;

    // cURL request for fetching locations
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "X-Shopify-Access-Token: {$accessToken}"
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(["query" => $locationQuery]));

    $response = curl_exec($ch);
    curl_close($ch);

    $locationData = json_decode($response, true);
    print_r($locationData);  // This will print location information

    // Assume the first location is the one you want to use
    $locationId = $locationData['data']['locations']['edges'][0]['node']['id'];


    $productId = 'gid://shopify/Product/8996475175158';
//options for inventory--available, committed, damaged, incoming, on_hand, quality_control, reserved, safety_stock
    $inventoryQuery = <<<GRAPHQL
    {
      product(id: "{$productId}") {
         id
    title
    variants(first: 5) {
      edges {
        node {
          id
          title
          inventoryItem {
            id
            inventoryLevels(first: 5) {
              edges {
                node {
                  location {
                    id
                    name
                  }
                  quantities(names: ["available"]) {
                  quantity
                  }
                }
              }
            }
          }
        }
      }
    }
  }
}
GRAPHQL;

    // cURL request for fetching inventory levels for the specific product
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "X-Shopify-Access-Token: {$accessToken}"
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(["query" => $inventoryQuery]));

    $response = curl_exec($ch);
    curl_close($ch);

    $inventoryData = json_decode($response, true);
    print_r($inventoryData);  // This will print the inventory details

    // Now that you have the inventory item id, update the inventory level for that product at the location
    // We'll assume you want to set the inventory to 50 for this example
    $inventoryItemId = $inventoryData['data']['product']['variants']['edges'][0]['node']['inventoryItem']['id'];
    $newAvailableQuantity = 50;  // New quantity for inventory

    $updateMutation = <<<GRAPHQL
mutation {
  inventoryAdjustQuantities(input: {
    inventoryItemId: "gid://shopify/Product/8996475175158",
    locationId: "gid://shopify/Location/81703469302",
    availableDelta: 10
  }) {
    inventoryAdjustment {
      id
      availableQuantity
    }
  }
}
GRAPHQL;

    // cURL request for updating the inventory
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "X-Shopify-Access-Token: {$accessToken}"
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(["query" => $updateMutation]));

    $response = curl_exec($ch);
    curl_close($ch);

    $updateResponse = json_decode($response, true);
    print_r($updateResponse);  // This will print the updated inventory level response




// --- Create New Product Mutation ---

// GraphQL Mutation to Create a New Product
    /*    $mutation = <<<GRAPHQL
    mutation {
      productCreate(input: {
        title: "New Product Example",
        vendor: "Example Vendor",
        productType: "Apparel"
      }) {
        product {
          id
          title
          vendor
          productType
        }
        userErrors {
          field
          message
        }
      }
    }
    GRAPHQL;

    // Initialize cURL for creating a new product
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "X-Shopify-Access-Token: {$accessToken}"
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(["query" => $mutation]));

    // Send the mutation request to Shopify
    $response = curl_exec($ch);
    curl_close($ch);

    // Decode and display the response for the product creation
    $createProductResponse = json_decode($response, true);
    print_r($createProductResponse);
    */


    /*
     --- GraphQL Mutation to Delete a Product ---
    $mutation = <<<GRAPHQL
    mutation {
//      productDelete(input: {id: "gid://shopify/Product/8995707355382"}) {
//        deletedProductId
//        userErrors {
//          field
          message
        }
      }
    }
    GRAPHQL;

    // Initialize cURL for deleting the product
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "X-Shopify-Access-Token: {$accessToken}"
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(["query" => $mutation]));

    $response = curl_exec($ch);
    curl_close($ch);

    // Decode and display the response for the product deletion
    $deleteProductResponse = json_decode($response, true);
    print_r($deleteProductResponse);

     */
});