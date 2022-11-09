# Scrybble

## Set-up local dev environment

We use Laravel sail for development.

```sh
# this may give errors after installing, we only need the sail package to install correctly
composer install --ignore-platform-reqs

alias sail="./vendor/bin/sail"
sail up -d
sail composer install

cp .env.example .env
sail artisan migrate
sail npm install
sail npm run dev
```

Site is running at [localhost](http://localhost)

## Live env

You can find the real site at [scrybble.ink](https://scrybble.ink)
