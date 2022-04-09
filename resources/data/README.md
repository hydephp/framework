Info for Hyde Framework package developers:

As of now, this data directory only stores one data file, the filecache.json which contains an index of the default files and their checksums. This file is used as per https://github.com/hydephp/framework/issues/67 to let Hyde know if a default file has been modified by the user or if it is safe to overwrite. Note that the checksums use the md5 hash algorithm which is not cryptographically secure and should not be used for anything other than checking if a file has been modified.

When changing the default files, please make sure to update the checksums in the filecache.json file.
The file is generated automatically by running the filecacheGenerator.php script from the command line in the data directory.

```bash
php filecacheGenerator.php
```