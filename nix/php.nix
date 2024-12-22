{ pkgs }: {
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
}
