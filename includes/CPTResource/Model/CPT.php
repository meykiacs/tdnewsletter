<?php
declare(strict_types=1);
namespace TdNewsletter\CPTResource\Model;

class CPT
{

  public string $slug;
  public string $name;
  
  // a page is an example of a hierarchical post type
  public bool $hierarchical = false;
  public bool $public = false;
  public bool $showUI = false;
  public bool $showInRest = true;
  public string $capabilityType = 'post';
  public ?string $icon = null;
  public bool $hasArchive = false;
  
  public bool $hasTitle = true;
  public bool $hasEditor = true;
  public bool $hasThumbnail = true;
  public bool $hasAuthor = true;
  public bool $hasMeta = true;
  public bool $hasComments = false;
  
  public array $labels = [];

  /**
   * @var Meta[]
   */
  public array $metas = [];

  public function __construct(string $slug, string $name) {
    $this->slug = $slug;
    $this->name = $name;
  }

}