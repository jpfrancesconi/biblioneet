<?php

namespace Drupal\Tests\twig_tweak\Kernel;

use Drupal\Core\Cache\Cache;
use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;
use Drupal\Tests\user\Traits\UserCreationTrait;

/**
 * A test for EntityViewBuilder.
 *
 * @group twig_tweak
 */
final class EntityViewBuilderTest extends KernelTestBase {

  use UserCreationTrait;

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'twig_tweak',
    'twig_tweak_test',
    'user',
    'system',
    'node',
    'field',
    'text',
  ];

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();
    $this->installConfig(['system', 'node']);
    $this->installEntitySchema('node');
    NodeType::create(['type' => 'article'])->save();
    $this->setUpCurrentUser([], ['access content']);
  }

  /**
   * Test callback.
   */
  public function testEntityViewBuilder(): void {

    $view_builder = $this->container->get('twig_tweak.entity_view_builder');

    $values = [
      'type' => 'article',
      'title' => 'Public node',
    ];
    $public_node = Node::create($values);
    $public_node->save();

    $values = [
      'type' => 'article',
      'title' => 'Private node',
    ];
    $private_node = Node::create($values);
    $private_node->save();

    // -- Full mode.
    $build = $view_builder->build($public_node);
    self::assertArrayHasKey('#node', $build);
    $expected_cache = [
      'tags' => [
        'node:1',
        'node_view',
        'tag_from_twig_tweak_test_node_access',
      ],
      'contexts' => [
        'user',
        'user.permissions',
      ],
      'max-age' => 50,
      'keys' => [
        'entity_view',
        'node',
        '1',
        'full',
      ],
      'bin' => 'render',
    ];
    self::assertSame($expected_cache, $build['#cache']);

    $expected_html = <<< 'HTML'
      <article role="article">
        <h2><a href="/node/1" rel="bookmark"><span>Public node</span></a></h2>
        <div></div>
      </article>
    HTML;
    $actual_html = $this->renderPlain($build);
    self::assertSame(self::normalizeHtml($expected_html), self::normalizeHtml($actual_html));

    // -- Teaser mode.
    $build = $view_builder->build($public_node, 'teaser');
    self::assertArrayHasKey('#node', $build);
    $expected_cache = [
      'tags' => [
        'node:1',
        'node_view',
        'tag_from_twig_tweak_test_node_access',
      ],
      'contexts' => [
        'user',
        'user.permissions',
      ],
      'max-age' => 50,
      'keys' => [
        'entity_view',
        'node',
        '1',
        'teaser',
      ],
      'bin' => 'render',
    ];
    self::assertSame($expected_cache, $build['#cache']);

    $expected_html = <<< 'HTML'
      <article role="article">
        <h2><a href="/node/1" rel="bookmark"><span>Public node</span></a></h2>
        <div>
          <ul class="links inline">
            <li>
              <a href="/node/1" rel="tag" title="Public node" hreflang="en">
                Read more<span class="visually-hidden"> about Public node</span>
              </a>
            </li>
          </ul>
        </div>
      </article>
    HTML;
    $actual_html = $this->renderPlain($build);
    self::assertSame(self::normalizeHtml($expected_html), self::normalizeHtml($actual_html));

    // -- Private node with access check.
    $build = $view_builder->build($private_node);
    self::assertArrayNotHasKey('#node', $build);
    $expected_cache = [
      'contexts' => [
        'user',
        'user.permissions',
      ],
      'tags' => [
        'node:2',
        'tag_from_twig_tweak_test_node_access',
      ],
      'max-age' => 50,
    ];
    self::assertSame($expected_cache, $build['#cache']);

    self::assertSame('', $this->renderPlain($build));

    // -- Private node without access check.
    $build = $view_builder->build($private_node, 'full', NULL, FALSE);
    self::assertArrayHasKey('#node', $build);
    $expected_cache = [
      'tags' => [
        'node:2',
        'node_view',
      ],
      'contexts' => [],
      'max-age' => Cache::PERMANENT,
      'keys' => [
        'entity_view',
        'node',
        '2',
        'full',
      ],
      'bin' => 'render',
    ];
    self::assertSame($expected_cache, $build['#cache']);

    $expected_html = <<< 'HTML'
      <article role="article">
        <h2><a href="/node/2" rel="bookmark"><span>Private node</span></a></h2>
        <div></div>
      </article>
    HTML;
    $actual_html = $this->renderPlain($build);
    self::assertSame(self::normalizeHtml($expected_html), self::normalizeHtml($actual_html));
  }

  /**
   * Renders a render array.
   */
  private function renderPlain(array $build): string {
    $actual_html = $this->container->get('renderer')->renderPlain($build);
    $actual_html = preg_replace('#<footer>.+</footer>#s', '', $actual_html);
    return $actual_html;
  }

  /**
   * Normalizes the provided HTML.
   */
  private static function normalizeHtml(string $html): string {
    return rtrim(preg_replace(['#\s{2,}#', '#\n#'], '', $html));
  }

}
