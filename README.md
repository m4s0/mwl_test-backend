# MadeWithLove Test - Backend

#### Application
Application runs with PHP 7.4, Symfony 5, Mysql 5.7, Elasticsearch and Broadway for CQRS+ES.

Application exposes Rest API and runs on http://localhost:80

#### Docker

```
cd docker
```

Build container
```
docker-compose build
```

Run container
```
docker-compose up -d
```

Enter in php container
```
docker-compose run php-fpm bash
```

#### Init Application

Enter in php container
```
docker-compose run php-fpm bash
```

install dependencies
```
make install
``` 

and init application
```
make init
``` 

#### Tests

Application has been designed using Test Driven Development.

There are Unit tests written using PHPUnit and Broadway TestCase,
and Functional tests written using Behat - here `app/features` you can have a look at the BDD tests

Enter in php container
```
docker-compose run php-fpm bash
```

run tests
```
make test
``` 
