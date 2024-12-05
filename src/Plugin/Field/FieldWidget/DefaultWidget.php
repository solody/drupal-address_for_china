<?php

declare(strict_types=1);

namespace Drupal\address_for_china\Plugin\Field\FieldWidget;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the 'address_for_china_default' field widget.
 *
 * @FieldWidget(
 *   id = "address_for_china_default",
 *   label = @Translation("Address for china default"),
 *   field_types = {"address_for_china"},
 * )
 */
final class DefaultWidget extends WidgetBase implements ContainerFactoryPluginInterface {

  /**
   * Constructs the plugin instance.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly ModuleHandlerInterface $moduleHandler,
  ) {
    parent::__construct($plugin_id, $plugin_definition, $configuration['field_definition'], $configuration['settings'], $configuration['third_party_settings']);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new self(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('module_handler'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    $setting = ['foo' => 'bar'];
    return $setting + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $element['foo'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Foo'),
      '#default_value' => $this->getSetting('foo'),
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    return [
      $this->t('Foo: @foo', ['@foo' => $this->getSetting('foo')]),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state): array {
    $storage_settings = $this->fieldDefinition->getFieldStorageDefinition()->getSettings();
    return [
      '#type' => 'address_for_china',
      '#province_code' => $items[$delta]->province_code ?? NULL,
      '#city_code' => $items[$delta]->city_code ?? NULL,
      '#district_code' => $items[$delta]->district_code ?? NULL,
      '#address_details' => $items[$delta]->address_details ?? NULL,
      '#name' => $items[$delta]->name ?? NULL,
      '#phone' => $items[$delta]->phone ?? NULL,
      '#enable_address_details' => $storage_settings['enable_address_details'],
      '#enable_receiver' => $storage_settings['enable_receiver'],
    ] + $element;
  }

}
