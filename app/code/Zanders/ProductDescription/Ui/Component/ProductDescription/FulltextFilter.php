<?php
/**
 * @category   Zanders
 * @package    Zanders_ProductDescription
 */

declare(strict_types=1);

namespace Zanders\ProductDescription\Ui\Component\ProductDescription;

use Zanders\ProductDescription\Ui\Component\AddFilterInterface;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;

/**
 * Adds fulltext filter for ProductDescription title attribute.
 */
class FulltextFilter implements AddFilterInterface
{
    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @param FilterBuilder $filterBuilder
     */
    public function __construct(FilterBuilder $filterBuilder)
    {
        $this->filterBuilder = $filterBuilder;
    }

    /**
     * @inheritdoc
     */
    public function addFilter(SearchCriteriaBuilder $searchCriteriaBuilder, Filter $filter)
    {
        $titleFilter = $this->filterBuilder->setField('sku')
            ->setValue(sprintf('%%%s%%', $filter->getValue()))
            ->setConditionType('like')
            ->create();

        $searchCriteriaBuilder->addFilter($titleFilter);
    }
}
