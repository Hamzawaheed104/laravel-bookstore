# Laravel Bookstore Setup
Follow the following steps to setup Magento in Ubuntu

- Install Docker desktop from official docker [website](https://www.docker.com/products/docker-desktop/)
- Run ` docker compose up -d ` to setup all containers
- Run ` php artisan key:generate ` inside the main app container to generate application key
- Copy `.env.example` and create `.env` and setup environment variables accordingly
- Run ` php artisan migrate:refresh ` to run all the migrations
- Run ` php artisan db:seed ` to seed the database
- Setup stripe account using the [link](https://dashboard.stripe.com/register)
- Add `Stripe Publishable key`, `Stripe Private Key` and `Stripe Webkhook key` in env file
- Run ` php artisan optimize:clear `
- Run ` php artisan config:cache `
- Run ` php artisan cache:clear `
- Run ` php artisan serve `
- Browse the ` local host ` to use the product