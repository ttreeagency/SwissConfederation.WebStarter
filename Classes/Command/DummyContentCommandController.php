<?php
namespace Ttree\SwissConfederation\WebStarter\Command;

/*
 * This file is part of the Ttree.SwissConfederation.WebStarter package.
 *
 * (c) Build by ttree.ch, supported by the community
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\ContentRepository\Domain\Model\NodeTemplate;
use Neos\ContentRepository\Domain\Service\NodeTypeManager;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Cli\CommandController;
use Neos\Neos\Controller\CreateContentContextTrait;

/**
 * Command controller for importing dummy data
 *
 * Data a provided by the LIIP Drupal Starter Kit
 * @see https://github.com/liip/bund_drupal_starterkit_dummycontent
 *
 * @Flow\Scope("singleton")
 */
class DummyContentCommandController extends CommandController
{
    use CreateContentContextTrait;

    /**
     * @var NodeTypeManager
     * @Flow\Inject
     */
    protected $nodeTypeManager;

    /**
     * @var array
     * @Flow\InjectConfiguration(path="dummyContent")
     */
    protected $settings;

    /**
     * Import dummy content for Navigation
     */
    public function navigationCommand()
    {
        $buildTree = function (array &$elements, $parentId = 0) use (&$buildTree) {
            $branch = [];

            foreach ($elements as $key => $element) {
                if ($element['parent'] == $parentId) {
                    $children = $buildTree($elements, $element['identifier']);
                    if ($children !== []) {
                        $element['children'] = $children;
                    }
                    $branch[] = $element;
                    unset($elements[$element['identifier']]);
                }
            }

            return $branch;
        };

        $data = $this->readNavigationData();
        $data = $this->mergeTranslation($data);
        $data = $buildTree($data);
        $this->importTree($data);
    }

    /**
     * Read Dummy Navigation Data
     */
    protected function readNavigationData(): array
    {
        $content = [];
        if (($handle = fopen($this->settings['navigation'], 'r')) !== false) {
            $line = 1;
            while (($data = fgetcsv($handle, 1000, ';')) !== false) {
                $data = array_map('trim', $data);
                if ($line === 1) {
                    $line++;
                    continue;
                }
                if (!isset($content[$data[0]])) {
                    $content[$data[0]] = [];
                }
                $title = null;
                if (isset($data[3]) && $data[3] !== '') {
                    $title = $data[3];
                } elseif (isset($data[4]) && $data[4] !== '') {
                    $title = $data[4];
                } elseif (isset($data[5]) && $data[5] !== '') {
                    $title = $data[5];
                }
                $content[$data[0]][] = [
                    'identifier' => (int)$data[1],
                    'parent' => (int)($data[2] ?: 0),
                    'label' => $title
                ];
                $line++;
            }
            fclose($handle);
        }
        return $content;
    }

    /**
     * Merge translation labels
     * @param array $data
     * @return array
     */
    protected function mergeTranslation(array $data): array
    {
        $processedData = $data['main-navigation-fr'];
        foreach ($data['main-navigation-de'] as $key => $value) {
            $processedData[$key]['label@de'] = $value['label'];
        }
        return $processedData;
    }

    /**
     * @param array $data
     */
    protected function importTree(array $data): void
    {
        $context = $this->createContentContext('live');
        $rootNode = $context->getRootNode();
        $siteNode = $rootNode->getNode('/sites/webstarter');

        $nodeType = $this->nodeTypeManager->getNodeType('Neos.NodeTypes:Page');

        $importNode = function ($data, NodeInterface $parentNode) use ($nodeType, &$importNode) {
            $template = new NodeTemplate();
            $template->setNodeType($nodeType);
            $template->setProperty('title', $data['label']);
            $this->outputLine(sprintf('Create document "%s" bellow "%s"', $data['label'], $parentNode->getLabel()));

            $node = $parentNode->createNodeFromTemplate($template);
            $children = $data['children'] ?? [];
            array_map(function ($data) use ($importNode, $node) {
                $importNode($data, $node);
            }, $children);
        };

        array_map(function ($data) use ($importNode, $siteNode) {
            $importNode($data, $siteNode);
        }, $data);
    }
}
