<?php return array (
  'da034c4f-0e24-4906-94b9-66b26c0549c9' => 
  array (
    'uuid' => 'da034c4f-0e24-4906-94b9-66b26c0549c9',
    'name' => 'Detailed Block',
    'type' => 'mixin',
    'handle' => 'Blocks\\DetailedBlock',
    'fields' => 
    array (
      'title' => 
      array (
        'label' => 'Title',
        'type' => 'text',
      ),
      'content' => 
      array (
        'label' => 'Description',
        'type' => 'richeditor',
        'size' => 'small',
      ),
      'list_items' => 
      array (
        'label' => 'List Items',
        'type' => 'datatable',
        'btnAddRowLabel' => 'Add Item',
        'btnDeleteRowLabel' => 'Delete Item',
        'columns' => 
        array (
          'text' => 
          array (
            'type' => 'string',
            'title' => 'Text',
          ),
        ),
      ),
      'button_text' => 
      array (
        'label' => 'Button Text',
        'span' => 'auto',
        'placeholder' => 'Main call to action',
      ),
      'button_url' => 
      array (
        'label' => 'Button URL',
        'span' => 'auto',
      ),
      'image' => 
      array (
        'label' => 'Image',
        'type' => 'mediafinder',
        'mode' => 'image',
        'maxItems' => 1,
      ),
    ),
    'handleSlug' => 'blocks-detailed-block',
    '_theme' => 'demo',
  ),
  '21aad99b-d3c6-4f5e-b271-15471c81e11b' => 
  array (
    'uuid' => '21aad99b-d3c6-4f5e-b271-15471c81e11b',
    'name' => 'Image Slice',
    'type' => 'mixin',
    'handle' => 'Blocks\\ImageSlice',
    'fields' => 
    array (
      'image' => 
      array (
        'label' => 'Image',
        'type' => 'mediafinder',
        'mode' => 'image',
        'maxItems' => 1,
      ),
    ),
    'handleSlug' => 'blocks-image-slice',
    '_theme' => 'demo',
  ),
  '015fde4b-23d8-4ba3-8e78-9c6ebfb5fcf7' => 
  array (
    'uuid' => '015fde4b-23d8-4ba3-8e78-9c6ebfb5fcf7',
    'name' => 'Paragraph Block',
    'type' => 'mixin',
    'handle' => 'Blocks\\ParagraphBlock',
    'fields' => 
    array (
      'title' => 
      array (
        'label' => 'Title',
        'type' => 'text',
      ),
      'content' => 
      array (
        'label' => 'Description',
        'type' => 'richeditor',
        'size' => 'small',
      ),
      'image' => 
      array (
        'label' => 'Image',
        'type' => 'mediafinder',
        'mode' => 'image',
        'maxItems' => 1,
      ),
    ),
    'handleSlug' => 'blocks-paragraph-block',
    '_theme' => 'demo',
  ),
  '55615b16-120f-4be9-9429-6ae6dabc523c' => 
  array (
    'uuid' => '55615b16-120f-4be9-9429-6ae6dabc523c',
    'name' => 'Scoreboard Metrics',
    'type' => 'mixin',
    'handle' => 'Blocks\\ScoreboardMetrics',
    'fields' => 
    array (
      'metrics' => 
      array (
        'label' => 'Metrics',
        'type' => 'repeater',
        'form' => 
        array (
          'fields' => 
          array (
            'number' => 
            array (
              'label' => 'Number',
              'type' => 'number',
              'span' => 'row',
              'spanClass' => 'col-md-3',
            ),
            'description' => 
            array (
              'label' => 'Description',
              'type' => 'text',
              'span' => 'row',
              'spanClass' => 'col-md-9',
            ),
            'icon' => 
            array (
              'label' => 'Icon',
              'type' => 'radio',
              'cssClass' => 'inline-options',
              'options' => 
              array (
                'notepad' => 'Notepad',
                'shield' => 'Shield',
                'basket' => 'Basket',
                'briefcase' => 'Briefcase',
              ),
            ),
          ),
        ),
      ),
    ),
    'handleSlug' => 'blocks-scoreboard-metrics',
    '_theme' => 'demo',
  ),
  '8c4041cf-f16d-46f8-86be-9372a266ae6d' => 
  array (
    'uuid' => '8c4041cf-f16d-46f8-86be-9372a266ae6d',
    'name' => 'Team Leaders',
    'type' => 'mixin',
    'handle' => 'Blocks\\TeamLeaders',
    'fields' => 
    array (
      'members' => 
      array (
        'label' => 'Members',
        'type' => 'repeater',
        'itemsExpanded' => false,
        'form' => 
        array (
          'fields' => 
          array (
            'title' => 
            array (
              'label' => 'Title',
              'span' => 'left',
            ),
            'role' => 
            array (
              'label' => 'Role',
              'span' => 'right',
            ),
            'description' => 
            array (
              'label' => 'Description',
              'type' => 'textarea',
              'size' => 'tiny',
            ),
            'avatar' => 
            array (
              'label' => 'Image',
              'type' => 'mediafinder',
              'mode' => 'image',
              'maxItems' => 1,
            ),
            '_social_links' => 
            array (
              'label' => 'Social Links',
              'type' => 'mixin',
              'source' => 'Fields\\SocialLinks',
            ),
          ),
        ),
      ),
    ),
    'handleSlug' => 'blocks-team-leaders',
    '_theme' => 'demo',
  ),
  '7b193500-ac0b-481f-a79c-2a362646364d' => 
  array (
    'uuid' => '7b193500-ac0b-481f-a79c-2a362646364d',
    'handle' => 'Fields\\Blocks',
    'type' => 'mixin',
    'name' => 'Page Blocks',
    'fields' => 
    array (
      'blocks' => 
      array (
        'label' => 'Blocks',
        'type' => 'repeater',
        'displayMode' => 'builder',
        'span' => 'adaptive',
        'tab' => 'Blocks',
        'groups' => 
        array (
          'image_slice' => 
          array (
            'name' => 'Image Slice',
            'description' => 'A large image with an angled slice.',
            'icon' => 'octo-icon-picture',
            'fields' => 
            array (
              '_mixin' => 
              array (
                'type' => 'mixin',
                'source' => 'Blocks\\ImageSlice',
              ),
            ),
          ),
          'paragraph_block' => 
          array (
            'name' => 'Paragraph Block',
            'description' => 'Simple paragraph with image',
            'icon' => 'octo-icon-text-h1',
            'fields' => 
            array (
              '_mixin' => 
              array (
                'type' => 'mixin',
                'source' => 'Blocks\\ParagraphBlock',
              ),
            ),
          ),
          'detailed_block' => 
          array (
            'name' => 'Detailed Block',
            'description' => 'Detailed paragraph with image and list',
            'icon' => 'octo-icon-diamond',
            'fields' => 
            array (
              '_mixin' => 
              array (
                'type' => 'mixin',
                'source' => 'Blocks\\DetailedBlock',
              ),
            ),
          ),
          'scoreboard_metrics' => 
          array (
            'name' => 'Scoreboard Metrics',
            'description' => 'Show some impressive metrics about the business.',
            'icon' => 'icon-quote-right',
            'fields' => 
            array (
              '_mixin' => 
              array (
                'type' => 'mixin',
                'source' => 'Blocks\\ScoreboardMetrics',
              ),
            ),
          ),
          'team_leaders' => 
          array (
            'name' => 'Team Leaders',
            'description' => 'Display the team members.',
            'icon' => 'octo-icon-comment',
            'fields' => 
            array (
              '_mixin' => 
              array (
                'type' => 'mixin',
                'source' => 'Blocks\\TeamLeaders',
              ),
            ),
          ),
        ),
      ),
    ),
    'handleSlug' => 'fields-blocks',
    '_theme' => 'demo',
  ),
  '4d7fd1e4-85f2-48f5-947e-92819fc8664b' => 
  array (
    'uuid' => '4d7fd1e4-85f2-48f5-947e-92819fc8664b',
    'handle' => 'Fields\\BlogContent',
    'type' => 'mixin',
    'name' => 'Blog Post Content',
    'fields' => 
    array (
      'banner' => 
      array (
        'tab' => 'Manage',
        'label' => 'Banner',
        'type' => 'fileupload',
        'mode' => 'image',
        'maxFiles' => 1,
      ),
      'author' => 
      array (
        'tab' => 'Manage',
        'label' => 'Author',
        'commentAbove' => 'Select the author for this blog post',
        'type' => 'entries',
        'maxItems' => 1,
        'source' => 'Blog\\Author',
      ),
      'categories' => 
      array (
        'tab' => 'Manage',
        'label' => 'Categories',
        'commentAbove' => 'Select categories the blog post belongs to',
        'type' => 'entries',
        'source' => 'Blog\\Category',
      ),
      'featured_text' => 
      array (
        'tab' => 'Featured',
        'label' => 'Featured Text',
        'type' => 'textarea',
        'size' => 'small',
      ),
      'gallery' => 
      array (
        'label' => 'Gallery',
        'type' => 'fileupload',
        'mode' => 'image',
        'span' => 'adaptive',
        'tab' => 'Gallery',
      ),
      'gallery_media' => 
      array (
        'label' => 'Media',
        'type' => 'mediafinder',
        'mode' => 'image',
        'span' => 'adaptive',
        'tab' => 'Media',
      ),
    ),
    'handleSlug' => 'fields-blog-content',
    '_theme' => 'demo',
  ),
  'ae2d2c25-3a0e-4765-8b36-d1666fc0e31f' => 
  array (
    'uuid' => 'ae2d2c25-3a0e-4765-8b36-d1666fc0e31f',
    'name' => 'Social Links',
    'type' => 'mixin',
    'handle' => 'Fields\\SocialLinks',
    'fields' => 
    array (
      'social_links' => 
      array (
        'type' => 'repeater',
        'prompt' => 'Add Link',
        'form' => 
        array (
          'fields' => 
          array (
            'platform' => 
            array (
              'span' => 'auto',
              'label' => 'Platform',
              'type' => 'dropdown',
              'options' => 
              array (
                'facebook' => 'Facebook',
                'linkedin' => 'LinkedIn',
                'dribbble' => 'Dribbble',
                'twitter' => 'Twitter',
                'youtube' => 'YouTube',
              ),
            ),
            'url' => 
            array (
              'span' => 'auto',
              'label' => 'Social Link',
              'type' => 'text',
              'placeholder' => 'https://...',
            ),
          ),
        ),
      ),
    ),
    'handleSlug' => 'fields-social-links',
    '_theme' => 'demo',
  ),
  '936f26c0-b816-4c78-afaa-0b6977e80213' => 
  array (
    'uuid' => '936f26c0-b816-4c78-afaa-0b6977e80213',
    'handle' => 'Site\\Menus\\MenuItem',
    'type' => 'mixin',
    'name' => 'Menu Item',
    'fields' => 
    array (
      'is_hidden' => 
      array (
        'label' => 'Hidden',
        'comment' => 'Hide this menu item from appearing on the website.',
        'type' => 'checkbox',
        'tab' => 'Display',
      ),
      'code' => 
      array (
        'label' => 'Code',
        'comment' => 'Enter the menu item code if you want to access it with the API.',
        'tab' => 'Attributes',
        'span' => 'auto',
        'type' => 'text',
        'preset' => 
        array (
          'field' => 'title',
          'type' => 'slug',
        ),
      ),
      'css_class' => 
      array (
        'label' => 'CSS Class',
        'comment' => 'Enter a CSS class name to give this menu item a custom appearance.',
        'tab' => 'Attributes',
        'span' => 'auto',
        'type' => 'text',
      ),
      'is_external' => 
      array (
        'label' => 'External Link',
        'comment' => 'Open links for this menu item in a new window.',
        'tab' => 'Attributes',
        'type' => 'checkbox',
      ),
      'nesting' => 
      array (
        'label' => 'Include nested items',
        'shortLabel' => 'Nesting',
        'comment' => 'Nested items could be generated dynamically by supported page references.',
        'type' => 'checkbox',
        'tab' => 'Reference',
        'column' => false,
      ),
      'replace' => 
      array (
        'label' => 'Replace this item with its generated children',
        'comment' => 'Use this checkbox to push generated menu items to the same level with this item. This item itself will be hidden.',
        'type' => 'checkbox',
        'tab' => 'Reference',
        'column' => false,
        'scope' => false,
        'trigger' => 
        array (
          'action' => 'disable|empty',
          'field' => 'nesting',
          'condition' => 'unchecked',
        ),
      ),
    ),
    'handleSlug' => 'site-menus-menu-item',
    '_theme' => 'demo',
  ),
  '5282e203-615f-4f1f-9c23-22b010c49eba' => 
  array (
    'uuid' => '5282e203-615f-4f1f-9c23-22b010c49eba',
    'handle' => 'Site\\Menus\\SitemapItem',
    'type' => 'mixin',
    'name' => 'Menu Item',
    'fields' => 
    array (
      'priority' => 
      array (
        'label' => 'Priority',
        'commentAbove' => 'The priority of this URL relative to other URLs on your site.',
        'tab' => 'Sitemap',
        'type' => 'radio',
        'inlineOptions' => true,
        'options' => 
        array (
          '0.1' => '0.1',
          '0.2' => '0.2',
          '0.3' => '0.3',
          '0.4' => '0.4',
          '0.5' => '0.5',
          '0.6' => '0.6',
          '0.7' => '0.7',
          '0.8' => '0.8',
          '0.9' => '0.9',
          '1.0' => '1.0',
        ),
      ),
      'changefreq' => 
      array (
        'commentAbove' => 'How frequently the page is likely to change.',
        'label' => 'Change Frequency',
        'tab' => 'Sitemap',
        'type' => 'radio',
        'inlineOptions' => true,
        'options' => 
        array (
          'always' => 'Always',
          'hourly' => 'Hourly',
          'daily' => 'Daily',
          'weekly' => 'Weekly',
          'monthly' => 'Monthly',
          'yearly' => 'Yearly',
          'never' => 'Never',
        ),
      ),
      'nesting' => 
      array (
        'label' => 'Include nested items',
        'shortLabel' => 'Nesting',
        'comment' => 'Nested items could be generated dynamically by supported page references.',
        'type' => 'checkbox',
        'tab' => 'Reference',
        'column' => false,
      ),
      'replace' => 
      array (
        'label' => 'Replace this item with its generated children',
        'comment' => 'Use this checkbox to push generated menu items to the same level with this item. This item itself will be hidden.',
        'type' => 'checkbox',
        'tab' => 'Reference',
        'column' => false,
        'scope' => false,
        'trigger' => 
        array (
          'action' => 'disable|empty',
          'field' => 'nesting',
          'condition' => 'unchecked',
        ),
      ),
    ),
    'handleSlug' => 'site-menus-sitemap-item',
    '_theme' => 'demo',
  ),
);