{
  pkgs ? import <nixpkgs> { }
  ,phpVersion ? "php82"
}:

let
  php = pkgs.${phpVersion}.buildEnv {
    extensions = { enabled, all }: enabled ++ (with all; [
      xdebug
    ]);

    extraConfig = ''
      xdebug.mode = debug
      memory_limit = 4G
    '';
  };
  inherit(pkgs."${phpVersion}Packages") composer;

  projectInstall = pkgs.writeShellApplication {
    name = "project-install";
    runtimeInputs = [
      composer
      php
    ];
    text = ''
      composer update --prefer-dist --no-progress
    '';
  };

  projectCgl = pkgs.writeShellApplication {
    name = "project-cgl";

    runtimeInputs = [
      composer
      php
    ];

    text = ''
      composer project:cgl
    '';
  };

  projectCglFix = pkgs.writeShellApplication {
    name = "project-cgl-fix";

    runtimeInputs = [
      composer
      php
    ];

    text = ''
      composer project:cgl:fix
    '';
  };

  projectLintPhp = pkgs.writeShellApplication {
    name = "project-lint-php";

    runtimeInputs = [
      composer
      php
    ];

    text = ''
      composer project:lint:php
    '';
  };

  projectPhpstan = pkgs.writeShellApplication {
    name = "project-phpstan";

    runtimeInputs = [
      composer
      php
    ];

    text = ''
      composer project:phpstan
    '';
  };

  projectTestUnit = pkgs.writeShellApplication {
    name = "project-test-unit";
    runtimeInputs = [
      composer
      php
      projectInstall
    ];
    text = ''
      project-install
      composer project:test:unit
    '';
  };

  projectTestFunctional = pkgs.writeShellApplication {
    name = "project-test-functional";
    runtimeInputs = [
      composer
      php
      projectInstall
    ];
    text = ''
      project-install
      composer project:test:functional
    '';
  };

in pkgs.mkShell {
  name = "TYPO3 Extension cart";
  buildInputs = [
    php
    composer
    projectInstall
    projectCgl
    projectCglFix
    projectLintPhp
    projectPhpstan
    projectTestUnit
    projectTestFunctional
  ];

  shellHook = ''
    export PROJECT_ROOT="$(pwd)"

    export typo3DatabaseDriver=pdo_sqlite
  '';
}
