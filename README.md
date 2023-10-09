## PDFPINTAR

pdfpintar is an AI web app to chat with PDFs created using Laravel and React.

![demo.png](https://res.cloudinary.com/dr15yjl8w/image/upload/v1696665108/pdfpintar_ycqgqs.png)

## Installation

If you want to install to your own VPS server you can follow this [instruction](server-setup.md), or you can [contact me](mailto:alahmadrosid@gmail.com) if you need a help to setup on your own VPS.

**Local Development**

The easiest way to run this project is using docker.

After docker installed you can just start docker with `docker-compose`:

```bash
docker-compose up -d
```

Then run database migration:

```bash
docker-compose exec server php artisan migrate
```

## LICENSE

MIT
