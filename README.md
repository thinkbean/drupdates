# Drupdates

An executable which reports available Drupal security updates for a list of 
configured sites.

[Drush](https://github.com/drush-ops/drush) is required to be installed on the
system and an alias defined for each site to be reported on.

## Usage

```
# Retrieve all updates for all aliases configured.
drupdates

# Retrieve security updates for all aliases configured.
drupdates --security-only

# Retrieve all updates for only aliases site1.prod and site2.prod
drupdates --aliases=site1.prod,site2.prod

# Retrieve all security updates with output formatted in json
drupdates --format=json
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
make run
```

## Build and Install

If you are building the phar file, you will need [Box](https://github.com/box-project/box2)
installed on your system.

Test is box is installed properly by running `box --version`.

Once installed, you can run the following commands to build and install the
`drupdates` executable.

```
composer install
make build
make install
make clean
```

This should create the `phar` file and copy it to `/usr/local/bin/drupdates`.

You can test if drupdates is installed properly by running `drupdates --version`.
