# Binary
Binary backend for fruithost

## Available Commands
All commands will be inserted as `fruithost <command> <arg1> <arg2> <arg...>`.

| Binary | Command |  Arguments | | Description |
|--|--|--|--|--|
| **Globals** |  |  |  |  |
| `fruithost` | `version` ||| Show the current version |
| `fruithost` | `help` ||| Open the Help |
| `fruithost` | `status` ||| Show teh status of your system |
| `fruithost` | `statistics` ||| Show some statistics |
| `fruithost` | `daemon` ||| Run the daemon process |
| **Updates & Upgrades** |  |  |  |  |
| `fruithost` | `upgrade` | `core` || Upgrade the core files |
| `fruithost` | `upgrade` | `<module>` || Upgrade the given module name |
| `fruithost` | `update` ||| Check for updates |
| **Modules** |  |  |  |  |
| `fruithost` | `remove` | `<module>` || Delete / Deinstall given module |
| `fruithost` | `install` | `<module>` || Install given module |
| `fruithost` | `enable` | `<module>` || Enable the given module |
| `fruithost` | `disable` | `<module>` || Disable the given module |
| **Repositorys** |  |  |  |  |
| `fruithost` | `repository` | `add` | `<url>` | Add a repository URL |
| `fruithost` | `repository` | `remove` | `<url>` | Remove a repository URL |
| `fruithost` | `repository` | `list` || List all registred repositorys |
