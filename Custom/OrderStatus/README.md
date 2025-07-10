# Custom Order Status Module for Magento 2

This module provides a suite of tools for managing and observing order statuses in Magento 2. It includes custom API endpoints to get and update order data, logs all order status changes to a custom database table, and sends an email notification to customers when their order is shipped.

-----

## Features

  * **POST API Endpoint**: Update an order's status using its increment ID (`/V1/orderstatus`).
  * **Status Change Logger**: Automatically logs every order status change to a dedicated `custom_order_status_log` database table.
  * **Shipment Email Notification**: Triggers a custom email to the customer as soon as a shipment is created for their order.

-----

## How to Install and Run

1.  **Copy the Module**: Place the entire `Custom_OrderStatus` directory into the `app/code/` directory of your Magento 2 installation. Your final path should be `app/code/Custom/OrderStatus`.

2.  **Enable the Module & Deploy**: From your Magento root directory, run the following commands in order:

    ```bash
    # Enable the module and run database schema updates
    php bin/magento setup:upgrade

    # Compile the code and dependency injection configurations
    php bin/magento setup:di:compile

    # Flush the cache to ensure all changes are live
    php bin/magento cache:flush
    ```

3.  **Verify**: The module is now active. You can test its API endpoints and observe its logging and email functionality by managing orders in the Magento Admin panel.

-----

## Architectural Decisions

This module was built following modern Magento 2 best practices to ensure it is performant, scalable, and does not conflict with core functionality.

### 1\. Service Contracts for API

  * **What**: The API endpoint (POST) is defined in `etc/webapi.xml` and point to service interfaces (`Api/...Interface.php`).
  * **Why**: This decouples the business logic from the web-facing layer. It allows other modules to easily use and extend our module's functionality and ensures that the API remains stable even if the underlying business logic is changed. This is the standard, recommended approach for building APIs in Magento 2.

### 2\. Event/Observer Pattern for Extending Core Behavior

  * **What**: We use observers defined in `etc/events.xml` to hook into core Magento events like `sales_order_save_after` and `sales_order_shipment_save_after`.
  * **Why**: This is a non-invasive method for adding custom functionality. Instead of overriding or modifying core Magento classes (which makes future upgrades difficult), we listen for events and execute our custom code separately. This ensures high compatibility and maintainability.

### 3\. Repository Pattern for Database Operations

  * **What**: For writing to our custom `custom_order_status_log` table, we created a full repository (`Api/StatusLogRepositoryInterface.php`, `Model/StatusLogRepository.php`) complete with its own Model, Resource Model, and Collection.
  * **Why**: This abstracts all database interactions into a clean, reusable service class. It prevents direct SQL queries in our business logic (observers), which improves security, performance, and code readability. It also aligns the entire module with Magento's service contract philosophy.

### 4\. Declarative Schema for Custom Database Table

  * **What**: The custom database table is defined in `etc/db_schema.xml`.
  * **Why**: This is the modern, declarative approach for managing database schema in Magento 2. It allows Magento to handle table creation and updates in a more efficient and predictable way compared to older `InstallSchema.php` scripts.

### 5\. Dependency Injection

  * **What**: Throughout the module, all class dependencies are requested through the `__construct()` method.
  * **Why**: This is a core principle of Magento 2 architecture. It promotes loose coupling between classes, making them easier to manage, test, and substitute with custom implementations via `etc/di.xml`.
