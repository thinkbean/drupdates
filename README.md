# Drupdates

An executable which reports available Drupal updates for a list of 
configured sites.

![drupdates](images/drupdates.png?raw=true "drupdates console example")

It's just a wrapper around `drush pm-updatestatus`, the objective
 being to gather update info on a group of sites you maintain with a single command.
 
You can specify the output to be `json` for use with other tools.

[Drush](https://github.com/drush-ops/drush) is required to be installed on the
system and an alias defined for each site to be reported on.

## Installation

On OSX you can install drupdates with the following commands.
```
curl -O https://github.com/thinkbean/drupdates/releases/download/0.0.1/drupdates.phar
chmod +x drupdates.phar
mv drupdates.phar /usr/local/bin/drupdates
```
If you run into permission issues, the second command may need to be run with `sudo`.

## Usage

```
# Retrieve all updates for all aliases configured.
drupdates

# Retrieve security updates for all aliases configured.
drupdates --security-only

# Retrieve all updates for only aliases site1.prod and site2.prod
drupdates --aliases=site1.prod,site2.prod

# Retrieve all security updates with output formatted in json
drupdates --security-only --format=json
```

## Config

The first time you run `drupdates`, a config file will be created at
`~/.thinkbean/drupdates.json`.

You will need to update the aliases array to a list of valid drush aliases
available to the system running the command.

Here is an example configuration:
```
{
  "aliases": [
    "site1.prod",
    "site2.prod",
    "site3.prod"
  ]
}
```

## Run the Executable

You'll either need to download, or build/install, but once the `drupdates` executable
is available, simply run it.

```
drupdates
```

You can also run it from source.

```
git clone git@github.com:thinkbean/drupdates.git
cd drupdates
composer install
make run
```

## Build and Install

If you are building the phar file, you will need [Box](https://github.com/box-project/box2)
installed on your system.

Test is box is installed properly by running `box --version`.

Once box is installed, you can run the following commands to build and install the
`drupdates` executable.

```
# Creates dist/drupdates.phar
make build

# Copies dist/drupdates.phar to /usr/local/bin/drupdates
make install

# Deletes dist/drupdates.phar
make clean
```

You can test if drupdates is installed properly by running `drupdates --version`.
