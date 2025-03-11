## Задние 1.
~~~~
SELECT 
    u.id AS ID,
    CONCAT(u.first_name, ' ', u.last_name) AS Name,
    b.author AS Author,
    GROUP_CONCAT(b.name ORDER BY b.name SEPARATOR ', ') AS Books
FROM 
    users u
JOIN 
    user_books ub ON u.id = ub.user_id
JOIN 
    books b ON ub.book_id = b.id
WHERE 
    TIMESTAMPDIFF(YEAR, u.birthday, CURDATE()) BETWEEN 7 AND 17
    AND DATEDIFF(ub.return_date, ub.get_date) <= 14
GROUP BY 
    u.id, b.author
HAVING 
    COUNT(b.id) = 2
ORDER BY 
    u.id;
~~~~


## Задание 2. Инструкция по устанвоке

1. Клонируйте репозиторий
   ```bash
   git clone https://github.com/R3nfre/test_task.git/
   cd test_task
   ```

2. Запустите makefile
   ```bash
   make setup
   ```
    Или запустите Docker Compose
    ```bash
    docker-compose up -d
    ```
    Установите зависимости Laravel
   ```bash
    docker-compose exec app composer install
    ```
## Тестирование

GET http://localhost:8080/api/v1?method=rates&currency=USD,BTC,EUR
Bearer Token: i40B2vyDFuCqXg3zHjYWeH3xf4YlqmSLWucVlEIBfWQ4woVoyVxJ89Q5VkQUWKCY
