# Scrybble

This is the back-end and front-end for [Scrybble](https://scrybble.ink). It's relatively easy to set-up locally, but is generally not meant to  be used locally. This project has been open-sourced after being private for a while and was not designed with open-source in mind initially.

The biggest issue is that locally you cannot process .rm files with remarks because it is in a docker container. Locally this project is run with docker-compose, the server runs on AWS beanstalk without docker.

The problem is that the remarks package is used as a docker-container on live. If you run docker locally you cannot execute another docker container from within docker. You can either

1. Figure out how docker-in-docker works
2. Use this as a downloading interface and run it manually
3. Change the code so that remarks is hosted as a docker service to talk to instead
4. Figure out how to run this without docker locally

I'm not going to focus on making this possible myself. The code is mostly here for inspection or tinkering.

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

# The binary can then be found at `$HOME/go/bin/rmapi`
# you can copy it over to `binaries/rmapi` to update it.
```

## Remarks

Remarks is the library we use to stitch together pdfs and .rm files. It's what turns a downloaded zip from the RMapi
into an annotated pdf, or a bunch of markdown.

I host a dockerfile at hub.docker.com, tagged `laauurraaa/remarks-bin:latest`.

`docker run -v "$PWD/YOUR FOLDER WITH REMARKABLE STUFF/":/store laauurraaa/remarks-bin:0.2.1 /store/in/v3_coords_notebook /store/out`

See the file `remarks.version` in the root directory for the most recent remarkable version.

This command runs remarks over a test file in the remarks project from the root of rm-notesync.
