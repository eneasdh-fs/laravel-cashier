<?php
/**
 * Created by enea dhack - 30/05/2017 02:54 PM.
 */

namespace Enea\Cashier;

use Enea\Cashier\Contracts\CalculatorContract;

/**
 * Class Calculator.
 *
 * @author enea dhack <enea.so@live.com>
 */
class Calculator implements CalculatorContract
{
    /**
     * Default value.
     *
     * @var int
     * */
    const ZERO = 0;

    /**
     * Absolute value.
     *
     * @var int
     * */
    const ABSOLUTE = 100;

    /**
     * Base price for item.
     *
     * @var float
     */
    protected $basePrice;

    /**
     * iIem quantity.
     *
     * @var int
     */
    private $quantity;

    /**
     * Tax price percentage.
     *
     * @var int
     */
    private $impostPercentage;

    /**
     * Discount item percentage.
     *
     * @var int
     */
    private $discountPercentage;

    /**
     * Plan discount Percentage.
     *
     * @var int
     */
    private $planDiscountPercentage;

    /**
     * Total decimals.
     *
     * @var int
     * */
    private $decimals;

    /**
     * Calculator constructor.
     *
     * @param float $basePrice
     * @param int $quantity
     * @param int $impostPercentage
     * @param int $discountPercentage
     * @param int $planDiscountPercentage
     */
    public function __construct(
        $basePrice,
        $quantity,
        $impostPercentage = self::ZERO,
        $discountPercentage = self::ZERO,
        $planDiscountPercentage = self::ZERO
    ) {
        $this->basePrice = $basePrice;
        $this->quantity = $quantity;
        $this->impostPercentage = $impostPercentage;
        $this->discountPercentage = $discountPercentage;
        $this->planDiscountPercentage = $planDiscountPercentage;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            // base
            'base_price' => $this->getBasePrice(),
            'quantity' => $this->getQuantity(),
            'subtotal' => $this->getSubtotal(),

            // plan discounts
            'plan_discount' => $this->getPlanDiscount(),
            'plan_discount_percentage' => $this->getPlanDiscountPercentage(),

            // only discounts
            'discount' => $this->getDiscount(),
            'discount_percentage' => $this->getDiscountPercentage(),

            // total discounts
            'total_discounts' => $this->getTotalDiscounts(),

            // taxes
            'general_sale_tax' => $this->getImpost(),
            'tax_percentage' => $this->getImpostPercentage(),

            // total
            'definitive_total' => $this->getDefinitiveTotal(),
        );
    }

    /**
     * Returns the percentage of formatted igv.
     *
     * @return float
     * */
    protected function getFormatImpostPercentage()
    {
        return $this->toPercentage($this->impostPercentage);
    }

    /**
     * Returns the formatted discount percentage.
     *
     * @return float
     * */
    protected function getFormatDiscountPercentage()
    {
        return $this->toPercentage($this->discountPercentage);
    }

    /**
     * Returns insurance discount percentage formatted.
     *
     * @return float
     * */
    protected function getFormatPlanDiscountPercentage()
    {
        return $this->toPercentage($this->planDiscountPercentage);
    }

    /**
     * Returns the requested quantity for the item.
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Returns the assigned tax.
     *
     * @return int
     */
    public function getImpostPercentage()
    {
        return $this->impostPercentage;
    }

    /**
     * Returns the assigned discount item.
     *
     * @return int
     */
    public function getDiscountPercentage()
    {
        return $this->discountPercentage;
    }

    /**
     * Returns the assigned plan discount item.
     *
     * @return int
     */
    public function getPlanDiscountPercentage()
    {
        return $this->planDiscountPercentage;
    }

    /**
     * Returns the assigned discount item.
     *
     * @param int $percentage
     *
     * @return CalculatorContract
     */
    public function setDiscountPercentage($percentage)
    {
        $this->discountPercentage = $percentage;

        return $this;
    }

    /**
     * Set a tax rate for the item.
     *
     * @param int $percentage
     *
     * @return CalculatorContract
     */
    public function setImpostPercentage($percentage)
    {
        $this->impostPercentage = $percentage;

        return $this;
    }

    /**
     * Set a plan discount for the item.
     *
     * @param int $percentage
     *
     * @return CalculatorContract
     */
    public function setPlanPercentage($percentage)
    {
        $this->planDiscountPercentage = $percentage;

        return $this;
    }

    /**
     * Returns the unit price.
     *
     * @return float
     */
    public function getBasePrice()
    {
        return $this->format($this->basePrice);
    }

    /**
     * Multiply the total by the amount.
     *
     * @return float
     */
    public function getSubtotal()
    {
        return $this->format($this->getBasePrice() * $this->getQuantity());
    }

    /**
     * Returns discount item.
     *
     * @return float
     */
    public function getDiscount()
    {
        return $this->format($this->getSubtotal() * $this->getFormatDiscountPercentage());
    }

    /**
     * Returns plan discount.
     *
     * @return float
     */
    public function getPlanDiscount()
    {
        return $this->format($this->getSubtotal() * $this->getFormatPlanDiscountPercentage());
    }

    /**
     * Total sum of discounts.
     *
     * @return float
     */
    public function getTotalDiscounts()
    {
        return $this->format($this->getDiscount() + $this->getPlanDiscount());
    }

    /**
     * Get general sale sax.
     *
     * @return float
     */
    public function getImpost()
    {
        return $this->format($this->getSubtotal() * $this->getFormatImpostPercentage());
    }

    /**
     * Returns total definitive.
     *
     * @return float
     */
    public function getDefinitiveTotal()
    {
        return $this->format($this->getSubtotal() - $this->getTotalDiscounts() + $this->getImpost());
    }

    /**
     * Converts to decimals.
     *
     * @param int $percentage
     *
     * @return float
     */
    protected function toPercentage($percentage)
    {
        return $percentage / self::ABSOLUTE;
    }

    /**
     * format decimal.
     *
     * @param float $total
     *
     * @return float
     */
    protected function format($total)
    {
        $this->decimals = $this->decimals ?: $this->decimals = config('cashier.decimals', 3);

        return round($total, $this->decimals);
    }
}
