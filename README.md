# Scrybble

This is the back-end and front-end for [Scrybble](https://scrybble.ink). It's relatively easy to set-up locally, but does require some technical knowledge.

## Pre-requisites

To install and use the scrybble site, make sure you have the following installed on your system before starting with the set-up section.

1. Everything assumes you're running on a Linux OS. Windows and MacOS are not officially supported.
2. [Docker and docker compose](https://docs.docker.com/engine/install/ubuntu/)
3. Git - `sudo apt install git`
4. [Composer](https://getcomposer.org/download/)

Optional:

1. Python3 if you want to run remarks as a standalone script
2. Go if you want to compile rmapi from scratch to use a customized build

## Set-up local dev environment

We use Laravel sail for development.

```sh
# this may give errors after installing, we only need the sail package to install correctly
composer install --ignore-platform-reqs

alias sail="./vendor/bin/sail"
sail up -d
sail composer install

cp .env.example .env
# The following command may fail initially.
# Wait up to a minute and try again if it's your first time setting up
sail artisan migrate
sail npm install
sail npm run dev
```

After running the last step, the site will be accessible on your [localhost](http://localhost)

## Live env

You can find a hosted version of scrybble at [scrybble.ink](https://scrybble.ink)

## RMapi

We use https://github.com/ddvk/rmapi as a direct dependency for this project. This is used to authenticate and sync
with the ReMarkable API.

Current build is not the release build, but a self-built version of the master branch. This is how to build the binary
with compatibility for our dev docker image as well as our host environment.

```sh
# make sure go is installed

git clone https://github.com/ddvk/rmapi
cd rmapi
CGO_ENABLED=0 go build
```

## Remarks

Remarks is the library we use to stitch together pdfs and .rm files. It's what turns a downloaded zip from the RMapi
into an annotated pdf, or a bunch of markdown.

I host a dockerfile at hub.docker.com, tagged `laauurraaa/remarks-bin:latest`.

`docker run -v "$PWD/YOUR FOLDER WITH REMARKABLE STUFF/":/store laauurraaa/remarks-bin:0.2.1 /store/in/v3_coords_notebook /store/out`

See the file `remarks.version` in the root directory for the most recent remarkable version.

This command runs remarks over a test file in the remarks project from the root of rm-notesync.
