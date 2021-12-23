# MadeWithLove Test - Backend

#### Application
Application runs with PHP 8.1, Symfony 5.4 LTS, Mysql 8, Elasticsearch and Broadway for CQRS+ES.

Application exposes Rest API and runs on http://localhost:80

#### Docker

Copy `docker/.env.dist` to `docker/.env` and customise it with your parameters

Build container

```
make build
```

Run container

```
make up
```

Stop container

```
make down
```

Remove database container

```
make rm-db
```

Display container logs

```
make logs
```

Enter into php container

```
make bash
```

#### SSH Keys

Generate the SSL keys

```
make generate-keypair
```

#### Init Application

install dependencies

```
make install
```

initialize database

```
make init
``` 

initialize event store

```
make drop-and-create-event_store
``` 

initialize read model

```
make drop-and-create-read_model
``` 

Drop database

```
make drop
```

#### Tests

run all tests

```
make test
```

run unit tests

```
make unit
``` 

run behat tests

```
make behat
``` 

#### Coding Standards

```
make cs
``` 

#### Static Code Analysis

```
make stan
``` 
