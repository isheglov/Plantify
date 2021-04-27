Smart garden planting planner

Requirements:
`php7.4`
`docker-compose`

How to run on local:
`composer install`
`symfony server:start`
`docker-compose up -d`

Deploy:
`git push heroku master`

Migrations:
`heroku run bash`
`symfony console doctrine:migrations:migrate`

Logs:
`heroku logs`

Encode password:
`symfony console security:encode-password`

Create a user:
`./bin/console doctrine:query:sql "insert into \"user\" values (1,'email@example.com','[\"ROLE\"]','escaped_pass')"`

Get list users:
`./bin/console doctrine:query:sql "select * from \"user\""`
