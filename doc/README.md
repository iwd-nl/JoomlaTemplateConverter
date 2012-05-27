This tool assists you with the migration of your Joomla 1.5.XX template to Joomla 1.6 / 1.7 / 2.5.
Please understand that, although we automated a lot, it is still sensitive for abnormalities in the PHP files. Also
know that **we cannot be held accountable** if an error occurs after using this tool.

- - -
# Frequently asked questions

## How to get started
Getting started with this utility is simple. Just upload this to your host (or *AMP) installation and change
`src/IntoWebDevelopment/Converter/config.php` if needed. After you've uploaded the files please download the template
from your Joomla installation and place it inside `src/Template`.

Then go to your browser (or terminal) and run `convert.php`. When the
`All templates (ja_iris) are successfully migrated to Joomla 2.5` message apears you can archive it into a ZIP file
and install it inside Joomla.

## Why are XMLWriter and SimpleXML required?
The XMLWriter extension is required because we have to create a new templateDetails.xml for your new template. If you
see something like `Class XMLWriter / SimpleXML not found in ........` then your host doesn't have support for them.
Normally these extensions are enabled by default, so you could try a L/W/MAMP installation on your own computer to run
this tool.

## Why am I seeing the 'The template is already converted' error?
Because a file named `.converted` exists in your template folder. This means that you already ran the template migrator
before. You can safely remove `.converted` and hit convert.php again.
- - -

If you have any questions, ideas or suggestions please contact us at info@intowebdevelopment.nl