# GetPayIn Task:

- I use Shell script to make the command line prompt more fast for this `chomd +x Name.sh` to make it work

## Start Redis service:

```bash
  ./scripts/Docker.sh
```

## Start Application:

- This will install , migrate,seed and start the served in port 8000 or write your port you need your app run on :
- Git Terminal or any terminal sh runner

```bash
  ./scripts/StartApp.sh
```

## Testing:

1. Run Test for one class

```bash
  php artisan test --filter=Class
```

2. Run Test for one class

```bash
  php artisan test
```

3. Push you development new feature on dev branch after successful test cycle:

```bash
  ./scripts/CI.sh
```

---

## Command :

- Create your service if in `Domain Service (redis,payment,sms,etc..)` or in `Database Service (Repositories Design Pattern : Order,Product,User,etc...)`

1. Database Service:

```bash
php artisan make:service Table --model
```

- Or

```bash
php artisan make:service TableService --model
```

2. Domain service:

```bash
php artisan make:service Name
```

- Or

```bash
php artisan make:service NameService
```
