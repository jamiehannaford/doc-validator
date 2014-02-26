doc-validator
=============

Simple scraper for validating nested XML/JSON examples in OpenStack/Rackspace doc files.

## General outline

1. Executes a recursive wget for a given URI. The process follows every link
nested in a given piece of markup until every possible link is exhausted. It
saves each remote URI to the local filesystem. An example location might be:
`./docs/docs.openstack.org`, where `docs.openstack.org` is the root URI you
specified. Only HTML files are saved, any other remote file type is omitted.

2. Once the wget procedure is complete, the PHP script traverses the local
directory recursively, scanning each HTML file for a given regex pattern. The
regular expression indicates how code samples are nested into the markup.

3. Once code samples are extracted, the script judges whether it is either JSON
or XML. It then executes the relevant parsing test for either type.

4. If the parsing fails, it is likely malformed, and generates an error. These
errors can either be output to the console (i.e. STDOUT) or to a local log file.

## CLI options

The main script you will need to run is:

```bash
./bin/doc-validator
```

### Supported CLI flags

Short|Long|Description|Default
---|---|---|---
`-u`|`--uri`|The URI which wget points at|docs.rackspace.com
`-s`|`--skip-wget`|Instructs the script to skip the wget stage and check the local `./docs` version only|Disabled
`-q`|`--quiet-wget`|Instructs the script to silence the wget output|Disabled
`-l`|`--log-file`|Instructs the script to output everything to a provided file. If left empty, the filename will be the same as the root URI|Disabled

### Convenience scripts

To run against docs.openstack.org, run: `./bin/openstack`

To run against docs.rackspace.com, run: `./bin/rackspace`

## Installation

You will need to install this package with Composer:

```bash
# Install Composer executable
curl -sS https://getcomposer.org/installer | php

# Install package
php composer.phar require jamiehannaford/doc-validator:1.0
```

You can then access the binary:

```bash
./vendor/bin/doc-validator -udocs.foo.com -q -l < /dev/null &
```

Because you have left the `--log-file` flag empty, it will log everything to a
default location:

```
./vendor/jamiehannaford/doc-validator/log/docs.foo.com.log
```