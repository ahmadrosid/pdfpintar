## PDFPintar

PDFPintar is an AI web application designed for interacting with PDFs. It's built using Laravel and React.

![demo.png](https://res.cloudinary.com/dr15yjl8w/image/upload/v1696665108/pdfpintar_ycqgqs.png)

## Installation

To run PDFPintar on your own VPS server, follow the [instructions here](server-setup.md). If you need assistance with the setup, feel free to [contact me](mailto:alahmadrosid@gmail.com).

**Local Development**

The easiest way to run this project is by using Docker.

Make sure you have Docker installed and then start it with `docker-compose`:

```bash
docker-compose up -d

```

Next, run the database migration:

```bash
docker-compose exec server php artisan migrate
```

If you are working on the UI, make sure to run Vite dev:

```bash
npm run dev
```

## Contributing

Contributions are welcome. Please open an issue before creating a pull request.

## LICENSE

MIT
