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

# The binary can then be found at `$HOME/go/bin/rmapi`
# you can copy it over to `binaries/rmapi` to update it.
```

## Remarks

Remarks is the library we use to stitch together pdfs and .rm files. It's what turns a downloaded zip from the RMapi
into an annotated pdf, or a bunch of markdown.

I host a dockerfile at hub.docker.com, tagged `laauurraaa/remarks-bin:latest`.

The Dockerfile is present in `binaries/remarks.Dockerfile`.

To build and push the Dockerfile to docker hub, you can use the Makefile in the root.
The version is defined in `remarks.version`

`make build-remarks` builds the docker image

`make push-remarks` pushes the image to docker hub

`make update-remarks` does both in succession

You can run remarks through a container with the following command

`docker run -v "$PWD/binaries/remarks/tests/":/store laauurraaa/remarks-bin:0.2.1 /store/in/v3_coords_notebook /store/out`

This command runs remarks over a test file in the remarks project from the root of rm-notesync.

### Updating remarks

Remarks is a git-submodule of this git repository. It is found in `binaries/remarks`.
The docker container is built from the files in the `binaries/remarks` folder and does not utilize git whatsoever.

**The version in remarks.version does not correspond with the remarks package version**

To release a new version of the docker container, follow these steps:

- [ ] Update the version in [`remarks.version` file](./remarks.version)
- [ ] Run `make update-remarks`
- [ ] Modify the `$remarks_version` field in [the php remarks service](./app/Services/RemarksService.php)
