# Proof of concept RPC-API

## How to use

1. Create new contract in /app/contracts

2. Generate actions for api:
```
$ php cli generate
```
3. Create handler for new contract in /app/src/handlers

## Run
```
$ php -S 127.0.0.1:8888 -t ./public >/dev/null 2>&1 &
```