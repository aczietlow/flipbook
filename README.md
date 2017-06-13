# Installation

## Dependencies

- [Libraries API 2.x](http://drupal.org/project/libraries)
- [dFlip Flexbook Library](https://codecanyon.net/item/dflip-flipbook-jquery-plugin/15834127)

## Installaion

1. Purchase a valid copy of the Flexbook library (https://codecanyon.net/item/dflip-flipbook-jquery-plugin/15834127).
    - Only 1.7 is currently supported
2. Unzip the file and rename the folder to "flipbook"
3. Put the folder in a libraries directory
    - e.g.: `sites/all/libraries`
4. The following files are required
    - flipbook.js
    - mockup.min.js
    - pdf.min.js
    - pdf.worker.min.js
    - three.min.js
    - sound/turn2.mp3
    - flipbook.css
5. Ensure you have a valid path similar to this one for all files
    - Ex: sites/all/libraries/flipbook/flipbook.js
    
## Implementation

(This is a very work in progress implementation)

Add this to the page containing he flipbook assets being rendered

```
  $flipbook = libraries_load('flipbook');

  $footer_js = 'var dFlipLocation ="/' . $flipbook['library path'] . '";';
  drupal_add_js($footer_js, array('type' => 'inline', 'scope' => 'footer'));
```

Print the following markup to a tpl file.

```
 <div class="thumb _df_button"
       source="<?php print "%source_to_pdf%"; ?>"
       id="df_manual_custom">
    <?php print "%rendered_image_for_thumbail%; ?>
  </div>
```
