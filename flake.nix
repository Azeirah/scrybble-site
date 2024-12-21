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

        scrybble-php83 = pkgs.php83.withExtensions ({enabled, all}: enabled ++ [ all.xdebug ]);

        rmapiBuild = pkgs.buildGoModule {
          pname = "rmapi";
          version = "0.0.25";

          CGO_ENABLED = 0;
          # strips debug information
          ldflags = [ "-s" "-w" ];

          src = pkgs.fetchFromGitHub {
            owner = "ddvk";
            repo = "rmapi";
            rev = "master"; # TODO: Should be configurable with a flake command input.
            sha256 = "sha256-GhyZRwsywnFQ4GABbbSOtjVgUuIn5k4iaqPfiyVOAIs=";
          };

          vendorHash = "sha256-5m3/XFyBEWM8UB3WylkBj+QWI5XsnlVD4K3BhKVVCB4=";
        };
      in
      {
        devShells.default = pkgs.mkShell {
          buildInputs = [
            pkgs.nodejs_22
            pkgs.bun
            scrybble-php83
            scrybble-php83.packages.composer
          ];

          shellHook = ''
            echo "Hello, welcome to the Scrybble Development environment :)"
            if [[ ! -d ./vendor ]]; then
              composer install
            fi

            composer outdated -DM
          '';
        };

        packages.default = pkgs.stdenv.mkDerivation {
          pname = "rmapi-bundle";
          version = "0.0.25";

          buildInputs = [ rmapiBuild ];

          dontUnpack = true;

          installPhase = ''
            mkdir -p $out/binaries
            cp ${rmapiBuild}/bin/rmapi $out/binaries/rmapi
            chmod 755 $out/binaries/rmapi
          '';
        };
      }
    );
}
