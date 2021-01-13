# Setup Project

- Copy env
```
cp .env.example .env
php artisan key:generate
php artisan migrate
```
- Update .env and please confirm it.
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=<your db name>
DB_USERNAME=<your mysql user name>
DB_PASSWORD=<your mysql user password>

QUEUE_CONNECTION=database
```

- Build js/css files
```
npm run production
```

# Run Project
```
php artisan serve
```
In another terminal
```
php artisan queue:work
```