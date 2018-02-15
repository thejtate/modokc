<?php

/**
 * @file
 * Default theme implementation to display a node.
 *
 * Available variables:
 * - $title: the (sanitized) title of the node.
 * - $content: An array of node items. Use render($content) to print them all,
 *   or print a subset such as render($content['field_example']). Use
 *   hide($content['field_example']) to temporarily suppress the printing of a
 *   given element.
 * - $user_picture: The node author's picture from user-picture.tpl.php.
 * - $date: Formatted creation date. Preprocess functions can reformat it by
 *   calling format_date() with the desired parameters on the $created variable.
 * - $name: Themed username of node author output from theme_username().
 * - $node_url: Direct URL of the current node.
 * - $display_submitted: Whether submission information should be displayed.
 * - $submitted: Submission information created from $name and $date during
 *   template_preprocess_node().
 * - $classes: String of classes that can be used to style contextually through
 *   CSS. It can be manipulated through the variable $classes_array from
 *   preprocess functions. The default values can be one or more of the
 *   following:
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
 * - $title_prefix (array): An array containing additional output populated by
 *   modules, intended to be displayed in front of the main title tag that
 *   appears in the template.
 * - $title_suffix (array): An array containing additional output populated by
 *   modules, intended to be displayed after the main title tag that appears in
 *   the template.
 *
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
 *
 * Node status variables:
 * - $view_mode: View mode; for example, "full", "teaser".
 * - $teaser: Flag for the teaser state (shortcut for $view_mode == 'teaser').
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
 *
 * Field variables: for each field instance attached to the node a corresponding
 * variable is defined; for example, $node->body becomes $body. When needing to
 * access a field's raw values, developers/themers are strongly encouraged to
 * use these variables. Otherwise they will have to explicitly specify the
 * desired field language; for example, $node->body['en'], thus overriding any
 * language negotiation rule that was previously applied.
 *
 * @see template_preprocess()
 * @see template_preprocess_node()
 * @see template_process()
 *
 * @ingroup themeable
 */
?>
<div id="node-<?php print $node->nid; ?>"
     class="<?php print $classes; ?> "<?php print $attributes; ?>>

  <?php print render($title_prefix); ?>
  <?php print render($title_suffix); ?>

  <div class="content"<?php print $content_attributes; ?>>

    <section class="section section-top">
      <div class="site-container">
        <h1>
          <?php print render($content['field_main_sect_title_txt']); ?>
        </h1>
        <?php print render($content['field_main_sect_top_elements_fc']); ?>
      </div>
    </section>

    <div class="b-desc">
      <span
        class="title"><?php print render($content['field_main_sect_vote_first_txt']); ?></span>
      <div
        class="img"><?php print render($content['field_main_sect_top_mid_img']); ?></div>
        <span class="text">
         <?php print render($content['field_main_sect_vote_snd_txt']); ?>
        </span>
    </div>

    <section class="section section-vote">
      <a href="#" name="why-vote"></a>
      <div class="site-container">
        <div class="img el-with-animation animate-opacity animate-up-to-down">
          <?php print render($content['field_main_sect_vote_top_img']); ?>
        </div>
        <h2>
          <?php print render($content['field_main_sect_vote_top_txt']); ?>
        </h2>
        <div class="cols cols-2">
          <?php print render($content['field_main_sect_vote_why_yes_fc']); ?>
        </div>
      </div>
    </section>

    <section class="section section-logo">
      <?php if (isset($section_logo_image)): ?>
        <img src="<?php print ($section_logo_image); ?>" align=""
             class=" el-with-animation animate-opacity animate-up-to-down">
      <?php endif; ?>
    </section>

    <section class="section section-faq">
      <a href="#" name="faq"></a>
      <div class="site-container">
        <?php if (isset($section_faq_image)): ?>
          <div class="img el-with-animation animate-opacity animate-up-to-down">
            <img src="<?php print ($section_faq_image); ?>" align="">
          </div>
        <?php endif; ?>
        <h2>
          <?php print render($content['field_main_sect_faq_title_txt']); ?>
        </h2>
        <div class="cols cols-2">
          <?php print render($content['field_main_sect_faq_fc']); ?>
        </div>
      </div>
    </section>

    <section class="section section-items">
      <a href="#" name="contact"></a>
      <div class="items">
        <div class="item">
          <img src="<?php print $section_contacts_first_image; ?>" alt=""/>
        </div>

        <div class="item">
          <div class="inner el-with-animation animate-zoom-in animate-opacity">
            <div class="img">
              <img src="<?php print $section_contacts_first_icon_image; ?>"
                   align="">
            </div>
            <h2>
              <?php print render($content['field_main_sect_items_first_txt']); ?>
            </h2>
            <div class="text">
              <p>
                <?php print render($content['field_main_sect_items_descr_txt']); ?>
              </p>
            </div>
            <div class="btn-wrapp style-a">
              <?php print render($content['field_main_sect_items_first_link']); ?>
            </div>
          </div>
        </div>

        <div class="item">
          <div class="inner el-with-animation animate-zoom-in animate-opacity">
            <div class="img">
              <img src="<?php print $section_contacts_second_icon_image; ?>"
                   alt="">
            </div>
            <h2><?php print render($content['field_main_sect_items_snd_txt']); ?></h2>

            <?php print render($content['webform']) ?>
          </div>
        </div>
        <div class="item">
          <img src="<?php print $section_contacts_second_image; ?>" alt=""/>
          <a href="" name="updated"></a>
        </div>
      </div>
    </section>

    <?php
    // We hide the comments and links now so that we can render them later.
    hide($content['comments']);
    hide($content['links']);
    hide($content['body']);

    print render($content);
    ?>
  </div>

  <?php print render($content['links']); ?>

  <?php print render($content['comments']); ?>

</div>
