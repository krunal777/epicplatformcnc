<?php

namespace App\Epic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Futureecom\Foundation\Support\Eloquent\Model;

class BulkPriceRule extends Model
{
    protected $collection = 'bulk_price_rules'; // Define your MongoDB collection
    protected $fillable = ['ProductsSKU', 'author', 'authorModifies', 'brands', 'categories', 'effective', 'endDate', 'price', 'priceStatus', 'startDate', 'title'];
}
