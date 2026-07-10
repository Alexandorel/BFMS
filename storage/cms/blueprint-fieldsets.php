<?php return array (
  '6947ff28-b660-47d7-9240-24ca6d58aeae' => 
  array (
    'name' => 'Author',
    'handle' => 'Blog\\Author',
    'contentUuid' => '6947ff28-b660-47d7-9240-24ca6d58aeae',
    'fields' => 
    array (
      'avatar' => 
      array (
        'label' => 'Avatar',
        'type' => 'mediafinder',
        'mode' => 'image',
      ),
      'email' => 
      array (
        'label' => 'Email',
        'type' => 'email',
        'column' => 
        array (
          'searchable' => true,
        ),
      ),
      'role' => 
      array (
        'label' => 'Role',
        'type' => 'text',
      ),
      'about' => 
      array (
        'label' => 'About',
        'type' => 'textarea',
      ),
      '_social_links' => 
      array (
        'type' => 'mixin',
        'label' => 'Social Links',
        'source' => 'Fields\\SocialLinks',
        'tab' => 'Social',
      ),
    ),
    'columns' => NULL,
    'scopes' => NULL,
    'validation' => NULL,
  ),
  'b022a74b-15e6-4c6b-9eb9-17efc5103543' => 
  array (
    'name' => 'Category',
    'handle' => 'Blog\\Category',
    'contentUuid' => 'b022a74b-15e6-4c6b-9eb9-17efc5103543',
    'fields' => 
    array (
      'description' => 
      array (
        'label' => 'Description',
      ),
    ),
    'columns' => NULL,
    'scopes' => NULL,
    'validation' => NULL,
  ),
  'edcd102e-0525-4e4d-b07e-633ae6c18db6:regular_post' => 
  array (
    'name' => 'Regular Post',
    'handle' => 'regular_post',
    'contentUuid' => 'edcd102e-0525-4e4d-b07e-633ae6c18db6',
    'columns' => NULL,
    'scopes' => NULL,
    'validation' => NULL,
    'fields' => 
    array (
      'content' => 
      array (
        'tab' => 'Edit',
        'label' => 'Content',
        'type' => 'richeditor',
        'span' => 'adaptive',
      ),
      '_blog_post_content' => 
      array (
        'type' => 'mixin',
        'source' => 'Fields\\BlogContent',
      ),
    ),
  ),
  'edcd102e-0525-4e4d-b07e-633ae6c18db6:markdown_post' => 
  array (
    'name' => 'Markdown Post',
    'handle' => 'markdown_post',
    'contentUuid' => 'edcd102e-0525-4e4d-b07e-633ae6c18db6',
    'columns' => NULL,
    'scopes' => NULL,
    'validation' => NULL,
    'fields' => 
    array (
      'content' => 
      array (
        'tab' => 'Edit',
        'label' => 'Content',
        'type' => 'markdown',
        'span' => 'adaptive',
      ),
      '_blog_post_content' => 
      array (
        'type' => 'mixin',
        'source' => 'Fields\\BlogContent',
      ),
    ),
  ),
  'a63fabaf-7c0b-4c74-b36f-7abf1a3ad1c1' => 
  array (
    'name' => 'About Page',
    'handle' => 'Page\\About',
    'contentUuid' => 'a63fabaf-7c0b-4c74-b36f-7abf1a3ad1c1',
    'fields' => 
    array (
      '_block_builder' => 
      array (
        'type' => 'mixin',
        'source' => 'Fields\\Blocks',
      ),
    ),
    'columns' => NULL,
    'scopes' => NULL,
    'validation' => NULL,
  ),
  '339b11b7-69ad-43c4-9be1-6953e7738827' => 
  array (
    'name' => 'Article',
    'handle' => 'Page\\Article',
    'contentUuid' => '339b11b7-69ad-43c4-9be1-6953e7738827',
    'fields' => 
    array (
      'content' => 
      array (
        'label' => 'Content',
        'tab' => 'Edit',
        'type' => 'richeditor',
        'span' => 'adaptive',
      ),
      'banner' => 
      array (
        'label' => 'Banner',
        'type' => 'fileupload',
        'mode' => 'image',
        'maxFiles' => 1,
      ),
      'show_in_toc' => 
      array (
        'label' => 'Show in TOC',
        'comment' => 'Include this article in the table of contents',
        'type' => 'checkbox',
      ),
      'summary_text' => 
      array (
        'label' => 'Summary Text',
        'type' => 'textarea',
        'size' => 'small',
      ),
      'gallery' => 
      array (
        'label' => 'Gallery',
        'type' => 'fileupload',
        'mode' => 'image',
      ),
      'external_links' => 
      array (
        'label' => 'External Links',
        'tab' => 'Links',
        'type' => 'repeater',
        'form' => 
        array (
          'fields' => 
          array (
            'link_text' => 
            array (
              'label' => 'Link Text',
              'span' => 'auto',
            ),
            'link_url' => 
            array (
              'label' => 'Link URL',
              'span' => 'auto',
            ),
          ),
        ),
      ),
    ),
    'columns' => NULL,
    'scopes' => NULL,
    'validation' => NULL,
  ),
  '85e471d2-09b9-4f3d-a63b-1ae9d92d2879:regular_menu' => 
  array (
    'name' => 'Regular Menu',
    'handle' => 'regular_menu',
    'contentUuid' => '85e471d2-09b9-4f3d-a63b-1ae9d92d2879',
    'columns' => 
    array (
      'title' => 
      array (
        'label' => 'Title',
      ),
      'entry_type_name' => 
      array (
        'label' => 'Menu Type',
        'invisible' => false,
      ),
      'slug' => 
      array (
        'label' => 'Code',
        'invisible' => false,
      ),
    ),
    'scopes' => NULL,
    'validation' => NULL,
    'fields' => 
    array (
      'slug' => 
      array (
        'label' => 'Code',
        'validation' => 
        array (
          0 => 'required',
        ),
      ),
      'items' => 
      array (
        'label' => 'Menu Items',
        'type' => 'nesteditems',
        'span' => 'adaptive',
        'maxDepth' => 0,
        'customMessages' => 
        array (
          'buttonCreate' => 'Add Item',
          'titleCreateForm' => 'Create Item',
          'titleUpdateForm' => 'Edit Item',
        ),
        'form' => 
        array (
          'fields' => 
          array (
            'title' => 
            array (
              'label' => 'Title',
              'tab' => 'Reference',
              'default' => 'New Menu Item',
              'span' => 'full',
              'type' => 'text',
              'autoFocus' => true,
              'validation' => 
              array (
                0 => 'required',
              ),
            ),
            'reference' => 
            array (
              'label' => 'Reference',
              'type' => 'pagefinder',
              'tab' => 'Reference',
            ),
          ),
          'tabs' => 
          array (
            'fields' => 
            array (
              '_menu_item' => 
              array (
                'type' => 'mixin',
                'source' => 'Site\\Menus\\MenuItem',
              ),
            ),
          ),
        ),
      ),
    ),
  ),
  '85e471d2-09b9-4f3d-a63b-1ae9d92d2879:sitemap_menu' => 
  array (
    'name' => 'Sitemap Menu',
    'handle' => 'sitemap_menu',
    'contentUuid' => '85e471d2-09b9-4f3d-a63b-1ae9d92d2879',
    'columns' => 
    array (
      'title' => 
      array (
        'label' => 'Title',
      ),
      'entry_type_name' => 
      array (
        'label' => 'Menu Type',
        'invisible' => false,
      ),
      'slug' => 
      array (
        'label' => 'Code',
        'invisible' => false,
      ),
    ),
    'scopes' => NULL,
    'validation' => NULL,
    'fields' => 
    array (
      'slug' => 
      array (
        'label' => 'Code',
        'validation' => 
        array (
          0 => 'required',
        ),
      ),
      'sitemap_items' => 
      array (
        'label' => 'Sitemap Items',
        'type' => 'nesteditems',
        'span' => 'adaptive',
        'maxDepth' => 1,
        'customMessages' => 
        array (
          'buttonCreate' => 'Add Sitemap Item',
          'titleCreateForm' => 'Create Sitemap Item',
          'titleUpdateForm' => 'Edit Sitemap Item',
        ),
        'form' => 
        array (
          'fields' => 
          array (
            'title' => 
            array (
              'label' => 'Title',
              'tab' => 'Reference',
              'default' => 'New Menu Item',
              'span' => 'full',
              'type' => 'text',
              'autoFocus' => true,
              'validation' => 
              array (
                0 => 'required',
              ),
            ),
            'reference' => 
            array (
              'label' => 'Reference',
              'type' => 'pagefinder',
              'tab' => 'Reference',
            ),
          ),
          'tabs' => 
          array (
            'fields' => 
            array (
              '_menu_item' => 
              array (
                'type' => 'mixin',
                'source' => 'Site\\Menus\\SitemapItem',
              ),
            ),
          ),
        ),
      ),
    ),
  ),
  '3328c303-7989-462e-b866-27e7037ba275' => 
  array (
    'name' => 'Blog Settings',
    'handle' => 'Blog\\Config',
    'contentUuid' => '3328c303-7989-462e-b866-27e7037ba275',
    'fields' => 
    array (
      'blog_name' => 
      array (
        'label' => 'Blog Name',
        'tab' => 'General',
        'placeholder' => 'Latest News',
      ),
      'about_this_blog' => 
      array (
        'label' => 'About This Blog',
        'comment' => 'Customize this section to tell your visitors a little bit about your publication, writers, content, or something else entirely. Totally up to you.',
        'type' => 'textarea',
        'size' => 'small',
        'tab' => 'General',
      ),
      '_section1' => 
      array (
        'label' => 'Social Links',
        'type' => 'section',
        'tab' => 'General',
      ),
      '_social_links' => 
      array (
        'type' => 'mixin',
        'source' => 'Fields\\SocialLinks',
        'tab' => 'General',
      ),
    ),
    'validation' => NULL,
  ),
);