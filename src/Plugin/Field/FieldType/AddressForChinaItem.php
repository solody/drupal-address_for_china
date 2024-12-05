<?php

declare(strict_types=1);

namespace Drupal\address_for_china\Plugin\Field\FieldType;

use Drupal\Component\Utility\Random;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Defines the 'address_for_china' field type.
 *
 * @FieldType(
 *   id = "address_for_china",
 *   label = @Translation("Address for china"),
 *   description = @Translation("Some description."),
 *   default_widget = "address_for_china_default",
 *   default_formatter = "address_for_china_default",
 * )
 */
final class AddressForChinaItem extends FieldItemBase {


  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings(): array {
    $settings = [
      'enable_address_details' => TRUE,
      'enable_receiver' => TRUE,
    ];
    return $settings + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data): array {
    $element['enable_address_details'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable address details'),
      '#default_value' => $this->getSetting('enable_address_details'),
      '#disabled' => $has_data,
    ];
    $element['enable_receiver'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable receiver'),
      '#default_value' => $this->getSetting('enable_receiver'),
      '#disabled' => $has_data,
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultFieldSettings(): array {
    $settings = ['bar' => ''];
    return $settings + parent::defaultFieldSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function fieldSettingsForm(array $form, FormStateInterface $form_state): array {
    $element['bar'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Bar'),
      '#default_value' => $this->getSetting('bar'),
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty(): bool {
    return empty($this->get('province_code')->getValue())
      || empty($this->get('city_code')->getValue())
      || empty($this->get('district_code')->getValue());
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition): array {

    // @DCG
    // See /core/lib/Drupal/Core/TypedData/Plugin/DataType directory for
    // available data types.
    $properties['province_code'] = DataDefinition::create('string')
      ->setLabel(t('Province code'))
      ->setRequired(TRUE);
    $properties['city_code'] = DataDefinition::create('string')
      ->setLabel(t('City code'))
      ->setRequired(TRUE);
    $properties['district_code'] = DataDefinition::create('string')
      ->setLabel(t('District code'))
      ->setRequired(TRUE);
    $properties['address_details'] = DataDefinition::create('string')
      ->setLabel(t('Address details'))
      ->setRequired(FALSE);
    $properties['name'] = DataDefinition::create('string')
      ->setLabel(t('Name'))
      ->setRequired(FALSE);
    $properties['phone'] = DataDefinition::create('string')
      ->setLabel(t('Phone'))
      ->setRequired(FALSE);
    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function getConstraints(): array {
    $constraints = parent::getConstraints();

    $constraint_manager = $this->getTypedDataManager()->getValidationConstraintManager();

    // @DCG Suppose our value must not be longer than 10 characters.
    $options['province_code']['Length']['min'] = 0;
    $options['city_code']['Length']['min'] = 0;
    $options['district_code']['Length']['min'] = 0;

    // @DCG
    // See /core/lib/Drupal/Core/Validation/Plugin/Validation/Constraint
    // directory for available constraints.
    $constraints[] = $constraint_manager->create('ComplexData', $options);
    return $constraints;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition): array {

    $columns = [
      'province_code' => [
        'type' => 'varchar',
        'not null' => TRUE,
        'description' => 'Province code',
        'length' => 255,
      ],
      'city_code' => [
        'type' => 'varchar',
        'not null' => TRUE,
        'description' => 'City code',
        'length' => 255,
      ],
      'district_code' => [
        'type' => 'varchar',
        'not null' => TRUE,
        'description' => 'District code',
        'length' => 255,
      ],
      'address_details' => [
        'type' => 'varchar',
        'not null' => FALSE,
        'description' => 'Address details',
        'length' => 255,
      ],
      'name' => [
        'type' => 'varchar',
        'not null' => FALSE,
        'description' => 'Name',
        'length' => 255,
      ],
      'phone' => [
        'type' => 'varchar',
        'not null' => FALSE,
        'description' => 'Phone',
        'length' => 255,
      ],
    ];

    $schema = [
      'columns' => $columns,
      // @todo Add indexes here if necessary.
    ];

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition): array {
    $random = new Random();
    $values['province_code'] = $random->word(mt_rand(1, 50));
    $values['city_code'] = $random->word(mt_rand(1, 50));
    $values['district_code'] = $random->word(mt_rand(1, 50));
    $values['address_details'] = $random->word(mt_rand(1, 50));
    $values['name'] = $random->word(mt_rand(1, 50));
    $values['phone'] = $random->word(mt_rand(1, 50));
    return $values;
  }

}
