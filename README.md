# CommonMarkBundle
A **Symfony 4 / 5** bundle to easily configure [league/commonmark](https://github.com/thephpleague/commonmark), 
allowing you to set multiple **MarkDown** converters.

![Unit Test Suite](https://github.com/AymDev/CommonMarkBundle/workflows/Unit%20Test%20Suite/badge.svg)
![Bundle installation](https://github.com/AymDev/CommonMarkBundle/workflows/Bundle%20installation/badge.svg)
[![Latest Stable Version](https://poser.pugx.org/aymdev/commonmark-bundle/v)](//packagist.org/packages/aymdev/commonmark-bundle)
[![License](https://poser.pugx.org/aymdev/commonmark-bundle/license)](//packagist.org/packages/aymdev/commonmark-bundle)

 - [Installation](#installation)
 - [Configuration](#configuration)
     - [Converter type](#converter-type)
     - [Converter options](#converter-options)
     - [Converter extensions](#converter-extensions)
 - [Using the converters](#using-the-converters)
     - [As services](#as-services)
     - [In your templates](#in-your-templates)

## Installation
Simply install it with **Composer**, an auto-generated recipe will enable the bundle for you:
```sh
composer require aymdev/commonmark-bundle
```

## Configuration
No converter is created by default. 
Create a **YAML** configuration file at path `config/packages/aymdev_commonmark.yaml` with the following content:
```yaml
aymdev_commonmark:
    converters:
        # add any converter here
        my_custom_converter:
            type: 'github'
            options:
                enable_strong: true
                use_underscore: false
        
        my_other_converter:
            type: 'commonmark'
            extensions:
                - League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension
```

>Note that all keys inside a converter are optional.

### Converter type

The `type` key can be used to choose between a *CommonMark* or a *GitHub* converter:

 - `commonmark` (default): `CommonMarkConverter`
 - `github`: `GithubFlavoredMarkdownConverter`

### Converter options

The `options` key holds the configuration passed to the converter, as an array.
>For more information, see the [CommonMark documentation about Configuration](https://commonmark.thephpleague.com/1.5/configuration/).

### Converter extensions

The `extensions` key allows to add any extension class to a converter.
>Check the complete list of extensions on the [CommonMark documentation](https://commonmark.thephpleague.com/1.5/extensions/overview/).

## Using the converters

### As services
The bundle registers your converters as **services** with the following ID:
```
aymdev_commonmark.converter.CONVERTER_NAME
```

It also creates an alias, so you can get them by *autowiring* using the *converter name* as the *argument name*,
type with the `League\CommonMark\CommonMarkConverter` class.

**Example YAML configuration**:
```yaml
aymdev_commonmark:
    converters:
        # You can add an argument for this converter as:
        #   CommonMarkConverter $myConverter
        my_converter:
```

### In your templates

You can use the `commonmark` **Twig** filter. You only need to pass it a *converter name*:
```twig
{{ markdown_content|commonmark('my_converter') }}
```
>If you have only 1 *converter* you can ommit the *converter name*.
