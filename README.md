# Drupdates

An executable which reports available Drupal security updates for a list of 
configured sites.

[Drush](https://github.com/drush-ops/drush) is required to be installed on the
system and an alias defined for each site to be reported on.

## Config

Create the following file to define which sites should be checked for updates
when the command is run. The aliases array should be valid drush aliases on 
the system `drupdates` is being used on.

`~/.thinkbean/drupdates.json`
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
make build
make install
make clean
```

This should create the `phar` file and copy it to `/usr/local/bin/drupdates`.

You can test if drupdates is installed properly by running `drupdates --version`.
