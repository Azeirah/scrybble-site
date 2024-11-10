{
  description = "rmapi - ReMarkable Cloud API CLI client";

  inputs = {
    nixpkgs.url = "github:NixOS/nixpkgs/nixos-unstable";
    flake-utils.url = "github:numtide/flake-utils";
  };

  outputs = { self, nixpkgs, flake-utils }:
    flake-utils.lib.eachDefaultSystem (system:
      let
        pkgs = nixpkgs.legacyPackages.${system};
      in
      {
        packages.default = pkgs.buildGoModule {
          pname = "rmapi";
          version = "0.0.25";

          src = pkgs.fetchFromGitHub {
            owner = "ddvk";
            repo = "rmapi";
            rev = "master";
            sha256 = "sha256-GhyZRwsywnFQ4GABbbSOtjVgUuIn5k4iaqPfiyVOAIs=";
          };

          vendorHash = "sha256-5m3/XFyBEWM8UB3WylkBj+QWI5XsnlVD4K3BhKVVCB4=";
        };
      }
    );
}
