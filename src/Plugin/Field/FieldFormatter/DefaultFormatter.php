<?php

declare(strict_types=1);

namespace Drupal\address_for_china\Plugin\Field\FieldFormatter;

use Drupal\address_for_china\Element\AddressForChina;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'Address for china default' formatter.
 *
 * @FieldFormatter(
 *   id = "address_for_china_default",
 *   label = @Translation("Address for china default"),
 *   field_types = {"address_for_china"},
 * )
 */
final class DefaultFormatter extends FormatterBase {

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
    $elements['foo'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Foo'),
      '#default_value' => $this->getSetting('foo'),
    ];
    return $elements;
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
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $element = [];
    foreach ($items as $delta => $item) {
      $address = AddressForChina::getProvinceName($item->province_code)
        . ' - ' . AddressForChina::getCityName($item->province_code, $item->city_code)
        . ' - ' . AddressForChina::getDistrictName($item->city_code, $item->district_code);
      if (!empty($item->address_details)) {
        $address .= ' - ' . $item->address_details;
      }
      if (!empty($item->name)) {
        $address .= ' - ' . $item->name;
      }
      if (!empty($item->phone)) {
        $address .= ' - ' . $item->phone;
      }
      $element[$delta] = [
        '#markup' => $address,
      ];
    }
    return $element;
  }

}
