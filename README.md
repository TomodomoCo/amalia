Amalia
======

Website management for the rest of us.

***

Amalia is a content management system, from [Van Patten Media](http://vanpattenmedia.com). Amalia makes it easy for anyone to have a simple website — and be able to make changes to their site's pages through the simple file browser and editing interface. Adding and editing pages is a breeze, and you can also upload images and documents, and organise your site in folders.

**Amalia is a database-less CMS**, so it doesn't need the complexity, maintenance, and expense of a MySQL server, making it possible to run on even many of the most limited of web hosting packages.

## Features

* Simple, elegant file browser for organising pages
* Built-in visual editor for web pages
* Media uploader and editor
* Powerful templates and theme support for web designers
* Keyword and description support
* Potential for extensibility with a plugin architecture
* *No database required* — works on even many of the most limited web hosts

## Limitations

Amalia works really well for running a simple, editable, database-less website (*it really does!*), but you should be aware that in its current state:

* The plugin architecture is unfinished — it has a robust design, but too few 'hooks' in the Core code for many useful plugins to be written
* *BYOT* — Bring Your Own Themes/Templates — at the current time, you will need to provide your own themes and templates, or your Amalia website will look a little spartan. We have [documentation to help](https://github.com/vanpattenmedia/amalia/wiki/Themes "See the Themes documentation on the wiki") with that.
* Your web server needs to meet the [system requirements](https://github.com/vanpattenmedia/amalia/wiki/System-Requirements).
* You need the ability to place files outside the DocumentRoot for security reasons (or at the *very least*, use `.htaccess` or similar to lock down the Users Path). See [Installation](https://github.com/vanpattenmedia/amalia/wiki/Installation "See Installation help on the wiki") for more information. This is **vital** for a secure installation.

We would love your contributions to this open source project — in Core code, plugins, default themes/templates, ideas and feedback. Perhaps we will, in time, be able to remove some of these limitations from the list!

Amalia was originally designed and developed by [Chris Van Patten](http://chrisvanpatten.com), [Peter Upfold](http://peter.upfold.org.uk/) and [Nick Sampsell](http://nicksampsell.com/), and was inspired by 'legacy Amalia', written by [Jacob Peddicord](http://jacob.peddicord.net/).

The project is released under [this licence](https://github.com/vanpattenmedia/amalia/raw/master/LICENSE).