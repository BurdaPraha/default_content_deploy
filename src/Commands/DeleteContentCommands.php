<?php

namespace Drupal\default_content_deploy\Commands;

use Drush\Commands\DrushCommands;

/**
 * Class DeleteContentCommands.
 *
 * @package Drupal\default_content_deploy\Commands
 */
class DeleteContentCommands extends DrushCommands {

  public $no_content  = 'There is not content for "%s" entity type';
  public $all_deleted = 'All content for entity type "%s" deleted - %d items';
  public $few_deleted = 'Only %d from %d deleted for "%s" entity type';

  /**
   * Delete content from database
   * @todo: tested only for block_content type, improve it
   *
   * @param string $entityType
   *   The entity type to delete.
   *
   * @command default-content-deploy:delete
   * @usage drush default-content-deploy:delete
   *   Delete content for defined entity type
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function delete($entityType) {
    $ids = \Drupal::entityQuery($entityType)->execute();
    $c = count($ids);

    if(null == $c || $c == 0) {
      $this->io()->success(sprintf($this->no_content, $entityType));
      return;
    }

    $this->io()->confirm("Delete {$c} items?");
    $this->io()->progressStart($c);

    $i = 0;
    foreach ($ids as $k => $id) {
      \Drupal::entityTypeManager()->getStorage($entityType)->load($id)->delete();
      ++$i;
      $this->io()->progressAdvance();
    }

    if($c === $i){
      $this->io()->progressFinish();
      $this->io()->success(sprintf($this->all_deleted, $entityType, $i));
    }else {
      $this->io()->error(sprintf($this->few_deleted, $i, $c, $entityType));
    }

  }

}
