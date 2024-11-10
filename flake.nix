{
  description = "A basic Go project";

  inputs = {
    nixpkgs.url = "github:NixOS/nixpkgs/nixos-24-05";
    flake-utils.url = "github:numtide/flake-utils";
  };

  outputs = { self, nixpkgs, flake-utils }:
    flake-utils.lib.eachDefaultSystem (system:
      let
        pkgs = nixpkgs.legacyPackages.${system};
      in
      {
        devShells.default = pkgs.mkShell {
          buildInputs = with pkgs; [
            go
            gopls
            go-tools
            golangci-lint
          ];
        };

        packages.default = pkgs.buildGoModule {
          pname = "my-go-project";
          version = "0.1.0";
          src = ./.;
          vendorSha256 = null; # Set this to the actual hash after first build
        };
      }
    );
}
