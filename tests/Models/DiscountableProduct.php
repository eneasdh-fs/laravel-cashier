<?php
/**
 * Created by enea dhack - 17/06/17 03:01 PM.
 */

namespace Enea\Tests\Models;

use Enea\Cashier\Contracts\DiscountableContract;
use Enea\Cashier\HasAttributes;
use Enea\Cashier\Modifiers\Discounts\Discount;

class DiscountableProduct extends Product implements DiscountableContract
{
    use HasAttributes;

    protected $fillable = ['id', 'price', 'description', 'taxable', 'discount'];

    public $incrementing = false;

    /**
     * {@inheritdoc}
     */
    public function getDiscounts()
    {
        return Discount::generate('example-discount', $this->discount);
    }

    /**
     * Returns an array with extra attributes.
     *
     * @return \Illuminate\Support\Collection
     * */
    public function getAdditionalAttributes()
    {
        return $this->attributes;
    }
}
