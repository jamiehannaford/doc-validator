## Examples

### Validating `docs.openstack.org`

Install the package (as above):

```bash
curl -sS https://getcomposer.org/installer | php
php composer.phar require jamiehannaford/doc-validator:1.0.0
```

Execute the binary:

```bash
./vendor/bin/openstack
```

Check the process is running:

```bash
ps aux | grep php
```

The process takes around 10 minutes to complete; the majority of the time is spent by wget downloading the files. Once this is done, you can view the log file:

```bash
cat ./vendor/jamiehannaford/doc-validator/log/docs.openstack.org.log
```
