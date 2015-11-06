<?php

/**
 * @file
 * Default theme implementation to display a node.
 * Available variables:
 * - $title: the (sanitized) title of the node.
 * - $content: An array of node items. Use render($content) to print them
 * all,
 *   or print a subset such as render($content['field_example']). Use
 *   hide($content['field_example']) to temporarily suppress the printing of
 *   a
 *   given element.
 * - $user_picture: The node author's picture from user-picture.tpl.php.
 * - $date: Formatted creation date. Preprocess functions can reformat it by
 *   calling format_date() with the desired parameters on the $created
 *   variable.
 * - $name: Themed username of node author output from theme_username().
 * - $node_url: Direct URL of the current node.
 * - $display_submitted: Whether submission information should be displayed.
 * - $submitted: Submission information created from $name and $date during
 *   template_preprocess_node().
 * - $classes: String of classes that can be used to style contextually
 * through CSS. It can be manipulated through the variable $classes_array
 * from preprocess functions. The default values can be one or more of the
 * following:
 *   - node: The current template type; for example, "theming hook".
 *   - node-[type]: The current node type. For example, if the node is a
 *     "Blog entry" it would result in "node-blog". Note that the machine
 *     name will often be in a short form of the human readable label.
 *   - node-teaser: Nodes in teaser form.
 *   - node-preview: Nodes in preview mode.
 *   The following are controlled through the node publishing options.
 *   - node-promoted: Nodes promoted to the front page.
 *   - node-sticky: Nodes ordered above other non-sticky nodes in teaser
 *     listings.
 *   - node-unpublished: Unpublished nodes visible only to administrators.
 * - $title_prefix (array): An array containing additional output populated
 * by
 *   modules, intended to be displayed in front of the main title tag that
 *   appears in the template.
 * - $title_suffix (array): An array containing additional output populated
 * by
 *   modules, intended to be displayed after the main title tag that appears
 *   in the template.
 * Other variables:
 * - $node: Full node object. Contains data that may not be safe.
 * - $type: Node type; for example, story, page, blog, etc.
 * - $comment_count: Number of comments attached to the node.
 * - $uid: User ID of the node author.
 * - $created: Time the node was published formatted in Unix timestamp.
 * - $classes_array: Array of html class attribute values. It is flattened
 *   into a string within the variable $classes.
 * - $zebra: Outputs either "even" or "odd". Useful for zebra striping in
 *   teaser listings.
 * - $id: Position of the node. Increments each time it's output.
 * Node status variables:
 * - $view_mode: View mode; for example, "full", "teaser".
 * - $teaser: Flag for the teaser state (shortcut for $view_mode ==
 * 'teaser').
 * - $page: Flag for the full page state.
 * - $promote: Flag for front page promotion state.
 * - $sticky: Flags for sticky post setting.
 * - $status: Flag for published status.
 * - $comment: State of comment settings for the node.
 * - $readmore: Flags true if the teaser content of the node cannot hold the
 *   main body content.
 * - $is_front: Flags true when presented in the front page.
 * - $logged_in: Flags true when the current user is a logged-in member.
 * - $is_admin: Flags true when the current user is an administrator.
 * Field variables: for each field instance attached to the node a
 * corresponding variable is defined; for example, $node->body becomes $body.
 * When needing to access a field's raw values, developers/themers are
 * strongly encouraged to use these variables. Otherwise they will have to
 * explicitly specify the desired field language; for example,
 * $node->body['en'], thus overriding any language negotiation rule that was
 * previously applied.
 * @see     template_preprocess()
 * @see     template_preprocess_node()
 * @see     template_process()
 * @ingroup themeable
 */
