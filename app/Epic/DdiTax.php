<?php

namespace App\Epic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Futureecom\Foundation\Support\Eloquent\Model;

class DdiTax extends Model
{
    protected $collection = 'ddi_tax'; // Define your MongoDB collection

    protected $fillable = ['z2t_ID', 'ZipCode', 'SalesTaxRate', 'RateState', 'ReportingCodeState', 'RateCounty', 'ReportingCodeCounty', 'RateCity', 'ReportingCodeCity', 'RateSpecialDistrict', 'ReportingCodeSpecialDistrict', 'City', 'PostOffice', 'State', 'County', 'ShippingTaxable', 'PrimaryRecord']; // Fields you're importing
}