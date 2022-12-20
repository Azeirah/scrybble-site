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

## RMapi

We use https://github.com/juruen/rmapi as a direct dependency for this project. This is used to authenticate and sync
with the ReMarkable API.

Current build is not the release build, but a self-built version of the master branch. This is how to build the binary
with compatibility for our dev docker image as well as our host environment.

```sh
# make sure go is installed

git clone https://github.com/juruen/rmapi
cd rmapi
CGO_ENABLED=0 go install
```

The binary can then be found at `$HOME/go/rmapi`, you can copy it over to `binaries/rmapi` to update it.
