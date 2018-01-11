<?php

namespace Drupal\Tests\layout_builder\Kernel;

use Drupal\layout_builder\Section;

/**
 * Ensures that Layout Builder and core EntityViewDisplays are compatible.
 *
 * @group layout_builder
 */
class LayoutBuilderInstallTest extends LayoutBuilderCompatibilityTestBase {

  /**
   * Tests the compatibility of Layout Builder with existing entity displays.
   */
  public function testCompatibility() {
    // Ensure that the fields are shown.
    $expected_fields = [
      'field field--name-name field--type-string field--label-hidden field__item',
      'field field--name-test-field-display-configurable field--type-boolean field--label-above',
      'clearfix text-formatted field field--name-test-display-configurable field--type-text field--label-above',
      'clearfix text-formatted field field--name-test-display-non-configurable field--type-text field--label-above',
      'clearfix text-formatted field field--name-test-display-multiple field--type-text field--label-above',
    ];
    $this->assertFieldAttributes($this->entity, $expected_fields);

    $this->installLayoutBuilder();

    // Without using Layout Builder for an override, the result has not changed.
    $this->assertFieldAttributes($this->entity, $expected_fields);

    // Add a layout override.
    $this->entity->get('layout_builder__layout')->appendSection(new Section('layout_onecol'));
    $this->entity->save();

    // The rendered entity has now changed. The non-configurable field is shown
    // outside the layout, the configurable field is not shown at all, and the
    // layout itself is rendered (but empty).
    $new_expected_fields = [
      'field field--name-name field--type-string field--label-hidden field__item',
      'clearfix text-formatted field field--name-test-display-non-configurable field--type-text field--label-above',
      'clearfix text-formatted field field--name-test-display-multiple field--type-text field--label-above',
    ];
    $this->assertFieldAttributes($this->entity, $new_expected_fields);
    $this->assertNotEmpty($this->cssSelect('.layout--onecol'));

    // Removing the layout restores the original rendering of the entity.
    $this->entity->get('layout_builder__layout')->removeSection(0);
    $this->entity->save();
    $this->assertFieldAttributes($this->entity, $expected_fields);
  }

}
