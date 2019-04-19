# Binary
CLI / Shell backend for fruithost

![Help](https://raw.githubusercontent.com/fruithost/Binary/master/screenshots/help.png)

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
      <th colspan="5">💥 Globals</th>
    </tr>
    <tr>
      <td><code>fruithost</code></td>
      <td colspan="3"><code>version</code></td>
      <td>Show the current version</td>
      <td><a href="https://raw.githubusercontent.com/fruithost/Binary/master/screenshots/version.png" target="_blank">🔍 View</a></td>
    </tr>
    <tr>
      <td><code>fruithost</code></td>
      <td colspan="3"><code>help</code></td>
      <td>Show the Help</td>
      <td><a href="https://raw.githubusercontent.com/fruithost/Binary/master/screenshots/help.png" target="_blank">🔍 View</a></td>
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
       <td><a href="https://raw.githubusercontent.com/fruithost/Binary/master/screenshots/daemon.png" target="_blank">🔍 View</a></td>
    </tr>
    <tr>
      <td colspan="5"></td>  
    </tr>
    <tr>
      <th colspan="5">🔄 Updates & Upgrades</th>  
    </tr>
    <tr>
      <td><code>fruithost</code></td>
      <td><code>upgrade</code></td>
      <td colspan="2"><code>core</code></td>
      <td>Upgrade the core files</td>
       <td><a href="https://raw.githubusercontent.com/fruithost/Binary/master/screenshots/core.png" target="_blank">🔍 View</a></td>
    </tr>
    <tr>
      <td><code>fruithost</code></td>
      <td><code>upgrade</code></td>
      <td colspan="2"><code>&lt;module&gt;</code></td>
      <td>Upgrade the given module name</td>
      <td><a href="https://raw.githubusercontent.com/fruithost/Binary/master/screenshots/upgrade.png" target="_blank">🔍 View</a></td>
    </tr>
    <tr>
      <td><code>fruithost</code></td>
      <td colspan="3"><code>update</code></td>
      <td>Check for updates</td>
       <td><a href="https://raw.githubusercontent.com/fruithost/Binary/master/screenshots/update.png" target="_blank">🔍 View</a></td>
    </tr>
    <tr>
      <td colspan="5"></td>  
    </tr>
    <tr>
      <th colspan="5">⚡️ Modules</th>
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
      <th colspan="5">:octocat: Repositorys</th>  
    </tr>
    <tr>
      <td><code>fruithost</code></td>
      <td><code>repository</code></td>
      <td><code>add</code></td>
      <td><code>&lt;url&gt;</code></td>
      <td>Add a repository URL</td>
       <td><a href="https://raw.githubusercontent.com/fruithost/Binary/master/screenshots/repository_add.png" target="_blank">🔍 View</a></td>
    </tr>
    <tr>
      <td><code>fruithost</code></td>
      <td><code>repository</code></td>
      <td><code>remove</code></td>
      <td><code>&lt;url&gt;</code></td>
      <td>Remove a repository URL</td>
       <td><a href="https://raw.githubusercontent.com/fruithost/Binary/master/screenshots/repository_remove.png" target="_blank">🔍 View</a></td>
    </tr>
    <tr>
      <td><code>fruithost</code></td>
      <td><code>repository</code></td>
      <td colspan="2"><code>list</code></td>
      <td>List all registred repositorys</td>
       <td><a href="https://raw.githubusercontent.com/fruithost/Binary/master/screenshots/repository_list.png" target="_blank">🔍 View</a></td>
    </tr>
  </tbody>
</table>
