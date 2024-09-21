# Binary
This is the default backend for ![](https://raw.githubusercontent.com/fruithost/Documentation/main/Images/LOGO_TEXT.png) **fruithost**.

**Running a server has never been so easy!**

# Installation
All informations and installations-instructions can be find in the [Documentation](https://github.com/fruithost/Documentation)!

[![INSTALL_NOW]](https://github.com/fruithost/Documentation/tree/main/Installation)

# Support us! ![Sponsors](https://img.shields.io/github/sponsors/fruithost?style=social)
Donations are an important contribution to the development of OpenSource projects. With your donation you can help us to advance our project. Your support enables us to support the programming.

Be a team-player, all feedbacks of our donations will have the priority. We will build the site for **YOU**!

[![PAYPAL]](https://paypal.me/debitdirect) [![PATREON]](https://www.patreon.com/fruithost) [![GITHUB]](https://github.com/sponsors/fruithost)

![BANK]

# Community
[![DISCORD]](https://discord.gg/8pTWckusSC)

# Contributing
Feel free to help us with the developing! 

[![CODE_OF_CONDUCT]](https://github.com/fruithost/Panel/blob/master/.github/CODE_OF_CONDUCT.md)
[![CONTRIBUTING]](https://github.com/fruithost/Panel/blob/master/.github/CONTRIBUTING.md)
[![STYLING_GUIDELINES]](https://fruithost.de/guidelines/styling)

[GITHUB]: https://img.shields.io/badge/GitHub-%24?style=for-the-badge&logo=github&color=%230d1117
[PAYPAL]: https://img.shields.io/badge/PayPal-%24?style=for-the-badge&logo=paypal&color=%23169BD7
[PATREON]: https://img.shields.io/badge/PATREON-%24?style=for-the-badge&logo=patreon&color=%23F96854
[INSTALL_NOW]: https://img.shields.io/badge/Install_Now!-37a779?style=for-the-badge
[CODE_OF_CONDUCT]: https://img.shields.io/badge/Code_of_Conduct-37a779?style=for-the-badge
[CONTRIBUTING]: https://img.shields.io/badge/Contributing-37a779?style=for-the-badge
[STYLING_GUIDELINES]: https://img.shields.io/badge/Styling_Guidelines-37a779?style=for-the-badge
[DISCORD]: https://img.shields.io/badge/Discord-37a779?style=for-the-badge&logo=discord&color=%230d1117
[BANK]: https://github.com/fruithost/Documentation/blob/main/Images/donation_bank.png?raw=true

# Screenshot
![Help](https://raw.githubusercontent.com/fruithost/Documentation/refs/heads/main/Images/Binary/help.png)

## Troubleshooting
If the binary not callable, you must manual add the script to the global scope:

> ln -s /etc/fruithost/bin/fruithost.sh /usr/local/bin/fruithost

If you have permission problems, update the changemod properies:

> chmod 0777 /etc/fruithost/bin/fruithost.sh

## Available Commands
All commands will be inserted as `fruithost <command> <arg1> <arg2> <arg...>`.

<table>
  <thead>
    <tr>
      <td>Binary</td>
      <td>Command</td>
      <td colspan="2">Arguments</td>
      <td>Description</td>
      <td>Preview</td>
    </tr>
  </thead>
  <tbody>
    <tr>
      <th>💥</th>
      <th colspan="4">Globals</th>
      <th>💥</th>
    </tr>
    <tr>
      <td><code>fruithost</code></td>
      <td colspan="3"><code>version</code></td>
      <td>Show the current version</td>
      <td><a href="https://raw.githubusercontent.com/fruithost/Documentation/refs/heads/main/Images/Binary/version.png" target="_blank">🔍 View</a></td>
    </tr>
    <tr>
      <td><code>fruithost</code></td>
      <td colspan="3"><code>help</code></td>
      <td>Show the Help</td>
      <td><a href="https://raw.githubusercontent.com/fruithost/Documentation/refs/heads/main/Images/Binary/help.png" target="_blank">🔍 View</a></td>
    </tr>
    <tr>
      <td><code>fruithost</code></td>
      <td colspan="3"><code>status</code></td>
      <td>Show the status of your system</td>
    </tr>
    <tr>
      <td><code>fruithost</code></td>
      <td colspan="3"><code>statistics</code></td>
      <td>Show some statistics</td>
    </tr>
    <tr>
      <td><code>fruithost</code></td>
      <td colspan="3"><code>daemon</code></td>
      <td>Run the daemon process</td>
       <td><a href="https://raw.githubusercontent.com/fruithost/Documentation/refs/heads/main/Images/Binary/daemon.png" target="_blank">🔍 View</a></td>
    </tr>
    <tr>
      <td colspan="5"></td>  
    </tr>
    <tr>
      <th>🔄</th>
      <th colspan="4">Updates & Upgrades</th>
      <th>🔄</th>
    </tr>
    <tr>
      <td><code>fruithost</code></td>
      <td><code>upgrade</code></td>
      <td colspan="2"><code>core</code></td>
      <td>Upgrade the core files</td>
       <td><a href="https://raw.githubusercontent.com/fruithost/Documentation/refs/heads/main/Images/Binary/core.png" target="_blank">🔍 View</a></td>
    </tr>
    <tr>
      <td><code>fruithost</code></td>
      <td><code>upgrade</code></td>
      <td colspan="2"><code>&lt;module&gt;</code></td>
      <td>Upgrade the given module name</td>
      <td><a href="https://raw.githubusercontent.com/fruithost/Documentation/refs/heads/main/Images/Binary/upgrade.png" target="_blank">🔍 View</a></td>
    </tr>
    <tr>
      <td><code>fruithost</code></td>
      <td colspan="3"><code>update</code></td>
      <td>Check for updates</td>
       <td><a href="https://raw.githubusercontent.com/fruithost/Documentation/refs/heads/main/Images/Binary/update.png" target="_blank">🔍 View</a></td>
    </tr>
    <tr>
      <td colspan="5"></td>  
    </tr>
    <tr>
      <th>⚡️</th>
      <th colspan="4">Modules</th>
      <th>⚡️</th>
    </tr>
    <tr>
      <td><code>fruithost</code></td>
      <td><code>remove</code></td>
      <td colspan="2"><code>&lt;module&gt;</code></td>
      <td>Delete / Deinstall given module</td>
    </tr>
    <tr>
      <td><code>fruithost</code></td>
      <td><code>install</code></td>
      <td colspan="2"><code>&lt;module&gt;</code></td>
      <td>Install given module</td>
    </tr>
    <tr>
      <td><code>fruithost</code></td>
      <td><code>enable</code></td>
      <td colspan="2"><code>&lt;module&gt;</code></td>
      <td>Enable the given module</td>
    </tr>
    <tr>
      <td><code>fruithost</code></td>
      <td><code>disable</code></td>
      <td colspan="2"><code>&lt;module&gt;</code></td>
      <td>Disable the given module</td>
    </tr>
    <tr>
      <td colspan="5"></td>  
    </tr>
    <tr>
      <th>:octocat:</th>
      <th colspan="4">Repositorys</th>
      <th>:octocat:</th>
    </tr>
    <tr>
      <td><code>fruithost</code></td>
      <td><code>repository</code></td>
      <td><code>add</code></td>
      <td><code>&lt;url&gt;</code></td>
      <td>Add a repository URL</td>
       <td><a href="https://raw.githubusercontent.com/fruithost/Documentation/refs/heads/main/Images/Binary/repository_add.png" target="_blank">🔍 View</a></td>
    </tr>
    <tr>
      <td><code>fruithost</code></td>
      <td><code>repository</code></td>
      <td><code>remove</code></td>
      <td><code>&lt;url&gt;</code></td>
      <td>Remove a repository URL</td>
       <td><a href="https://raw.githubusercontent.com/fruithost/Documentation/refs/heads/main/Images/Binary/repository_remove.png" target="_blank">🔍 View</a></td>
    </tr>
    <tr>
      <td><code>fruithost</code></td>
      <td><code>repository</code></td>
      <td colspan="2"><code>list</code></td>
      <td>List all registred repositorys</td>
       <td><a href="https://raw.githubusercontent.com/fruithost/Documentation/refs/heads/main/Images/Binary/repository_list.png" target="_blank">🔍 View</a></td>
    </tr>
  </tbody>
</table>
