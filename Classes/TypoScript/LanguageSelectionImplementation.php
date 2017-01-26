<?php
namespace Ttree\SwissConfederation\WebStarter\TypoScript;

use Neos\Flow\Annotations as Flow;
use TYPO3\Neos\TypoScript\DimensionsMenuImplementation;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;
use TYPO3\TypoScript\Exception as TypoScriptException;

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
