<?php
namespace Ttree\SwissConfederation\WebStarter\TypoScript;

use Neos\Flow\Annotations as Flow;
use Neos\Neos\TypoScript\DimensionsMenuImplementation;
use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Fusion\Exception as TypoScriptException;

/**
 * TypoScript implementation for a language selection menu.
 */
class LanguageSelectionImplementation extends DimensionsMenuImplementation
{
    /**
     * @param string $pinnedDimensionName
     * @param NodeInterface $nodeInDimensions
     * @param array $targetDimensions
     * @return string
     */
    protected function itemLabel(string $pinnedDimensionName, NodeInterface $nodeInDimensions, array $targetDimensions): string
    {
        if ($pinnedDimensionName === null) {
            return $nodeInDimensions->getLabel();
        }
        $label = $targetDimensions[$pinnedDimensionName]['value'];
        return strtoupper($label);
    }

}
