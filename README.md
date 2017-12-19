# Bibtex

Export items in Bibtex format.

## Getting Started


### Prerequisites

The mapping between Omeka's Dublin Core fields and Bibtex can be modified in /mapping.ini file.

The example below means that Dublin Core field "Description" is mapped in Bixtex "description" field.

```
Description=description
```

## Deployment

Simply unzip the archive in /plugins/ folder and rename it "Bibtex".

## Built With

* [Bibtex PHP library](https://github.com/pear/Structures_BibTex) - The Bibtex PHP library used in the plugin.
* [Bibtex website](http://www.bibtex.org/) - Infos about Bibtex implementation.

## Contributing

Please read [CONTRIBUTING.md](https://gist.github.com/PurpleBooth/b24679402957c63ec426) for details on our code of conduct, and the process for submitting pull requests to us.

## Authors

* **Franck Dupont** - *Initial work* - [Limonade and Co](http://limonadeandco.fr/).

## License

This project is licensed under the [GNU GPLv3 License](http://www.gnu.org/licenses/gpl-3.0.txt).