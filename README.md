# GetPayIn Task:

- I use Shell script to make the command line prompt more fast for this `chomd +x Name.sh` to make it work

## Start Redis service:

- I use docker to run the redis service

```bash
  docker-compose -f docker/docker-redis.yml up --build -d
```

## Start Application:

- This will install , migrate,seed and start the served in port 8000 or write your port you need your app run on :
- Git Terminal or any terminal sh runner

```bash
  ./scripts/Docker.sh
  ./scripts/StartApp.sh
```
