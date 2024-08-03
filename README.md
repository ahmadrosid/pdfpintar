## PDFPintar

PDFPintar is a web application designed for chatting with your PDF documents. It's built using Laravel, Livewire, and a bit of React.

![demo.png](http://res.cloudinary.com/dr15yjl8w/image/upload/v1722672310/public/shc84ttvanftn575crkl.png)

## Requirements

- PHP 8.1+
- Composer
- Node.js and npm
- MySQL or SQLite
- OpenAI API key

## Installation

1. Clone the repository:
   ```
   git clone https://github.com/ahmadrosid/pdfpintar.git
   cd pdfpintar
   ```

2. Install PHP dependencies:
   ```
   composer install
   ```

3. Install JavaScript dependencies:
   ```
   npm install
   ```

4. Copy the `.env.example` file to `.env` and configure your environment variables:
   ```
   cp .env.example .env
   ```
   Make sure to set `OPENAI_API_KEY` and `OPENAI_ORGANIZATION` in your `.env` file.

5. Generate an application key:
   ```
   php artisan key:generate
   ```

6. Run database migrations:
   ```
   php artisan migrate
   ```

7. Build frontend assets:
   ```
   npm run build
   ```

8. Link storage:
   ```
   php artisan storage:link
   ```

9. Start the development server:
   ```
   php artisan serve
   ```

## Environment Variables

Make sure to set the following environment variables in your `.env` file:

```
OPENAI_API_KEY=your_openai_api_key_here
OPENAI_ORGANIZATION=your_openai_organization_id_here
```

Replace `your_openai_api_key_here` with your actual OpenAI API key, and `your_openai_organization_id_here` with your OpenAI organization ID if applicable.

## Contribution

If you want to contribute to this project, I really appreciate it. Here are some things you can do:

1. Report [issues](https://github.com/ahmadrosid/pdfpintar/issues) if you encounter errors or bugs.
1. Submit [pull requests](https://github.com/ahmadrosid/pdfpintar/pulls) for bug fixes, adding new features, or improving documentation.

## LICENSE

MIT