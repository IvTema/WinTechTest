# WinTechTest

## Task: Wallet Management API

### Requirements:
Implement API methods for managing user wallets with the following constraints:
- Each user can have only one wallet.
- Supported currencies: USD and RUB.
- When changing the wallet with a currency different from the wallet currency, the amount should be converted based on the exchange rate. Exchange rates are updated periodically.
- All wallet changes should be recorded in the database.

### Method for Changing Balance:
#### Mandatory Parameters:
- Wallet ID (e.g., 241, 242)
- Transaction type (debit or credit)
- Amount to change the balance
- Currency of the amount (acceptable values: USD, RUB)
- Reason for the balance change (e.g., stock, refund). The list of reasons is fixed.

### Method for Retrieving Current Balance:
#### Mandatory Parameters:
- Wallet ID (e.g., 241, 242)

### SQL Query:
Write an SQL query that returns the sum received due to a refund in the last 7 days.

### Technical Requirements:
- Server-side logic should be written in PHP version >=7.0
- A relational database management system (RDBMS) should be used for data storage.
- Deployment instructions should be provided.

### Assumptions:
- The choice of additional technologies is not limited.
- All disputed issues in the task can and should be resolved by the Executor.



## Launch:

1. Free up ports 80, 443, 3306, 8081.
2. Install the Make package: `sudo apt install make`.
3. Start Docker.
4. Execute the command `make local-run-seed` in the directory `/Laravel/WinTechTest`.
5. Wait for the deployment to complete.

## Method Descriptions:

1. **Obtaining an Authorization Token:** Method uses login and password from the seed: `admin@admin.ru` and `password`. The authorization token obtained here will be used in subsequent methods as a Bearer Token. Replace `#PLACE FOR TOKEN#` with the actual token in the following methods.
   
    ```bash
    curl --location --request POST 'localhost/api/v1/login?email=admin%40admin.ru&password=password' \
    --header 'Accept: application/json'
    ```

2. **Retrieving Wallet Balance by ID:**

    ```bash
    curl --location 'localhost/api/v1/balance/info/?id=2' \
    --header 'Accept: application/json' \
    --header 'Authorization: Bearer #PLACE FOR TOKEN#'
    ```

3. **Changing Balance and Recording Transactions:**

    ```bash
    curl --location 'localhost/api/v1/balance/update/?transaction=debit&currency=rub&amount=96&issue=renunciation&id=2' \
    --header 'Accept: application/json' \
    --header 'Authorization: Bearer #PLACE FOR TOKEN#'
    ```


### SQL Query:

```sql
SELECT SUM(amount) AS total_refund_amount
FROM transactions
WHERE issue = 'refund'
AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
LIMIT 500;
```
 ----------------
**Project includes phpMyAdmin, accessible via URL: [http://localhost:8081/](http://localhost:8081/)**
- Login: sail
- Password: 0000
