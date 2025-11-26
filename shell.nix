{
  pkgs ? import <nixpkgs> { }
  ,phpPkgs ? import (fetchTarball "https://github.com/piotrkwiecinski/nixpkgs/archive/1c614d75004b9eb1ecda6ddeb959c4f544403de5.tar.gz") {}
  ,phpVersion ? "php82"
}:

let
  php = phpPkgs.${phpVersion}.buildEnv {
    extensions = { enabled, all }: enabled ++ (with all; [
      xdebug
    ]);

    extraConfig = ''
      xdebug.mode = debug
      memory_limit = 4G
    '';
  };
  inherit(phpPkgs."${phpVersion}Packages") composer;

  projectInstall = pkgs.writeShellApplication {
    name = "project-install";
    runtimeInputs = [
      php
      composer
    ];
    text = ''
      composer update --prefer-dist --no-progress
    '';
  };

  projectCgl = pkgs.writeShellApplication {
    name = "project-cgl";

    runtimeInputs = [
      php
    ];

    text = ''
      ./.build/bin/php-cs-fixer fix --config=Build/.php-cs-fixer.dist.php -v --dry-run --diff
    '';
  };

  projectCglFix = pkgs.writeShellApplication {
    name = "project-cgl-fix";

    runtimeInputs = [
      php
    ];

    text = ''
      ./.build/bin/php-cs-fixer fix --config=Build/.php-cs-fixer.dist.php
    '';
  };

  projectLint = pkgs.writeShellApplication {
    name = "project-lint";

    runtimeInputs = [
      php
    ];

    text = ''
      find ./*.php Classes Configuration Tests -name '*.php' -print0 | xargs -0 -n 1 -P 4 php -l
    '';
  };

  projectPhpstan = pkgs.writeShellApplication {
    name = "project-phpstan";

    runtimeInputs = [
      php
    ];

    text = ''
      ./.build/bin/phpstan analyse -c Build/phpstan.neon --memory-limit 256M
    '';
  };

  projectTestUnit = pkgs.writeShellApplication {
    name = "project-test-unit";
    runtimeInputs = [
      php
      projectInstall
    ];
    text = ''
      project-install
      .build/bin/phpunit -c Build/UnitTests.xml --display-deprecations --display-warnings
    '';
  };

  projectTestFunctional = pkgs.writeShellApplication {
    name = "project-test-functional";
    runtimeInputs = [
      php
      projectInstall
    ];
    text = ''
      project-install
      .build/bin/phpunit -c Build/FunctionalTests.xml --display-deprecations --display-warnings
    '';
  };

in pkgs.mkShell {
  name = "TYPO3 Extension cart";
  buildInputs = [
    php
    composer
    projectInstall
    projectPhpstan
    projectCgl
    projectCglFix
    projectLint
    projectPhpstan
    projectTestUnit
    projectTestFunctional
  ];

  shellHook = ''
    export PROJECT_ROOT="$(pwd)"

    export typo3DatabaseDriver=pdo_sqlite
  '';
}
