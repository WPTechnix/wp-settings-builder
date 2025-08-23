# Command-Line Scripts

This directory contains all the wrapper scripts used to manage and interact with the project's development environment. These scripts are designed to be run from the project root.

---

## `bin/docker`

This is the primary script for interacting with the Docker environment. It's a smart wrapper around `docker compose` that simplifies container management and command execution.

### Shorthand Container Access

The script's default behavior is to execute commands directly inside the main `app` container. Any command that is not a special management command (listed below) is passed through.

| Command                               | Description                                                               |
| ------------------------------------- | ------------------------------------------------------------------------- |
| `./bin/docker`                        | Open an interactive `bash` shell inside the default `app` container.      |
| `./bin/docker <cmd...>`               | Run any command with its arguments inside the `app` container.            |
| **Example:** `./bin/docker php -v`    | Checks the PHP version inside the container.                              |
| **Example:** `./bin/docker ls -la`    | Lists files in the container's default working directory (`/app`).        |

### Environment Management Commands

These special commands are used to control the Docker Compose stack.

| Command                         | Description                                                               |
| ------------------------------- | ------------------------------------------------------------------------- |
| `up`                            | Start all services defined in `docker-compose.yml` in detached mode.      |
| `down`                          | Stop and remove all containers, networks, and volumes.                    |
| `build [service...]`            | Rebuild and restart services (default: all).                              |
| `restart [service...]`          | Restart one or more services (default: all).                              |
| `logs [service...]`             | Follow log output from one or more services (default: all).               |
| `exec <service> <cmd...>`       | Execute a command in a **specific** service container.                    |
| **Example:** `exec db mysql`    | Opens a MySQL command-line client inside the `db` container.              |

---

## `bin/composer`

This is a dedicated wrapper script for Composer. It simplifies running Composer commands by automatically forwarding them to be executed inside the `app` container.

Instead of typing `./bin/docker composer <command>`, you can simply use:

| Command                                   | Description                                       |
| ----------------------------------------- | ------------------------------------------------- |
| `./bin/composer install`                  | Install all PHP dependencies from `composer.lock`.|
| `./bin/composer update`                   | Update PHP dependencies to their latest versions. |
| `./bin/composer require vendor/package`   | Add a new PHP package to the project.             |
| `./bin/composer remove vendor/package`    | Remove a PHP package from the project.            |

---

## Setup Scripts

These scripts are typically used only during the initial setup of the project.

### `bin/copy`

This script prepares your local environment by copying all necessary configuration files from their templates.

| Command                      | Description                                                  |
| ---------------------------- | ------------------------------------------------------------ |
| `./bin/copy`                 | Copies template files (e.g., `.dist`, `.example`) if the destination does not already exist. |
| `./bin/copy --override`      | Forces the copy, overwriting any existing configuration files. |
