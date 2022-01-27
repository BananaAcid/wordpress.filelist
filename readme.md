# File List - Wordpress/Elementor Plugin

![image](https://user-images.githubusercontent.com/1894723/151361189-dd7c119d-262a-4f3e-a845-466e8236280f.png)


Elementor plugin to generate a file list from an existing folder and rendering `.index.html` files as headers.

Generates pure HTML, you may want to style it, otherwise you get a ordered list with links.

> Watch out for `File List Pro` to be able to list folders and more.

## Installation

1. Click the green `Code` button, then on `Download ZIP`.
    ![image](https://user-images.githubusercontent.com/1894723/151415902-9929197b-c1e2-4cb9-92f5-979053e91b3a.png)
2. In your Wordpress installation, within the admin area, sidebar `Plugins` > `Installed Plugins` > `Add New` and upload the the ZIP from step 1.
3. Create a new `Elementor Page`, use the search and type `File List`, drag that widget to our main area.
4. Configure your path - read the `Recommendations` bwelow !

## HTML hirachy

```
    div.bfl-filelist
        blockquote.readme
        blockquote.readme.debug  / if debug is enabled and and no index is found
        ol
            li
                a
                    span.name
                    span.sep
                    span.size
                    small.debug  / if debug is enabled
            ...

```

**Error (not usable path):**

```
div.bfl-filelist.error
    code

```

## Recommendations

### Use a path in `/wp-content/uploads/your-path`

If you have your files elsewhere, link (symlink/haedlink - what ever your system is and what is enabled in your nginx/apache config) your folder into the uploads folder to have inherit any protection configured on your upload folder.

This also keeps the files in reach for the download link to work.

### protect files with a password

If you need to protect your folder or any specific file or filetype with a password, and you are using apache:

Add a `.htaccess` and a `.htpasswd` to trigger a Basic Auth password dialog when accessing it.

This plugin does nothing special to the files, just genertes the link, so any other plugin protecting files would work (you might need to configure the path-prefix). 

## Changes

#### 1.0.2

- added update (experimental)

#### 1.0.1

- fixed url path being wrong
- added Elementor requirement info
- fixed typos to what was intended
- added more info to the readme

#### 1.0.0

- inital.
