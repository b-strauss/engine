# typo3 page localization

## 1. activate languages in backend

Create all needed languages except the default language!

Web -> List -> select the topmost page (page with typo3 symbol) -> create new record
-> Website language -> Fill out the config -> save

To see the language id hover over the flag icon.

## 2. add language to typoscript setup config

```
# default locale
config {
  # locale string for localization files see typo3/sysext/core/Classes/Localization/Locales.php
  language = de
  # html language key
  htmlTag_langKey = de
  # locale for php setlocale("LC_ALL", [value]); see http://php.net/manual/en/function.setlocale.php
  # needed for right date parsing
  locale_all = de_DE
}

# set the locale information for every backend generated data record
[globalVar = GP:L=1]
  config {
    # id from backend generated data record
    sys_language_uid = 1
    language = en
    htmlTag_langKey = en
    locale_all = en_GB
  }
[global]
```

### optional values
```
config {
  # allow only a specific range of language ids
  linkVars = L(0-2)
  # prevent multiple get parameters of the same kind
  uniqueLinkVars = 1
  # set this if you always want to show the locale info in the url, even for the default locale (which is 0)
  defaultGetVars.L = 0
}
```

## 3. configure realUrl
Go to extensions and configure realUrl:

- set "Automatic configuration file format" to: PHP source
- disable "Enable automatic configuration"
- set the path to a configuration file, or if already set create the file

```php
'preVars' => [
  'GETvar' => 'L',
  'valueMap' => [
    'de' => 0,  // German, default locale
    'en' => 1,  // English
  ],
  // use this instead of valueDefault!
  'noMatch' => 'bypass',
]
```