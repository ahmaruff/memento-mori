# Memento-Mori
Web API sederhana untuk CRUD data desa. dibuat dengan Laravel

## Getting Started

#### Clone
  
```bash
git clone https://github.com/ahmaruff/memento-mori
```

#### Install dependencies

```bash
composer install
```

#### Copy .env.example

```bash
cp .env.example .env
```

#### Set `.env` (database,etc)

#### Generate app key
  
```bash
php artisan key:generate
```

#### Run migration

```bash
php artisan migrate
```

#### Run seeder

```bash
php artisan laravolt:indonesia:seed
```

#### Run server

```bash
php artisan serve
```

## API Docs

| Method    | Path              | Params                | Body  | Desc                      |
|---        |---                |---                    |---    |---                        |
| `GET`     | `/api/desa`       | ?page={page_number}   | n/a   | Return all data desa      |
| `GET`     | `/api/desa/{id}`  | n/a    	            | n/a 	| Return single data desa   |
| `POST`	| `/api/desa`     	| n/a    	            | code, district_code, name, lat, long, pos     | Insert new data desa  	|
| `PUT`	| `/api/desa/{id}`     	| n/a    	            | code, district_code, name, lat, long, pos     | Update data desa  	|
| `DELETE`     | `/api/desa/{id}`  | n/a    	            | n/a 	| Delete single data desa   |

## Response Template

All responses should be in JSON format. following [JSend Standard](https://github.com/omniti-labs/jsend). The HTTP code will be 400 (Bad Request) for all errors unless stated otherwise.

```json

{
    "status" : "success|fail|error",
    "code" : "HTTP_STATUS_CODE",
    "message" : "custom message",
    "data" : {
        "data" : "data goes here"
    }
}

```

## Postman Collection

[<img src="https://run.pstmn.io/button.svg" alt="Run In Postman" style="width: 128px; height: 32px;">](https://god.gw.postman.com/run-collection/27401766-d1b5fb19-b9bc-4fc8-931c-c8663e0fa327?action=collection%2Ffork&source=rip_markdown&collection-url=entityId%3D27401766-d1b5fb19-b9bc-4fc8-931c-c8663e0fa327%26entityType%3Dcollection%26workspaceId%3Dc9fa6e11-41c9-4e55-b66b-bfba04df19e7)

## Author

Ahmad Ma'ruf &copy; 2024
