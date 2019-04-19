# Binary
Binary backend for fruithost

## Available Commands
All commands will be inserted as `fruithost <command> <arg1> <arg2> <arg...>`.

| Binary | Command <td colspan=2> Arguments | Description |
|:--------|:--------|:--------|:--------|
| <td colspan=4> **Globals** |
| `fruithost` <td colspan=3> `version` | Show the current version |
| `fruithost` <td colspan=3> `help` | Open the Help |
| `fruithost` <td colspan=3> `status` |Show teh status of your system |
| `fruithost` <td colspan=3> `statistics` | Show some statistics |
| `fruithost` <td colspan=3> `daemon` | Run the daemon process |
| <td colspan=4> **Updates & Upgrades** |
| `fruithost` | `upgrade` <td colspan=2> `core` | Upgrade the core files |
| `fruithost` | `upgrade` <td colspan=2> `<module>` | Upgrade the given module name |
| `fruithost` <td colspan=3> `update` | Check for updates |
| <td colspan=4> **Modules** |
| `fruithost` | `remove` <td colspan=2> `<module>` | Delete / Deinstall given module |
| `fruithost` | `install` <td colspan=2> `<module>` | Install given module |
| `fruithost` | `enable` <td colspan=2> `<module>` | Enable the given module |
| `fruithost` | `disable` <td colspan=2> `<module>` | Disable the given module |
| <td colspan=4> **Repositorys** |
| `fruithost` | `repository` | `add` | `<url>` | Add a repository URL |
| `fruithost` | `repository` | `remove` | `<url>` | Remove a repository URL |
| `fruithost` | `repository` <td colspan=2> `list` | List all registred repositorys |