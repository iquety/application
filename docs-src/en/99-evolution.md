# Evolving the library

--page-nav--

## 1. Infrastructure

If [Docker](https://www.docker.com/) is installed on your computer, you do not
need to have Composer or PHP installed.

To use Composer and the code quality libraries, use the `./composer` script,
located in the root of this repository. This script is actually a bridge to all
Composer commands, running them through Docker.

## 2. Quality control

### 2.1. Tools

For development, tools for unit testing and static analysis were used.
All configured to the maximum level of demand.

These are the following tools:

- [PHP Unit](https://phpunit.de)
- [PHP Stan](https://phpstan.org)
- [PHP Code Sniffer](https://github.com/squizlabs/PHP_CodeSniffer)
- [PHP MD](https://phpmd.org)

### 2.2. Static analysis

To analyze the implemented code and collect feedback from the tools, use:

```bash
./composer analyse
```

The above command runs all static analysis tools at the same time. If necessary,
they can be carried out individually:

```bash
# Run the Mess Detector
./composer mess
```

```bash
# Run PHP Static Analyzer
./composer stan
```

```bash
# Run Code Sniffer
./composer psr
```

### 2.3. Automated Tests

To run the unit tests, use:

```bash
./composer test
```

## 3. Documentation

Good navigation is essential for documentation to be easy to use. With this in
mind, the tool [Iquety Docmap](https://github.com/iquety/docmap) was used to
generate a pleasant navigation menu on all documentation pages.

Editable documents are located in the `docs-src` directory. After adding or
editing any documents contained there, simply run the command below to generate
browsable documentation in the `docs` directory:

```bash
composer docmap
```

--page-nav--
