{ pkgs, ... }:

{
  languages.javascript = {
    enable = true;
  };

  languages.php = {
    enable = true;
  };


  packages = [
    pkgs.yarn
    pkgs.nodePackages.live-server
  ];

  processes.server.exec = "live-server --port=3000 --host=localhost";

  enterShell = ''
    echo "Website Development Shell - Module 1 - Assignment 1"
  '';
}
