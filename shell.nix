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
      "$PROJECT_ROOT/Build/Scripts/cgl.sh"
    '';
  };

  projectCglFix = pkgs.writeShellApplication {
    name = "project-cgl-fix";

    runtimeInputs = [
      composer
      php
    ];

    text = ''
      "$PROJECT_ROOT/Build/Scripts/cgl.sh" --fix
    '';
  };

  projectLintPhp = pkgs.writeShellApplication {
    name = "project-lint-php";

    runtimeInputs = [
      composer
      php
    ];

    text = ''
      "$PROJECT_ROOT/Build/Scripts/lint-php.sh"
    '';
  };

  projectLintTypos = pkgs.writeShellApplication {
    name = "project-lint-typos";

    runtimeInputs = [
      pkgs.typos
    ];

    text = ''
      "$PROJECT_ROOT/Build/Scripts/lint-typos.sh"
    '';
  };

  projectLintTyposFix = pkgs.writeShellApplication {
    name = "project-lint-typos-fix";

    runtimeInputs = [
      pkgs.typos
    ];

    text = ''
      "$PROJECT_ROOT/Build/Scripts/lint-typos.sh" --fix
    '';
  };

  projectLintTypoScript = pkgs.writeShellApplication {
    name = "project-lint-typoscript";

    runtimeInputs = [
      php
    ];

    text = ''
      "$PROJECT_ROOT/Build/Scripts/lint-typoscript.sh"
    '';
  };

  projectLintXml = pkgs.writeShellApplication {
    name = "project-lint-xml";

    runtimeInputs = [
      pkgs.libxml2
    ];

    text = ''
      "$PROJECT_ROOT/Build/Scripts/lint-xml.sh"
    '';
  };

  projectLintYaml = pkgs.writeShellApplication {
    name = "project-lint-yaml";

    runtimeInputs = [
      pkgs.yamllint
    ];

    text = ''
      "$PROJECT_ROOT/Build/Scripts/lint-yaml.sh"
    '';
  };

  projectLint = pkgs.writeShellApplication {
    name = "project-lint";

    runtimeInputs = [
      projectLintPhp
      projectLintTypos
      projectLintTypoScript
      projectLintXml
      projectLintYaml
    ];

    text = ''
      project-lint-php
      project-lint-typos
      project-lint-typoscript
      project-lint-xml
      project-lint-yaml
    '';
  };

  projectPhpstan = pkgs.writeShellApplication {
    name = "project-phpstan";

    runtimeInputs = [
      composer
      php
    ];

    text = ''
      "$PROJECT_ROOT/Build/Scripts/phpstan.sh"
    '';
  };

  projectTestUnit = pkgs.writeShellApplication {
    name = "project-test-unit";
    runtimeInputs = [
      composer
      php
    ];
    text = ''
      "$PROJECT_ROOT/Build/Scripts/phpunit.sh" unit
    '';
  };

  projectTestFunctional = pkgs.writeShellApplication {
    name = "project-test-functional";
    runtimeInputs = [
      composer
      php
    ];
    text = ''
      "$PROJECT_ROOT/Build/Scripts/phpunit.sh" functional
    '';
  };

  projectTestFunctionalMariadb = pkgs.writeShellApplication {
    name = "project-test-functional-mariadb";
    runtimeInputs = [
      composer
      php
      pkgs.mariadb
    ];
    text = ''
      MARIADB_DIR="$(mktemp -d)"
      MARIADB_PORT="''${MARIADB_PORT:-33306}"
      MARIADB_SOCKET="$MARIADB_DIR/mariadb.sock"

      stopMariadb() {
        mariadb-admin --no-defaults --socket="$MARIADB_SOCKET" --user=root shutdown 2> /dev/null || true
        rm -rf "$MARIADB_DIR"
      }
      trap stopMariadb EXIT

      mariadb-install-db \
        --no-defaults \
        --auth-root-authentication-method=normal \
        --datadir="$MARIADB_DIR/data" \
        > "$MARIADB_DIR/install.log" 2>&1

      mariadbd \
        --no-defaults \
        --datadir="$MARIADB_DIR/data" \
        --socket="$MARIADB_SOCKET" \
        --pid-file="$MARIADB_DIR/mariadb.pid" \
        --port="$MARIADB_PORT" \
        --bind-address=127.0.0.1 \
        > "$MARIADB_DIR/mariadb.log" 2>&1 &

      mariadbIsReady() {
        mariadb-admin --no-defaults --socket="$MARIADB_SOCKET" --user=root ping > /dev/null 2>&1
      }
      for _ in $(seq 1 30); do
        mariadbIsReady && break
        sleep 1
      done
      if ! mariadbIsReady; then
        echo "MariaDB did not start:" >&2
        cat "$MARIADB_DIR/mariadb.log" >&2
        exit 1
      fi

      typo3DatabaseDriver=pdo_mysql \
      typo3DatabaseHost=127.0.0.1 \
      typo3DatabasePort="$MARIADB_PORT" \
      typo3DatabaseUsername=root \
      typo3DatabasePassword="" \
      typo3DatabaseName=cart_test \
        "$PROJECT_ROOT/Build/Scripts/phpunit.sh" functional "$@"
    '';
  };

in pkgs.mkShell {
  name = "TYPO3 Extension cart";
  buildInputs = [
    pkgs.libxml2
    pkgs.typos
    pkgs.yamllint
    php
    composer
    projectInstall
    projectCgl
    projectCglFix
    projectLint
    projectLintPhp
    projectLintTypos
    projectLintTyposFix
    projectLintTypoScript
    projectLintXml
    projectLintYaml
    projectPhpstan
    projectTestUnit
    projectTestFunctional
    projectTestFunctionalMariadb
  ];

  shellHook = ''
    export PROJECT_ROOT="$(pwd)"

    export typo3DatabaseDriver=pdo_sqlite
  '';
}
