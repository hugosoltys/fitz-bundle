Fitz Bundle
=

Pick what you want in your Symfony application.

Installation
=
Open a terminal and type
```bash
$ composer require hugosoltys/fitz-bundle
```

Configuration
=
Import the bundle routing
```yaml
# config/routes/dev/fitz.yaml
fitz:
  resource: "@FitzBundle/Resources/config/routing.xml"
```

Configuring the bundle
```yaml
# config/packages/dev/fitz.yaml
fitz:
  composer_path: '/path/to/composer'
```