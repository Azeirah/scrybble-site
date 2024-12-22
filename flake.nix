{
  description = "Scrybble development environment";

  inputs = {
    nixpkgs.url = "github:NixOS/nixpkgs/nixos-24.11";
    flake-utils.url = "github:numtide/flake-utils";
  };

  outputs = { self, nixpkgs, flake-utils }:
    flake-utils.lib.eachDefaultSystem (system:
      let
        pkgs = nixpkgs.legacyPackages.${system};

        scrybble-php = pkgs.php83.withExtensions ({ enabled, all }:
          enabled ++ [
            all.xdebug
            all.pdo
            all.pdo_mysql
            all.mysqli
            all.mbstring
            all.tokenizer
            all.curl
            all.zip
          ]);

        setup-script = pkgs.writeScriptBin "setup-scrybble" ''
                    #!${pkgs.bash}/bin/bash
                    if [[ ! -d ./vendor ]]; then
                      ${pkgs.gum}/bin/gum format <<EOF
          # This is a new development environment

          You should read the README.md for more information on running Scrybble locally.

          We'll set up your development environment for you.
          EOF
                      if ${pkgs.gum}/bin/gum confirm; then
                        ${pkgs.gum}/bin/gum spin --title "Installing composer packages" -- ${scrybble-php.packages.composer}/bin/composer install
                        cp .env.example .env

                        ${pkgs.gum}/bin/gum spin --show-error --title "Migrating db" -- ./vendor/bin/sail artisan migrate
                        ${pkgs.gum}/bin/gum spin --show-error --title "Installing frontend packages" -- bun install
                        ${pkgs.gum}/bin/gum spin --show-error --title "Starting docker containers" -- sail up -d
                      else
                        exit 1
                      fi
                    else
                      ${scrybble-php.packages.composer}/bin/composer outdated -DM
                    fi
        '';
      in {
        devShells.default = pkgs.mkShell {
          buildInputs = [
            # TODO: Deprecated in favor of bun
            pkgs.nodejs_22
            pkgs.bun
            scrybble-php
            scrybble-php.packages.composer
            setup-script
          ];

          packages = with pkgs; [ gum ];

          WWWGROUP = 1000;
          WWWUSER = 1000;

          shellHook = ''
                        setup-scrybble
                        alias sail="./vendor/bin/sail "
                        ${pkgs.gum}/bin/gum format <<EOF
            # Welcome to the Scrybble development environment :)

            - Use [sail](https://laravel.com/docs/11.x/sail) for interacting with Laravel, docker and composer
            - Use [bun](https://bun.sh/) for the frontend
              - \`bun run dev\` - run frontend server

            EOF
          '';
        };

        packages.rmapi = pkgs.buildGoModule {
          pname = "rmapi";
          version = "0.0.28";

          CGO_ENABLED = 0;
          # strips debug information
          ldflags = [ "-s" "-w" ];

          src = pkgs.fetchFromGitHub {
            owner = "ddvk";
            repo = "rmapi";
            rev =
              "fe71ae7"; # TODO: Should be configurable with a flake command input.
            sha256 = "sha256-GhyZRwsywnFQ4GABbbSOtjVgUuIn5k4iaqPfiyVOAIs=";
          };

          vendorHash = "sha256-5m3/XFyBEWM8UB3WylkBj+QWI5XsnlVD4K3BhKVVCB4=";
        };
      });
}