?>
<div id="node-<?php print $node->nid; ?>"
     class="<?php print $classes; ?> clearfix"<?php print $attributes; ?>>

  <?php print $user_picture; ?>

  <?php print render($title_prefix); ?>
  <?php if (!$page): ?>
    <h2<?php print $title_attributes; ?>><a
        href="<?php print $node_url; ?>"><?php print $title; ?></a></h2>
  <?php endif; ?>
  <?php print render($title_suffix); ?>

  <?php if ($display_submitted): ?>
    <div class="submitted">
      <?php print $submitted; ?>
    </div>
  <?php endif; ?>

  <div class="content"<?php print $content_attributes; ?>>
    <?php
    // We hide the comments and links now so that we can render them later.
    hide($content['comments']);
    hide($content['links']);
    switch ($view_mode) :
      case 'full': ?>
        <link rel="stylesheet" type="text/css"
              href="<?php print check_plain(variable_get('flipbook_3d_style_css', flipbook_3d_get_library_url() . '/deploy/css/flipbook.style.css')); ?>">
        <link rel="stylesheet" type="text/css"
              href="<?php print check_plain(variable_get('flipbook_3d_font_css', flipbook_3d_get_library_url() . '/deploy/css/font-awesome.css')); ?>">
      <?php
      $pdf = FALSE;
      $images = FALSE;
      if (isset($field_flipbook_3d_pdf_pages[0]['uri'])) {
        /** @var DrupalLocalStreamWrapper $wrapper */
        $wrapper = file_stream_wrapper_get_instance_by_uri(
          $field_flipbook_3d_pdf_pages[0]['uri']
        );
        if ($wrapper) {
          $url = $wrapper->getExternalUrl();
          $path = $wrapper->realpath();
          $pdf = (file_exists($path) && is_file($path) && is_readable($path));
        }
      }
      elseif (isset($field_flipbook_3d_pages) && is_array(
          $field_flipbook_3d_pages
        ) && !empty($field_flipbook_3d_pages)
      ) {
        $images = TRUE;
        $pages = array();
        foreach ($field_flipbook_3d_pages as $flipbook_3d_page) {
          $src_wrapper = file_stream_wrapper_get_instance_by_uri(
            $flipbook_3d_page['entity']->field_flipbook_3d_page[LANGUAGE_NONE][0]['uri']
          );
          $thumb_wrapper = file_stream_wrapper_get_instance_by_uri(
            $flipbook_3d_page['entity']->field_flipbook_3d_page_thumb[LANGUAGE_NONE][0]['uri']
          );
          $page = array(
            'src'   => $src_wrapper->getExternalUrl(),
            'thumb' => $thumb_wrapper->getExternalUrl(),
            'title' => $flipbook_3d_page['entity']->title,
          );
          $pages[] = $page;
        }
        $total_pages = count($pages);
      }
      ?>
        <script type="text/javascript">
          (function ($) {
            $(function () {
              var options = {
                <?php if($pdf) : ?>
                pdfUrl: '<?php print $url; ?>',
                pdfPageScale: <?php print check_plain(variable_get('flipbook_3d_pdf_page_scale' , '1.5')); ?>,
                <?php elseif($images) : ?>
                pages: [
                  <?php foreach($pages as $key => $page) : ?>
                  {
                    src: '<?php print $page['src']; ?>',
                    thumb: '<?php print $page['thumb']; ?>',
                    title: '<?php print $page['title']; ?>'
                  } <?php $key < $total_pages - 1 ? print ',' : FALSE; ?>
                  <?php endforeach; ?>
                ],
                <?php endif; ?>
                skin: '<?php print check_plain(variable_get('flipbook_3d_skin', 'light')); ?>',
                assets: {
                  preloader: '<?php print check_plain(variable_get('flipbook_3d_image_preloader', flipbook_3d_get_library_url() . '/deploy/images/preloader.jpg')); ?>',
                  overlay: '<?php print check_plain(variable_get('flipbook_3d_image_overlay', flipbook_3d_get_library_url() . '/deploy/images/overlay.jpg')); ?>',
                  flipMp3: '<?php print check_plain(variable_get('flipbook_3d_flip_mp3', flipbook_3d_get_library_url() . '/deploy/mp3/turnPage.mp3')); ?>'
                },
                rightToLeft: <?php print check_plain(variable_get('flipbook_3d_rtl', 'false')); ?>,
                startPage: <?php print check_plain(variable_get('flipbook_3d_start_page', '1')); ?>,
                sounds: <?php print check_plain(variable_get('flipbook_3d_sounds', 'true')); ?>,
                pageWidth: <?php print check_plain(variable_get('flipbook_3d_page_width', '1000')); ?>,
                pageHeight: <?php print check_plain(variable_get('flipbook_3d_page_height', '1414')); ?>,
                thumbnailWidth: <?php print check_plain(variable_get('flipbook_3d_thumb_width', '100')); ?>,
                thumbnailHeight: <?php print check_plain(variable_get('flipbook_3d_thumb_height', '141')); ?>,
                contentOnStart: <?php print check_plain(variable_get('flipbook_3d_content_on_start', 'false')); ?>,
                thumbnailsOnStart: <?php print check_plain(variable_get('flipbook_3d_thumbs_on_start', 'false')); ?>,
                mode: 'normal', // [String] You can select either 'normal', 'lightbox' or 'fullscreen'
                singlePageMode: <?php print check_plain(variable_get('flipbook_3d_single_page', 'false')); ?>,
                viewMode: '<?php print check_plain(variable_get('flipbook_3d_view_mode', '3d')); ?>',
                pageShadow1: <?php print check_plain(variable_get('flipbook_3d_shadow_1', 'true')); ?>,
                pageShadow2: <?php print check_plain(variable_get('flipbook_3d_shadow_2', 'true')); ?>,
                pageShadow3: <?php print check_plain(variable_get('flipbook_3d_shadow_3', 'false')); ?>,
                /* Mobile */
                singlePageModeIfMobile: true, // [Boolean] if the single page view will be forced on mobile devices
                pdfBrowserViewerIfMobile: true, // [Boolean] if instead of flipbook app browser will open pdf directly in its own default pdf viewer. For this option PDF url must be set.
                btnTocIfMobile: true, // [Boolean]
                btnThumbsIfMobile: true, // [Boolean]
                btnShareIfMobile: true, // [Boolean]
                btnDownloadPagesIfMobile: true, // [Boolean]
                btnDownloadPdfIfMobile: true, // [Boolean]
                btnSoundIfMobile: true, // [Boolean]
                btnExpandIfMobile: true, // [Boolean]
                btnPrintIfMobile: true // [Boolean]
              };
              jQuery('#real-3d-flipbook-container').flipBook(options);
            });
          }(jQuery));
        </script>
        <div id="real-3d-flipbook-container"></div>
        <style>
          #node-<?php print $node->nid; ?> .content, #real-3d-flipbook-container {
            height: 700px;
          }

          #real-3d-flipbook-container {
            position: relative;
          }
        </style>
        <?php
        break;

      default:
        print render($content);
        break;

    endswitch;
    ?>
  </div>
  <?php
  print render($content['links']);
  print render($content['comments']);
  ?>
</div>
